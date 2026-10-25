<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\KaheelSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return match ($method) {
            'index' => 'suppliers.view',
            'store' => 'suppliers.create',
            'update' => 'suppliers.edit',
            default => null,
        };
    }

    public function index()
    {
        $rows = KaheelSupplier::query()->orderByDesc('id')->get();

        return mainResponse(true, 'Success', $rows, 200, 'items', '');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'email' => 'required|email|max:255|unique:kaheel_suppliers,email',
            'password' => 'required|string|min:6|max:255',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $supplier = KaheelSupplier::query()->create([
            'name_ar' => $request->string('name_ar')->toString(),
            'name_en' => $request->string('name_en')->toString(),
            'contact_name' => $request->input('contact_name') ?: null,
            'phone' => $request->input('phone') ?: null,
            'commission_rate' => $request->input('commission_rate', 0),
            'email' => $request->string('email')->toString(),
            'password' => $request->input('password'),
            'is_active' => true,
        ]);

        return mainResponse(true, 'Success', $supplier, 200, 'items', '');
    }

    public function update(Request $request)
    {
        $supplier = KaheelSupplier::query()->find($request->input('id'));
        if (!$supplier) {
            $message = app()->getLocale() === 'ar' ? 'المورد غير موجود' : 'Supplier not found';

            return mainResponse(false, $message, null, 404, 'items', '');
        }

        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $supplier->fill([
            'name_ar' => $request->string('name_ar')->toString(),
            'name_en' => $request->string('name_en')->toString(),
            'contact_name' => $request->input('contact_name') ?: null,
            'phone' => $request->input('phone') ?: null,
            'commission_rate' => $request->input('commission_rate', 0),
            'is_active' => $request->boolean('is_active'),
        ]);
        $supplier->save();

        return mainResponse(true, 'Success', $supplier->fresh(), 200, 'items', '');
    }
}
