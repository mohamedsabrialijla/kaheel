<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\KaheelInquiry;
use Illuminate\Http\Request;

class InquiryController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return match ($method) {
            'index' => 'inquiries.view',
            'delete' => 'inquiries.delete',
            default => null,
        };
    }

    public function index()
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'ar';
        $rows = KaheelInquiry::query()
            ->orderByDesc('id')
            ->get()
            ->map(fn (KaheelInquiry $row) => [
                'id' => $row->id,
                'name' => $row->name,
                'phone' => $row->phone,
                'customer_type' => $row->customer_type,
                'customer_type_ar' => KaheelInquiry::typeLabel($row->customer_type, 'ar'),
                'customer_type_en' => KaheelInquiry::typeLabel($row->customer_type, 'en'),
                'customer_type_label' => KaheelInquiry::typeLabel($row->customer_type, $locale),
                'message' => $row->message,
                'created_at' => optional($row->created_at)?->toIso8601String(),
            ]);

        return mainResponse(true, 'Success', $rows, 200, 'items', '');
    }

    public function delete(Request $request)
    {
        $row = KaheelInquiry::query()->find($request->integer('id'));
        if (!$row) {
            return mainResponse(false, kaheel_msg('الرسالة غير موجودة.', 'Inquiry not found.'), null, 404, 'items', '');
        }
        $row->delete();

        return mainResponse(true, 'Success', null, 200, 'items', '');
    }
}
