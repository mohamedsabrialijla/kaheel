<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');







header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');



// Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);





    Route::group(['prefix' => 'auth'], function () {
        
        Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
        Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
        Route::post('/verify', [App\Http\Controllers\API\AuthController::class, 'verify']);
        Route::post('/resendCode', [App\Http\Controllers\API\AuthController::class, 'resendCode']);
        Route::post('/forget', [App\Http\Controllers\API\AuthController::class, 'forget']);
        Route::post('/setupNewPassword', [App\Http\Controllers\API\AuthController::class, 'setupNewPassword']);
        Route::post('/login/facebook', [App\Http\Controllers\API\AuthController::class, 'login_facebook']);
        Route::post('/login/google', [App\Http\Controllers\API\AuthController::class, 'login_google']);
        Route::get('/userInformations', [App\Http\Controllers\API\AuthController::class, 'userInformations']);
        Route::get('/userStatus', [App\Http\Controllers\API\AuthController::class, 'userStatus']);

        // خارج auth:sanctum حتى لو التوكن باطل/ناقص ما يرجع 401 ويعلق التطبيق
        Route::match(['get', 'post'], '/logOut', [App\Http\Controllers\API\AuthController::class, 'logout']);

    });
 
    


    Route::group(['prefix' => 'auth', 'middleware' => ['auth:sanctum']], function () {

       Route::post('/editInfo', [App\Http\Controllers\API\AuthController::class, 'editInfo']);
       Route::post('/updatePassword', [App\Http\Controllers\API\AuthController::class, 'updatePassword']);
       Route::get('/userInfo', [App\Http\Controllers\API\AuthController::class, 'user']);
       Route::post('/storeDeviceToken', [App\Http\Controllers\API\AuthController::class, 'storeDeviceToken']);

       // Yakeen (يقين) — نفس منطق الويب للموبايل
       Route::post('/yakeen/consent', [App\Http\Controllers\User\AuthController::class, 'yakeenConsent']);
       Route::post('/yakeen/verify', [App\Http\Controllers\User\AuthController::class, 'yakeenVerify']);
       Route::get('/yakeen/status', [App\Http\Controllers\User\AuthController::class, 'nafathStatus']);

    });





    Route::group(['prefix' => 'basic'], function () {
        
        Route::get('/getCountries', [App\Http\Controllers\API\BasicController::class, 'getCountries']);
        Route::get('/getCategories', [App\Http\Controllers\API\BasicController::class, 'getCategories']);
        Route::get('/getFilterGenders', [App\Http\Controllers\API\BasicController::class, 'getFilterGenders']);
        Route::get('/getFilterSubcategories', [App\Http\Controllers\API\BasicController::class, 'getFilterSubcategories']);
        Route::get('/getGenders', [App\Http\Controllers\API\BasicController::class, 'getGenders']);
        Route::get('/getSubcategories', [App\Http\Controllers\API\BasicController::class, 'getSubcategories']);
        Route::get('/getSetting', [App\Http\Controllers\API\BasicController::class, 'getSetting']);
        Route::get('/getBanner', [App\Http\Controllers\API\BasicController::class, 'getBanner']);
        Route::get('/getReasons', [App\Http\Controllers\API\BasicController::class, 'getReasons']);
        Route::get('/getPages', [App\Http\Controllers\API\BasicController::class, 'getPages']);
       
      
    });


 
    Route::group(['prefix' => 'basic', 'middleware' => ['auth:sanctum']], function () {
        
        Route::post('/contact', [App\Http\Controllers\API\BasicController::class, 'contact']);
        Route::post('/report', [App\Http\Controllers\API\BasicController::class, 'report']);

        Route::get('/getNotifications', [App\Http\Controllers\API\BasicController::class, 'getNotifications']);
        Route::match(['get', 'post'], '/readNotification', [App\Http\Controllers\API\BasicController::class, 'readNotification']);
        Route::get('/unreadNotificationsCount', [App\Http\Controllers\API\BasicController::class, 'unreadNotificationsCount']);
        Route::match(['get', 'post'], '/testNotification', [App\Http\Controllers\API\NotificationTestController::class, 'test']);
        Route::post('/deleteNotification', [App\Http\Controllers\API\BasicController::class, 'deleteNotification']);
        Route::delete('/deleteNotification', [App\Http\Controllers\API\BasicController::class, 'deleteNotification']);

      
    });

    Route::group(['prefix' => 'chat', 'middleware' => ['auth:sanctum', 'throttle:120,1']], function () {
        Route::post('/notifyNewMessage', [App\Http\Controllers\API\ChatNotificationController::class, 'notifyNewMessage']);
    });




   Route::group(['prefix' => 'ads', 'middleware' => ['auth:sanctum']], function () {

       Route::post('/create', [App\Http\Controllers\API\AdsController::class, 'create']);
       Route::post('/update', [App\Http\Controllers\API\AdsController::class, 'update']);
       Route::post('/refresh', [App\Http\Controllers\API\AdsController::class, 'refresh']);
       Route::post('/rateAd', [App\Http\Controllers\API\AdsController::class, 'rateAd']);
       Route::post('/toggleFavorite', [App\Http\Controllers\API\AdsController::class, 'toggleFavorite']);
       Route::post('/reportAd', [App\Http\Controllers\API\AdsController::class, 'reportAd']);
       Route::get('/favorites', [App\Http\Controllers\API\AdsController::class, 'favorites']);
       Route::post('/blockUser', [App\Http\Controllers\API\AdsController::class, 'blockUser']);
       Route::match(['get', 'post'], '/checkBlocks', [App\Http\Controllers\API\AdsController::class, 'checkBlocks']);
       Route::post('/addComment', [App\Http\Controllers\API\AdsController::class, 'addComment']);
       Route::get('/deleteComment', [App\Http\Controllers\API\AdsController::class, 'deleteComment']);
       Route::post('/addReportComment', [App\Http\Controllers\API\AdsController::class, 'addReportComment']);
       Route::match(['get', 'post'], '/deleteAd', [App\Http\Controllers\API\AdsController::class, 'deleteAd']);
       Route::get('/getAlldeletedAd', [App\Http\Controllers\API\AdsController::class, 'getAlldeletedAd']);
       Route::post('/toggleFollow', [App\Http\Controllers\API\AdsController::class, 'toggleFollow']);
       Route::get('/myFollowing', [App\Http\Controllers\API\AdsController::class, 'myFollowing']);
       Route::get('/myFollowers', [App\Http\Controllers\API\AdsController::class, 'myFollowers']);
       Route::get('/getAllItemsMyAds', [App\Http\Controllers\API\AdsController::class, 'getAllItemsMyAds']);
       Route::get('/getAllItemsMyInvoice', [App\Http\Controllers\API\AdsController::class, 'getAllItemsMyInvoice']);
       Route::post('/rateSeller', [App\Http\Controllers\API\AdsController::class, 'rateSeller']);
       Route::post('/FollowUp', [App\Http\Controllers\API\AdsController::class, 'FollowUp']);




    }); 


    Route::group(['prefix' => 'ads'], function () {

       Route::get('/getAllItems', [App\Http\Controllers\API\AdsController::class, 'getAllItems']);
       Route::get('/getAllItemsBasedUser', [App\Http\Controllers\API\AdsController::class, 'getAllItemsBasedUser']);
       Route::get('/filter', [App\Http\Controllers\API\AdsController::class, 'filter']);
       Route::get('/sortAds', [App\Http\Controllers\API\AdsController::class, 'sortAds']);
       Route::get('/DataSortAds', [App\Http\Controllers\API\AdsController::class, 'DataSortAds']);
       Route::get('/DataFilter', [App\Http\Controllers\API\AdsController::class, 'DataFilter']);
       Route::get('/DataSerach', [App\Http\Controllers\API\AdsController::class, 'DataSerach']);
       Route::get('/getComments', [App\Http\Controllers\API\AdsController::class, 'getComments']);
       Route::post('/CalculateComissionPayment', [App\Http\Controllers\API\AdsController::class, 'CalculateComissionPayment']);
        Route::get('/getAdSimilar', [App\Http\Controllers\API\AdsController::class, 'getAdSimilar']);


    });



    Route::group(['prefix' => 'Payment', 'middleware' => ['auth:sanctum']], function () {
 

        Route::get('/generateUrwayPaymentLink', [App\Http\Controllers\API\PaymentController::class, 'generateUrwayPaymentLink'])->name('Payment.generateUrwayPaymentLink');
        Route::get('/generateUrwayPaymentLinkTrust', [App\Http\Controllers\API\PaymentController::class, 'generateUrwayPaymentLinkTrust'])->name('Payment.generateUrwayPaymentLinkTrust');

        

        Route::get('error', [App\Http\Controllers\API\PaymentController::class, 'handleError'])->name('Payment.error');
        Route::post('webhook', [App\Http\Controllers\API\PaymentController::class, 'webhook'])->name('webhook');
        Route::get('paymentStatus', [App\Http\Controllers\API\PaymentController::class, 'paymentStatus'])->name('paymentStatus');

   });



    Route::group(['prefix' => 'Payment'], function () {

        Route::get('callback', [App\Http\Controllers\API\PaymentController::class, 'handleCallback'])->name('Payment.callback');
    
   });

    // Neoleap – روابط الدفع والـ callback للـ API (بدون auth)
    Route::group(['prefix' => 'neoleap', 'as' => 'api.neoleap.'], function () {
        Route::get('pay', [App\Http\Controllers\API\NeoleapPaymentController::class, 'index'])->name('pay');
        Route::any('callback', [App\Http\Controllers\API\NeoleapPaymentController::class, 'callback'])->name('callback');
    });
    Route::any('neoleap-callback', [App\Http\Controllers\API\NeoleapPaymentController::class, 'callback']);



   