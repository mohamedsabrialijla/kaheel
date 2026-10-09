<?php

namespace App\Http\Controllers\Web\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Language;
use App\Models\Ad;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DB;


class DashboardController extends Controller
{
  
     public function __construct() {
        $this->middleware('auth', ['except' => ['login', 'register']]);

        $this->locales = Language::all();
        view()->share([
            'locales' => $this->locales,

        ]);
    }

    public function image_extensions(){
        return array('jpg','png','jpeg','gif','bmp');
    }

   





    public function getAllItemsPercenatge(Request $request)
    {
        // -------------------------
        // حساب النسب والإحصائيات
        // -------------------------
        $totalPayments = Payment::count();
        $paidPayments = Payment::where('status', 'paid')->count();
        $paidPaymentsPercentage = $totalPayments > 0 ? round(($paidPayments / $totalPayments) * 100, 2) : 0;

        $totalAmount = Payment::sum('amount');
        $paidAmount = Payment::where('status', 'paid')->sum('amount');
        $paidAmountPercentage = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100, 2) : 0;

        $totalUsers = User::count();
        $usersWithAds = User::whereHas('ads')->count();
        $usersWithAdsPercentage = $totalUsers > 0 ? round(($usersWithAds / $totalUsers) * 100, 2) : 0;

        $usersSold = User::whereHas('ads.payments', function($q) {
            $q->where('status', 'paid');
        })->count();
        $usersSoldPercentage = $totalUsers > 0 ? round(($usersSold / $totalUsers) * 100, 2) : 0;

        $totalAds = Ad::count();
        $soldAds = Ad::whereHas('payments', function($q) {
            $q->where('status', 'paid');
        })->count();

        $percentages = [
            'paid_payments_percentage'   => $paidPaymentsPercentage,
            'paid_amount_percentage'     => $paidAmountPercentage,
            'users_with_ads_percentage'  => $usersWithAdsPercentage,
            'users_sold_percentage'      => $usersSoldPercentage,
        ];

        $stats = [
            'total_users'   => $totalUsers,
            'total_ads'     => $totalAds,
            'sold_ads'      => $soldAds,
            'paid_amount'   => $paidAmount,
        ];

        // -------------------------
        // آخر 5 سجلات لكل جدول
        // -------------------------
        $latestUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $latestAds = Ad::with('user','category','brand')->orderBy('created_at', 'desc')->take(5)->get();
        $latestPayments = Payment::with('user', 'ad')->orderBy('created_at', 'desc')->take(5)->get();

        return mainResponse(true, "success return", [
            'percentages' => $percentages,
            'stats'       => $stats,
            'latest'      => [
                'users'    => $latestUsers,
                'ads'      => $latestAds,
                'payments' => $latestPayments,
            ]
        ], 200, 'items', '');
    }

 
public function getChartsData(Request $request)
{
    $period = $request->get('period', 'month'); // افتراضي: شهر
    $now = now();

    switch ($period) {
        case 'week':
            $startDate = $now->copy()->startOfWeek(); // بداية الأسبوع
            $endDate = $now->copy()->endOfWeek();     // نهاية الأسبوع
            $dateFormat = 'Y-m-d';
            $periodLabels = [];
            for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
                $periodLabels[] = $d->format('Y-m-d');
            }
            break;

        case 'month':
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
            $dateFormat = 'Y-m-d';
            $periodLabels = [];
            for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
                $periodLabels[] = $d->format('Y-m-d');
            }
            break;

        case 'year':
            $startDate = $now->copy()->startOfYear();
            $endDate = $now->copy()->endOfYear();
            $dateFormat = 'Y-m';
            $periodLabels = [];
            for ($m = $startDate->copy(); $m <= $endDate; $m->addMonth()) {
                $periodLabels[] = $m->format('Y-m');
            }
            break;

        default:
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
            $dateFormat = 'Y-m-d';
            $periodLabels = [];
            for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
                $periodLabels[] = $d->format('Y-m-d');
            }
            break;
    }


    // ----------- المبيعات -------------
    


     

    if($request->period == 'year'){
        $dateFormat = '%Y-%m';
    }else{
        $dateFormat = '%Y-%m-%d';
    }

    $salesDataRaw = Payment::selectRaw("DATE_FORMAT(created_at, '$dateFormat') as period, COUNT(*) as net_profit, SUM(amount) as revenue")
        ->where('status','paid')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('period')
        ->orderBy('period')
        ->get()
        ->keyBy('period');

        // return $salesDataRaw;

    $netProfitData = [];
    $revenueData = [];
    foreach ($periodLabels as $label) {
        $netProfitData[] = $salesDataRaw[$label]->net_profit ?? 0;
        $revenueData[] = $salesDataRaw[$label]->revenue ?? 0;
    }

    $sales_chart = [
        'categories' => $periodLabels,
        'series' => [
            ['name' => 'Net Profit', 'data' => $netProfitData],
            ['name' => 'Revenue',   'data' => $revenueData],
        ]
    ];

    // ----------- الإعلانات -------------

    $dateFormat2 = 'Y-m';

    $adsDataRaw = Ad::withCount(['payments as sold_ads' => function($q) {
            $q->where('status', 'paid');
        }])
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get()
        ->groupBy(function($ad) use ($dateFormat2) {
            return \Carbon\Carbon::parse($ad->created_at)->format($dateFormat2);
        });

        // return $adsDataRaw;
        // return $endDate;

    $totalAdsData = [];
    $soldAdsData = [];
    foreach ($periodLabels as $label) {
        if(isset($adsDataRaw[$label])){
            $totalAdsData[] = $adsDataRaw[$label]->count();
            $soldAdsData[] = $adsDataRaw[$label]->sum('sold_ads');
        } else {
            $totalAdsData[] = 0;
            $soldAdsData[] = 0;
        }
    }

    $ads_chart = [
        'categories' => $periodLabels,
        'series' => [
            ['name' => 'Total Ads', 'data' => $totalAdsData],
            ['name' => 'Sold Ads',  'data' => $soldAdsData],
        ]
    ];

    return mainResponse(true, "success return", [
        'sales_chart' => $sales_chart,
        'ads_chart'   => $ads_chart
    ], 200, 'charts', '');
}





    

    

}