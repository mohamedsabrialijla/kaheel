<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // عرض لوحة التحكم
    public function index()
    {
        return view('dashboard.index');
    }
}
