<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\KaheelSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return match ($method) {
            'index' => 'settings.view',
            'save' => 'settings.edit',
            default => null,
        };
    }

    public function index()
    {
        return mainResponse(true, 'Success', $this->rows(), 200, 'items', '');
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array|min:1',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
        ]);
        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $numeric = ['delivery_working_days', 'deposit_percent'];
        foreach ($request->input('settings') as $row) {
            $setting = KaheelSetting::query()->where('key', $row['key'])->first();
            if (!$setting) {
                continue;
            }

            $value = $row['value'] ?? '';
            if (in_array($setting->key, $numeric, true)) {
                if (!is_numeric($value)) {
                    return mainResponse(false, kaheel_msg('أدخل رقماً صحيحاً.', 'Invalid number.'), null, 422, 'items', '');
                }
                $number = (float) $value;
                if ($setting->key === 'deposit_percent' && ($number < 0 || $number > 100)) {
                    return mainResponse(false, kaheel_msg('النسبة غير صحيحة.', 'Invalid percent.'), null, 422, 'items', '');
                }
                if ($setting->key === 'delivery_working_days' && $number < 0) {
                    return mainResponse(false, kaheel_msg('عدد الأيام غير صحيح.', 'Invalid days.'), null, 422, 'items', '');
                }
                $value = rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.');
            }

            $setting->value = (string) $value;
            $setting->save();
        }

        return mainResponse(true, 'Success', $this->rows(), 200, 'items', '');
    }

    private function rows()
    {
        return KaheelSetting::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'key', 'label_ar', 'label_en', 'value', 'sort_order']);
    }
}
