<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // لاحظ هنا تمديد الكلاس الأساسي

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests; // يمكنك إضافة أي traits تحتاج إليها هنا
}
