<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\KaheelCategory;
use App\Models\KaheelOrder;
use App\Models\KaheelPackage;
use App\Models\KaheelSupplier;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class OverviewController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return 'overview.view';
    }

    public function stats()
    {
        $orders = KaheelOrder::query()->with('events')->orderBy('id')->get();
        $packages = KaheelPackage::query()->with(['tiers.items'])->orderBy('sort_order')->orderBy('id')->get();
        $packagesByCode = $packages->keyBy('code');
        $suppliers = KaheelSupplier::query()->get()->keyBy('id');
        $categories = collect(KaheelCategory::catalog());

        $booked = $orders->filter(fn (KaheelOrder $order) => $order->payment_status === 'approved');
        $closed = $orders->filter(fn (KaheelOrder $order) => $order->listedStatus() === 'closed');
        $active = $booked->reject(fn (KaheelOrder $order) => $order->listedStatus() === 'closed');

        $collected = 0.0;
        $revenue = 0.0;
        $cost = 0.0;
        $commission = 0.0;
        $purchaseCount = 0;
        $mix = [];
        $supplierStats = [];
        $packageStats = [];
        $assignmentDays = [];
        $lateOrders = 0;
        $ready = $orders->filter(fn (KaheelOrder $order) => in_array($order->listedStatus(), ['supplied', 'closed'], true))->count();

        foreach ($packages as $package) {
            $packageStats[$package->code] = [
                'id' => $package->id,
                'code' => $package->code,
                'name_ar' => $package->name_ar,
                'name_en' => $package->name_en,
                'orders' => 0,
                'sales' => 0.0,
                'cost' => 0.0,
                'tiers' => $package->tiers->map(fn ($tier) => [
                    'name_ar' => $tier->name_ar,
                    'name_en' => $tier->name_en,
                    'price_incl_vat' => (float) $tier->price_incl_vat,
                    'supplier_cost' => (float) $tier->supplier_cost,
                ])->values(),
            ];
        }

        foreach ($booked as $order) {
            $amount = $this->amount($order);
            $orderCost = $this->orderCost($order, $packagesByCode);
            $deposit = (float) $order->deposit_amount;
            $finalPaid = $order->final_payment_status === 'approved';
            $collected += $finalPaid ? $amount : $deposit;
            $revenue += $amount;
            $cost += $orderCost;

            $code = (string) $order->package_code;
            if (!isset($packageStats[$code])) {
                $packageStats[$code] = [
                    'id' => 0,
                    'code' => $code,
                    'name_ar' => $order->package_name_ar,
                    'name_en' => $order->package_name_en,
                    'orders' => 0,
                    'sales' => 0.0,
                    'cost' => 0.0,
                    'tiers' => [],
                ];
            }
            $packageStats[$code]['orders']++;
            $packageStats[$code]['sales'] += $amount;
            $packageStats[$code]['cost'] += $orderCost;

            $this->addMix($mix, $order, $packagesByCode, $amount);

            $seen = [];
            foreach ($this->assignments($order) as $row) {
                $purchaseCount++;
                $sid = (int) ($row['supplier_id'] ?? 0);
                $rowCost = (float) ($row['cost'] ?? 0);
                $rate = (float) optional($suppliers->get($sid))->commission_rate;
                $commission += $rowCost * ($rate / 100);
                if (!isset($supplierStats[$sid])) {
                    $supplier = $suppliers->get($sid);
                    $supplierStats[$sid] = [
                        'id' => $sid,
                        'name_ar' => $supplier->name_ar ?? ($row['supplier_name_ar'] ?? '—'),
                        'name_en' => $supplier->name_en ?? ($row['supplier_name_en'] ?? '—'),
                        'orders' => 0,
                        'sales' => 0.0,
                        'cost' => 0.0,
                        'rate' => $rate,
                        'commission' => 0.0,
                        'on_time' => 0,
                        'dated' => 0,
                        'late' => 0,
                        'deviation' => [],
                    ];
                }
                if (!isset($seen[$sid])) {
                    $supplierStats[$sid]['orders']++;
                    $supplierStats[$sid]['sales'] += $amount;
                    $seen[$sid] = true;
                }
                $supplierStats[$sid]['cost'] += $rowCost;
                $supplierStats[$sid]['commission'] += $rowCost * ($rate / 100);

                $expected = $this->dateOrNull($row['expected_date'] ?? null);
                $delivered = $this->dateOrNull($row['delivered_at'] ?? null);
                if ($expected && $delivered) {
                    $diff = $this->daySpan($expected, $delivered, true);
                    $supplierStats[$sid]['dated']++;
                    $supplierStats[$sid]['deviation'][] = $diff;
                    $assignmentDays[] = $diff;
                    if ($diff <= 0) {
                        $supplierStats[$sid]['on_time']++;
                    } else {
                        $supplierStats[$sid]['late']++;
                    }
                } elseif ($expected && !$delivered && $expected->isPast()) {
                    $supplierStats[$sid]['dated']++;
                    $supplierStats[$sid]['late']++;
                    $supplierStats[$sid]['deviation'][] = $this->daySpan($expected, now(), true);
                }
            }

            if ($this->orderIsLate($order)) {
                $lateOrders++;
            }
        }

        $stageCodes = array_merge(['deposit'], KaheelOrder::workflow());
        $stageCopy = KaheelOrder::stageCopy();
        $stageCounts = array_fill_keys($stageCodes, 0);
        foreach ($orders as $order) {
            if ($order->progressStep() === 0) {
                $stageCounts['deposit']++;
            } else {
                $code = $order->listedStatus();
                if (isset($stageCounts[$code])) {
                    $stageCounts[$code]++;
                }
            }
        }
        $stages = [];
        foreach ($stageCodes as $index => $code) {
            $copy = $code === 'deposit'
                ? ['ar' => 'بانتظار العربون', 'en' => 'Awaiting deposit']
                : ($stageCopy[$index - 1] ?? ['ar' => $code, 'en' => $code]);
            $stages[] = [
                'code' => $code,
                'ar' => $copy['ar'],
                'en' => $copy['en'],
                'count' => (int) $stageCounts[$code],
            ];
        }

        $monthKeys = [];
        $cursor = now()->startOfMonth()->subMonths(5);
        for ($i = 0; $i < 6; $i++) {
            $monthKeys[] = $cursor->format('Y-m');
            $cursor = $cursor->copy()->addMonth();
        }
        $monthly = [];
        foreach ($monthKeys as $key) {
            $monthly[$key] = ['key' => $key, 'sales' => 0.0, 'cost' => 0.0, 'profit' => 0.0];
        }
        foreach ($booked as $order) {
            $key = optional($order->created_at)->format('Y-m');
            if (!isset($monthly[$key])) {
                continue;
            }
            $amount = $this->amount($order);
            $orderCost = $this->orderCost($order, $packagesByCode);
            $monthly[$key]['sales'] += $amount;
            $monthly[$key]['cost'] += $orderCost;
            $monthly[$key]['profit'] += $amount - $orderCost;
        }

        $colors = ['#C7783E', '#8F6A4B', '#5C6B63', '#9A5B3F', '#6D7C88', '#B99A6B', '#4F6F52', '#A35D6A'];
        $mixRows = [];
        $colorIndex = 0;
        foreach ($categories as $category) {
            $value = (float) ($mix[$category['code']] ?? 0);
            $mixRows[] = [
                'code' => $category['code'],
                'ar' => $category['ar'],
                'en' => $category['en'],
                'value' => round($value, 2),
                'color' => $colors[$colorIndex % count($colors)],
            ];
            $colorIndex++;
        }
        foreach ($mix as $code => $value) {
            if ($categories->contains('code', $code)) {
                continue;
            }
            $mixRows[] = [
                'code' => $code,
                'ar' => $code,
                'en' => $code,
                'value' => round((float) $value, 2),
                'color' => $colors[$colorIndex % count($colors)],
            ];
            $colorIndex++;
        }

        $dated = collect($supplierStats)->sum('dated');
        $onTime = collect($supplierStats)->sum('on_time');
        $onTimePct = $dated > 0 ? round(($onTime / $dated) * 100, 1) : null;
        $avgDeviation = $assignmentDays ? round(array_sum($assignmentDays) / count($assignmentDays), 1) : null;

        $designDays = $this->avgSpan($orders, ['design_preparation'], ['design_review', 'final_payment']);
        $adminDays = $this->avgSpan($orders, [null, 'awaiting_approval', 'admin_review'], ['design_preparation', 'design_review']);
        $supplyStartDays = $this->avgSpan($orders, ['final_approval'], ['in_supply']);
        $supplyDays = $this->avgSpan($orders, ['in_supply'], ['supplied', 'closed']);
        $closeDays = $this->avgCreatedTo($closed);

        $profit = $revenue - $cost;
        $marginSales = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : null;
        $marginCost = $cost > 0 ? round(($profit / $cost) * 100, 1) : null;
        $avgOrder = $booked->count() ? round($revenue / $booked->count(), 2) : 0;

        $suppliersOut = collect($supplierStats)->map(function ($row) {
            $dated = (int) $row['dated'];
            $dev = $row['deviation'] ? round(array_sum($row['deviation']) / count($row['deviation']), 1) : null;

            return [
                'id' => $row['id'],
                'name_ar' => $row['name_ar'],
                'name_en' => $row['name_en'],
                'orders' => $row['orders'],
                'sales' => round($row['sales'], 2),
                'cost' => round($row['cost'], 2),
                'rate' => round($row['rate'], 2),
                'commission' => round($row['commission'], 2),
                'on_time' => $dated ? round(($row['on_time'] / $dated) * 100, 1) : null,
                'deviation' => $dev,
                'late' => $row['late'],
            ];
        })->sortByDesc('sales')->values();

        return mainResponse(true, 'Success', [
            'kpis' => [
                'collected' => round($collected, 2),
                'cost' => round($cost, 2),
                'profit' => round($profit, 2),
                'margin_sales' => $marginSales,
                'margin_cost' => $marginCost,
                'commission' => round($commission, 2),
                'purchase_orders' => $purchaseCount,
                'orders' => $orders->count(),
                'active' => $active->count(),
                'closed' => $closed->count(),
                'customers' => $orders->pluck('bricks_user_id')->filter()->unique()->count(),
                'avg_order' => $avgOrder,
                'on_time' => $onTimePct,
                'deviation' => $avgDeviation,
                'late' => $lateOrders,
                'ready' => $ready,
            ],
            'months' => array_values($monthly),
            'mix' => $mixRows,
            'stages' => $stages,
            'sla' => [
                ['key' => 'on_time', 'value' => $onTimePct, 'unit' => 'pct', 'target' => 95],
                ['key' => 'design', 'value' => $designDays, 'unit' => 'days', 'target' => 3],
                ['key' => 'admin', 'value' => $adminDays, 'unit' => 'days', 'target' => 2],
                ['key' => 'supply_start', 'value' => $supplyStartDays, 'unit' => 'days', 'target' => 2],
                ['key' => 'supply', 'value' => $supplyDays, 'unit' => 'days', 'target' => 18],
                ['key' => 'close', 'value' => $closeDays, 'unit' => 'days', 'target' => 25],
            ],
            'suppliers' => $suppliersOut,
            'packages' => array_values($packageStats),
        ], 200, 'items', '');
    }

    private function amount(KaheelOrder $order): float
    {
        return round((float) $order->price_incl + (float) $order->addons_total, 2);
    }

    private function assignments(KaheelOrder $order): array
    {
        return is_array($order->supplier_assignments) ? $order->supplier_assignments : [];
    }

    private function orderCost(KaheelOrder $order, $packagesByCode): float
    {
        $assigned = collect($this->assignments($order))->sum(fn ($row) => (float) ($row['cost'] ?? 0));
        if ($assigned > 0) {
            return round($assigned, 2);
        }
        $tier = $this->matchTier($order, $packagesByCode);

        return $tier ? round((float) $tier->supplier_cost, 2) : 0.0;
    }

    private function matchTier(KaheelOrder $order, $packagesByCode)
    {
        $package = $packagesByCode->get($order->package_code);
        if (!$package) {
            return null;
        }
        $ar = trim((string) $order->tier_name_ar);
        $en = trim((string) $order->tier_name_en);

        return $package->tiers->first(function ($tier) use ($ar, $en) {
            return ($ar !== '' && $tier->name_ar === $ar) || ($en !== '' && $tier->name_en === $en);
        }) ?: $package->tiers->first();
    }

    private function addMix(array &$mix, KaheelOrder $order, $packagesByCode, float $amount): void
    {
        $tier = $this->matchTier($order, $packagesByCode);
        $items = $tier ? $tier->items : collect();
        if ($items->isEmpty()) {
            $package = $packagesByCode->get($order->package_code);
            $items = $package ? $package->tiers->flatMap->items : collect();
        }
        if ($items->isEmpty()) {
            return;
        }
        $weights = $items->map(function ($item) {
            $price = (float) $item->price_per_unit * (float) ($item->quantity ?: 1);

            return $price > 0 ? $price : 1;
        });
        $total = (float) $weights->sum();
        if ($total <= 0) {
            return;
        }
        foreach ($items as $index => $item) {
            $code = (string) ($item->category ?: 'other');
            $share = ((float) $weights[$index] / $total) * $amount;
            $mix[$code] = ($mix[$code] ?? 0) + $share;
        }
    }

    private function orderIsLate(KaheelOrder $order): bool
    {
        foreach ($this->assignments($order) as $row) {
            $expected = $this->dateOrNull($row['expected_date'] ?? null);
            $delivered = $this->dateOrNull($row['delivered_at'] ?? null);
            if ($expected && $delivered && $delivered->gt($expected)) {
                return true;
            }
            if ($expected && !$delivered && $expected->isPast() && !in_array($order->listedStatus(), ['closed'], true)) {
                return true;
            }
        }

        return false;
    }

    private function avgSpan($orders, array $fromStatuses, array $toStatuses): ?float
    {
        $days = [];
        foreach ($orders as $order) {
            $events = $order->events->sortBy('id')->values();
            $start = null;
            foreach ($events as $event) {
                $to = (string) $event->to_status;
                $from = (string) $event->from_status;
                if ($start === null && (in_array($to, $fromStatuses, true) || in_array($from, $fromStatuses, true))) {
                    $start = $event->created_at;
                }
                if ($start && in_array($to, $toStatuses, true) && $event->created_at->gte($start)) {
                    $days[] = $this->daySpan($start, $event->created_at);
                    break;
                }
            }
        }

        return $days ? round(array_sum($days) / count($days), 1) : null;
    }

    private function avgCreatedTo($orders): ?float
    {
        $days = [];
        foreach ($orders as $order) {
            $end = $order->events->sortByDesc('id')->first(fn ($event) => $event->to_status === 'closed');
            $start = $order->created_at;
            $finish = $end?->created_at ?: $order->updated_at;
            if ($start && $finish && $finish->gte($start)) {
                $days[] = $this->daySpan($start, $finish);
            }
        }

        return $days ? round(array_sum($days) / count($days), 1) : null;
    }

    private function daySpan(CarbonInterface $from, CarbonInterface $to, bool $signed = false): float
    {
        $days = $from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay(), false);
        $days = (float) $days;

        return $signed ? $days : abs($days);
    }

    private function dateOrNull($value): ?CarbonInterface
    {
        if (!$value) {
            return null;
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
