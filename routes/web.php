<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\NeoleapPaymentController;
use Illuminate\Http\Request;

// يقين — تشخيص (APP_DEBUG أو YAKEEN_DEBUG_ENABLED فقط)
Route::post('/yakeen/debug', [App\Http\Controllers\YakeenDebugController::class, 'probe']);
Route::get('/yakeen/debug', [App\Http\Controllers\YakeenDebugController::class, 'probe']);

// Neoleap Payment (بدون locale لثبات رابط الـ callback)
Route::group(['prefix' => 'neoleap', 'as' => 'neoleap.'], function () {
    Route::get('pay', [NeoleapPaymentController::class, 'index'])->name('pay');
    // Neoleap يرجع POST بدون CSRF token
    Route::any('callback', [NeoleapPaymentController::class, 'callback'])
        ->name('callback')
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
});
Route::any('neoleap-callback', [NeoleapPaymentController::class, 'callback'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);






Route::get('/test-image', function () {
    $img = Image::canvas(200, 200, '#ff0000'); // يرسم صورة حمراء 200x200
    return $img->response('png');
});



// use Illuminate\Support\Facades\Mail;
// use App\Mail\WelcomeUserMail;

// Route::get('/test-mail', function () {
//     $user = [
//         'name' => 'Mohamed',
//         'email' => 'mohamed@example.com', // الإيميل هنا لا يهم، Mailtrap يلتقطه
//     ];

//     Mail::to($user['email'])->send(new WelcomeUserMail($user));

//     return '📧 Email sent (check Mailtrap)';
// });







Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Web\AuthController::class, 'verify'])
    ->middleware(['signed']) 
    ->name('verification.verify');

// نفاذ: استدعاء من سيرفر نفاذ فقط (POST + JWT) — بدون locale
Route::post('/nafath/post-callback', [App\Http\Controllers\User\AuthController::class, 'nafathPostCallback']);






Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath'
    ]
], function () {






Route::get('/test-signed', function () {
    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => 43, 'hash' => sha1('mohamed.alijla@gmail.com')]
    );

    return '<a href="'.$url.'">Test Verify Link</a><br>' . $url;
});






    Route::get('/dashboard/{pathMatch?}', function () {
        return view('basic.dashboard'); // للوحة الإدارة
    })->where('pathMatch', '.*');

    // Route::get('/app/{pathMatch?}', function () {
    //     return view('basic.dashboard_user'); // لوحة المستخدم
    // })->where('pathMatch', '.*');

    Route::get('/', [App\Http\Controllers\Kaheel\SiteController::class, 'home']);

    Route::get('/auth/csrf', [App\Http\Controllers\Kaheel\AuthController::class, 'csrf']);
    Route::get('/auth', [App\Http\Controllers\Kaheel\AuthController::class, 'show'])->defaults('screen', 'login');
    Route::get('/auth/code', [App\Http\Controllers\Kaheel\AuthController::class, 'show'])->defaults('screen', 'code');
    Route::get('/auth/forgot', [App\Http\Controllers\Kaheel\AuthController::class, 'show'])->defaults('screen', 'forgot');
    Route::get('/auth/reset', [App\Http\Controllers\Kaheel\AuthController::class, 'show'])->defaults('screen', 'reset');

    Route::post('/auth/session', [App\Http\Controllers\Kaheel\AuthController::class, 'login'])->middleware('throttle:8,1');
    Route::post('/auth/code', [App\Http\Controllers\Kaheel\AuthController::class, 'verifyCode'])->middleware('throttle:8,1');
    Route::post('/auth/signup', [App\Http\Controllers\Kaheel\AuthController::class, 'signup']);
    Route::post('/auth/sms', [App\Http\Controllers\Kaheel\AuthController::class, 'sendSmsCode'])->middleware('throttle:8,1');
    Route::post('/auth/sms/verify', [App\Http\Controllers\Kaheel\AuthController::class, 'verifySmsLogin'])->middleware('throttle:8,1');
    Route::post('/auth/password/forgot', [App\Http\Controllers\Kaheel\AuthController::class, 'startReset'])->middleware('throttle:8,1');
    Route::post('/auth/password/reset', [App\Http\Controllers\Kaheel\AuthController::class, 'resetPassword'])->middleware('throttle:8,1');
    Route::post('/auth/logout', [App\Http\Controllers\Kaheel\AuthController::class, 'logout']);
    Route::get('/auth/google/callback', [App\Http\Controllers\Kaheel\AuthController::class, 'googleCallback']);

    Route::get('/account', function () {
        $customer = kaheel_customer();
        if (!$customer && !auth('admin')->check()) {
            return redirect(url(app()->getLocale().'/auth'));
        }

        return view('kaheel.account', ['customer' => $customer]);
    });

    Route::get('/admin/login', function () {
        return view('kaheel.admin-login');
    });

    Route::get('/packages/gallery/{image}', [App\Http\Controllers\Kaheel\SiteController::class, 'galleryImage']);
    Route::get('/packages/{code}', [App\Http\Controllers\Kaheel\SiteController::class, 'package']);

    Route::get('/order', [App\Http\Controllers\Kaheel\OrderController::class, 'create']);
    Route::post('/order', [App\Http\Controllers\Kaheel\OrderController::class, 'store']);
    Route::get('/orders', [App\Http\Controllers\Kaheel\OrderController::class, 'index']);
    Route::get('/orders/notices', [App\Http\Controllers\Kaheel\OrderController::class, 'notices']);
    Route::get('/orders/{order}/messages', [App\Http\Controllers\Kaheel\OrderController::class, 'messages']);
    Route::post('/orders/{order}/messages', [App\Http\Controllers\Kaheel\OrderController::class, 'storeMessage']);
    Route::post('/orders/{order}/messages/read', [App\Http\Controllers\Kaheel\OrderController::class, 'readMessages']);
    Route::get('/orders/{order}/messages/{message}/media', [App\Http\Controllers\Kaheel\OrderController::class, 'messageMedia']);
    Route::get('/orders/{order}/files/{file}', [App\Http\Controllers\Kaheel\OrderController::class, 'file']);
    Route::get('/orders/{order}/pay', [App\Http\Controllers\Kaheel\OrderController::class, 'pay']);
    Route::post('/orders/{order}/pay', [App\Http\Controllers\Kaheel\OrderController::class, 'storePayment']);
    Route::post('/orders/{order}/design', [App\Http\Controllers\Kaheel\OrderController::class, 'confirmDesign']);
    Route::post('/orders/{order}/design/selections', [App\Http\Controllers\Kaheel\OrderController::class, 'saveDesignSelections']);
    Route::post('/orders/{order}/final', [App\Http\Controllers\Kaheel\OrderController::class, 'submitFinalPayment']);
    Route::get('/orders/{order}', [App\Http\Controllers\Kaheel\OrderController::class, 'show']);

    Route::get('/{pathMatch}', function () {
        return view('basic.landing');
    })->where('pathMatch', '^(?!dashboard|web|user|api).*');




// المجموعة المحمية بـ Authentication
Route::middleware(['auth:sanctum'])->group(function () {
    
    // 1. جلب قائمة المحادثات للمستخدم الحالي
    Route::get('/chat/conversations', [App\Http\Controllers\User\ChatController::class, 'getConversations']);
    
    // 2. جلب رسائل محادثة معينة
    Route::get('/chat/messages/{chatId}', [App\Http\Controllers\User\ChatController::class, 'getMessages']);
    
    // 3. إرسال رسالة نصية
    Route::post('/chat/send-message', [App\Http\Controllers\User\ChatController::class, 'sendMessage']);
    
    // 4. رفع وإرسال صورة
    Route::post('/chat/send-image', [App\Http\Controllers\User\ChatController::class, 'sendImage']);
    
    // 5. رفع وإرسال تسجيل صوتي
    Route::post('/chat/send-audio', [App\Http\Controllers\User\ChatController::class, 'sendAudio']);
    
    // 6. تحديث حالة القراءة (mark as read)
    Route::post('/chat/mark-read/{chatId}', [App\Http\Controllers\User\ChatController::class, 'markAsRead']);
    
    // 7. إنشاء محادثة جديدة
    Route::post('/chat/create-conversation', [App\Http\Controllers\User\ChatController::class, 'createConversation']);
    
    // 8. حذف رسالة
    Route::delete('/chat/delete-message/{messageId}', [App\Http\Controllers\User\ChatController::class, 'deleteMessage']);
    
    // 9. البحث في المحادثات
    Route::get('/chat/search', [App\Http\Controllers\User\ChatController::class, 'searchConversations']);
    
    // 10. جلب معلومات المستخدم للشات
    Route::get('/chat/user-info/{userId}', [App\Http\Controllers\User\ChatController::class, 'getUserInfo']);
    
    // 11. جلب عدد الرسائل غير المقروءة
    Route::get('/chat/unread-count', [App\Http\Controllers\User\ChatController::class, 'getUnreadCount']);
});

   



  

    Route::group(['prefix' => 'user'], function () {

        // Lightweight auth ping only — do not serialize User (appends/$with can 500).
        Route::middleware('auth:sanctum')->get('/check-auth', function (Request $request) {
            return response()->json([
                'authenticated' => true,
                'user_id' => $request->user()?->id,
            ]);
        });

       

        Route::group(['prefix' => 'Settings'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\Setting\SettingController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\Setting\SettingController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\Setting\SettingController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\Setting\SettingController::class,'edit']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\Setting\SettingController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\Setting\SettingController::class,'deleteAllItem']);
            
        });


         Route::group(['prefix' => 'Languages'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\LanguageController::class,'getAllItems']);
            Route::get('change', [App\Http\Controllers\Web\LanguageController::class,'changeItem']);
            Route::get('translations', [App\Http\Controllers\Web\LanguageController::class,'translations']);
          
        
        });


        Route::group(['prefix' => 'Pages'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\Setting\PageController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\Setting\PageController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\Setting\PageController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\Setting\PageController::class,'edit']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\Setting\PageController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\Setting\PageController::class,'deleteAllItem']);
        
        });



            Route::group(['prefix' => 'auth'], function () {
            
                Route::post('/login', [App\Http\Controllers\User\AuthController::class, 'login']);
                Route::post('/register', [App\Http\Controllers\User\AuthController::class, 'register']);
                Route::post('/verify', [App\Http\Controllers\User\AuthController::class, 'verify']);
                Route::post('/resendCode', [App\Http\Controllers\User\AuthController::class, 'resendCode']);
                Route::post('/forget', [App\Http\Controllers\User\AuthController::class, 'forget']);
                Route::post('/setupNewPassword', [App\Http\Controllers\User\AuthController::class, 'setupNewPassword']);
                // Route::post('/login/facebook', [App\Http\Controllers\User\AuthController::class, 'login_facebook']);
                // Route::post('/login/google', [App\Http\Controllers\User\AuthController::class, 'login_google']);
                Route::get('/userInformations', [App\Http\Controllers\User\AuthController::class, 'userInformations']);
                
                // Nafath (نفس منطق package semati): طلب تحقق + فحص حالة + حفظ
                Route::post('/nafath/request', [App\Http\Controllers\User\AuthController::class, 'nafathRequest'])->middleware('auth:sanctum');
                Route::get('/nafath/check/{transId}', [App\Http\Controllers\User\AuthController::class, 'nafathCheck'])->middleware('auth:sanctum');
                Route::get('/nafath/status', [App\Http\Controllers\User\AuthController::class, 'nafathStatus'])->middleware('auth:sanctum');
                Route::get('/nafath/callback', [App\Http\Controllers\User\AuthController::class, 'nafathCallbackPage']);
                Route::post('/nafath/save', [App\Http\Controllers\User\AuthController::class, 'nafathSave'])->middleware('auth:sanctum');

                // Yakeen (يقين): Step 1 consent → Step 2 OTP verify
                Route::post('/yakeen/consent', [App\Http\Controllers\User\AuthController::class, 'yakeenConsent'])->middleware('auth:sanctum');
                Route::post('/yakeen/verify',  [App\Http\Controllers\User\AuthController::class, 'yakeenVerify'])->middleware('auth:sanctum');
            });
 
     


            Route::group(['prefix' => 'auth', 'middleware' => ['auth:sanctum']], function () {

               Route::post('/editInfo', [App\Http\Controllers\User\AuthController::class, 'editInfo']);
               Route::post('/updatePassword', [App\Http\Controllers\User\AuthController::class, 'updatePassword']);
               Route::get('/userInfo', [App\Http\Controllers\User\AuthController::class, 'user']);
               Route::post('/logOut', [App\Http\Controllers\User\AuthController::class, 'logout']);
               Route::post('/storeDeviceToken', [App\Http\Controllers\User\AuthController::class, 'storeDeviceToken']);

            });



 

            Route::group(['prefix' => 'basic'], function () {
                
                Route::get('/getCountries', [App\Http\Controllers\User\BasicController::class, 'getCountries']);
                Route::get('/getCategories', [App\Http\Controllers\User\BasicController::class, 'getCategories']);
                Route::get('/getFilterGenders', [App\Http\Controllers\User\BasicController::class, 'getFilterGenders']);
                Route::get('/getFilterSubcategories', [App\Http\Controllers\User\BasicController::class, 'getFilterSubcategories']);
                Route::get('/getGenders', [App\Http\Controllers\User\BasicController::class, 'getGenders']);
                Route::get('/getSubcategories', [App\Http\Controllers\User\BasicController::class, 'getSubcategories']);
                Route::get('/getSetting', [App\Http\Controllers\User\BasicController::class, 'getSetting']);
                Route::get('/getBanner', [App\Http\Controllers\User\BasicController::class, 'getBanner']);
                Route::get('/getReasons', [App\Http\Controllers\User\BasicController::class, 'getReasons']);
               
              
            });



            Route::group(['prefix' => 'chat', 'middleware' => ['auth:sanctum', 'throttle:120,1']], function () {
                Route::post('/notifyNewMessage', [App\Http\Controllers\API\ChatNotificationController::class, 'notifyNewMessage']);
            });

            Route::group(['prefix' => 'basic', 'middleware' => ['auth:sanctum']], function () {
                
                Route::post('/contact', [App\Http\Controllers\User\BasicController::class, 'contact']);
                Route::post('/report', [App\Http\Controllers\User\BasicController::class, 'report']);
                Route::get('/getNotifications', [App\Http\Controllers\User\BasicController::class, 'getNotifications']);
                Route::get('/readNotification', [App\Http\Controllers\User\BasicController::class, 'readNotification']);
                Route::get('/unreadNotificationsCount', [App\Http\Controllers\User\BasicController::class, 'unreadNotificationsCount']);
                Route::post('/deleteNotification', [App\Http\Controllers\User\BasicController::class, 'deleteNotification']);
                Route::delete('/deleteNotification', [App\Http\Controllers\User\BasicController::class, 'deleteNotification']);

              
            });




            Route::group(['prefix' => 'ads', 'middleware' => ['auth:sanctum']], function () {

               Route::post('/create', [App\Http\Controllers\User\AdsController::class, 'create']);
               Route::post('/trust', [App\Http\Controllers\User\AdsController::class, 'trust']);
               Route::post('/update', [App\Http\Controllers\User\AdsController::class, 'update']);
               Route::post('/refresh', [App\Http\Controllers\User\AdsController::class, 'refresh']);
               Route::post('/rateSeller', [App\Http\Controllers\User\AdsController::class, 'rateSeller']);
               Route::post('/toggleFavorite', [App\Http\Controllers\User\AdsController::class, 'toggleFavorite']);
               Route::post('/reportAd', [App\Http\Controllers\User\AdsController::class, 'reportAd']);
               Route::get('/favorites', [App\Http\Controllers\User\AdsController::class, 'favorites']);
               Route::post('/blockUser', [App\Http\Controllers\User\AdsController::class, 'blockUser']);
               Route::post('/addComment', [App\Http\Controllers\User\AdsController::class, 'addComment']);
               // Route::get('/getComments', [App\Http\Controllers\User\AdsController::class, 'getComments']);
               Route::get('/deleteComment', [App\Http\Controllers\User\AdsController::class, 'deleteComment']);
               Route::post('/addReportComment', [App\Http\Controllers\User\AdsController::class, 'addReportComment']);
               Route::match(['get', 'post'], '/deleteAd', [App\Http\Controllers\User\AdsController::class, 'deleteAd']);
               Route::get('/getAlldeletedAd', [App\Http\Controllers\User\AdsController::class, 'getAlldeletedAd']);
               Route::post('/toggleFollow', [App\Http\Controllers\User\AdsController::class, 'toggleFollow']);
               Route::get('/myFollowing', [App\Http\Controllers\User\AdsController::class, 'myFollowing']);
               Route::get('/myFollowers', [App\Http\Controllers\User\AdsController::class, 'myFollowers']);
               Route::get('/getAllItemsMyAds', [App\Http\Controllers\User\AdsController::class, 'getAllItemsMyAds']);

               Route::get('/getAllItemsMyAdsWithTrash', [App\Http\Controllers\User\AdsController::class, 'getAllItemsMyAdsWithTrash']);

                Route::get('/deleteImg', [App\Http\Controllers\User\AdsController::class, 'deleteImg']);
                Route::get('/deleteInvoice', [App\Http\Controllers\User\AdsController::class, 'deleteInvoice']);



            });


           Route::group(['prefix' => 'ads'], function () {

               Route::get('/getAllItems', [App\Http\Controllers\User\AdsController::class, 'getAllItems']);
               Route::get('/getAllItemsSellerAds', [App\Http\Controllers\User\AdsController::class, 'getAllItemsSellerAds']);
               
               Route::get('/filter', [App\Http\Controllers\User\AdsController::class, 'filter']);
               Route::get('/sortAds', [App\Http\Controllers\User\AdsController::class, 'sortAds']);

               Route::get('/getComments', [App\Http\Controllers\User\AdsController::class, 'getComments']);
               Route::get('/getAdSimilar', [App\Http\Controllers\User\AdsController::class, 'getAdSimilar']);
               Route::get('Calculate', [App\Http\Controllers\User\AdsController::class,'Calculate']);
               Route::get('/DataSerach', [App\Http\Controllers\API\AdsController::class, 'DataSerach']);



            });

 

            Route::group(['prefix' => 'Payment'], function () {

                Route::middleware(['auth:sanctum'])->group(function () {
                    Route::get('/generateUrwayPaymentLink', [App\Http\Controllers\User\PaymentController::class, 'generateUrwayPaymentLink'])->name('Payment.generateUrwayPaymentLink');
                    Route::get('/generateUrwayPaymentLinkTrust', [App\Http\Controllers\User\PaymentController::class, 'generateUrwayPaymentLinkTrust'])->name('Payment.generateUrwayPaymentLinkTrust');
                    Route::get('getAllItems', [App\Http\Controllers\User\PaymentController::class, 'getAllItems'])->name('Payment.getAllItems');
                });

                Route::get('callback', [App\Http\Controllers\User\PaymentController::class, 'handleCallback'])->name('Payment.callback');
                Route::get('error', [App\Http\Controllers\User\PaymentController::class, 'handleError'])->name('Payment.error');
                Route::post('webhook', [App\Http\Controllers\User\PaymentController::class, 'webhook'])->name('webhook');

           });

 
            Route::group(['prefix' => 'order'], function () {

                Route::get('invoices/{id}', [App\Http\Controllers\User\OrderController::class, 'show']);
                Route::get('invoices/{id}/pdf', [App\Http\Controllers\User\OrderController::class, 'downloadPdf'])->name('user.order.download');

                Route::get('invoices', [App\Http\Controllers\User\OrderController::class, 'invoices']);

           });
    });

    Route::group(['prefix' => 'user'], function () {
    
        // Route::group(['prefix' => 'ItemsCategories'], function () {

        //     Route::get('getAllItems', [App\Http\Controllers\User\UserDataController::class,'getAll']);
        
        // });



        Route::group(['prefix' => 'Contacts'], function () {

          
            Route::post('createItem2', [App\Http\Controllers\User\BasicController::class,'create2']);
        
        });
    });

    Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum']], function () {
        

        Route::group(['prefix' => 'UserAuth'], function () {

            Route::get('getAuth', [App\Http\Controllers\User\AuthController::class,'getAuth']);
        
        });
 


        Route::group(['prefix' => 'Users'], function () {

            Route::post('editItem', [App\Http\Controllers\User\AuthController::class,'edit']);
            Route::post('editItemPassword', [App\Http\Controllers\User\AuthController::class,'edit_password']);
        
        });


        // Route::group(['prefix' => 'Coupon'], function () {

        //     Route::post('check', [App\Http\Controllers\User\UserController::class,'check']);
        
        // });


    }); // End Prefix User




    





 





    //web route here 





    Route::group(['prefix' => 'web'], function () {
        
        Route::post('/admin/login', [App\Http\Controllers\Web\AuthAdminController::class, 'login']);

        Route::group(['prefix' => 'Settings'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\Setting\SettingController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\Setting\SettingController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\Setting\SettingController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\Setting\SettingController::class,'edit']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\Setting\SettingController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\Setting\SettingController::class,'deleteAllItem']);  
        });


        Route::group(['prefix' => 'Languages'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\LanguageController::class,'getAllItems']);
            Route::get('change', [App\Http\Controllers\Web\LanguageController::class,'changeItem']);
            Route::get('translations', [App\Http\Controllers\Web\LanguageController::class,'translations']);
        });
    
    });




    Route::group(['prefix' => 'web', 'middleware' => ['auth:admin']], function () {
        

 
       

        Route::group(['prefix' => 'AdminAuth'], function () {

            Route::get('getAuth', [App\Http\Controllers\Web\UserManagement\AdminController::class,'getAuth']);
            Route::post('/logOut', [App\Http\Controllers\Web\AuthAdminController::class, 'logout']);

        });

        Route::get('KaheelSuppliers/getAllItems', [App\Http\Controllers\Kaheel\SupplierController::class, 'index']);
        Route::post('KaheelSuppliers/createItem', [App\Http\Controllers\Kaheel\SupplierController::class, 'store']);
        Route::post('KaheelSuppliers/editItem', [App\Http\Controllers\Kaheel\SupplierController::class, 'update']);

        Route::get('KaheelPackages/getAllItems', [App\Http\Controllers\Kaheel\PackageController::class, 'index']);
        Route::post('KaheelPackages/savePackage', [App\Http\Controllers\Kaheel\PackageController::class, 'savePackage']);
        Route::post('KaheelPackages/togglePackage', [App\Http\Controllers\Kaheel\PackageController::class, 'togglePackage']);
        Route::post('KaheelPackages/deletePackage', [App\Http\Controllers\Kaheel\PackageController::class, 'deletePackage']);
        Route::post('KaheelPackages/saveTier', [App\Http\Controllers\Kaheel\PackageController::class, 'saveTier']);
        Route::post('KaheelPackages/deleteTier', [App\Http\Controllers\Kaheel\PackageController::class, 'deleteTier']);
        Route::post('KaheelPackages/copyItems', [App\Http\Controllers\Kaheel\PackageController::class, 'copyItems']);
        Route::post('KaheelPackages/saveItems', [App\Http\Controllers\Kaheel\PackageController::class, 'saveItems']);

        Route::get('KaheelSettings/getAllItems', [App\Http\Controllers\Kaheel\SettingController::class, 'index']);
        Route::post('KaheelSettings/save', [App\Http\Controllers\Kaheel\SettingController::class, 'save']);

        Route::get('KaheelRoles/getAllItems', [App\Http\Controllers\Kaheel\RoleController::class, 'index']);
        Route::post('KaheelRoles/save', [App\Http\Controllers\Kaheel\RoleController::class, 'save']);
        Route::post('KaheelRoles/delete', [App\Http\Controllers\Kaheel\RoleController::class, 'delete']);
        Route::post('KaheelRoles/assign', [App\Http\Controllers\Kaheel\RoleController::class, 'assign']);

        Route::get('KaheelStaff/getAllItems', [App\Http\Controllers\Kaheel\StaffController::class, 'index']);
        Route::post('KaheelStaff/createItem', [App\Http\Controllers\Kaheel\StaffController::class, 'store']);
        Route::post('KaheelStaff/save', [App\Http\Controllers\Kaheel\StaffController::class, 'save']);

        Route::get('KaheelOverview/stats', [App\Http\Controllers\Kaheel\OverviewController::class, 'stats']);

        Route::get('KaheelOrders/notices', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'notices']);
        Route::post('KaheelOrders/readMessages', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'readMessages']);
        Route::get('KaheelOrders/getAllItems', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'index']);
        Route::get('KaheelOrders/show', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'show']);
        Route::get('KaheelOrders/packageItems', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'packageItems']);
        Route::get('KaheelOrders/file/{file}', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'file']);
        Route::post('KaheelOrders/reviewPayment', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'reviewPayment']);
        Route::post('KaheelOrders/confirmFinal', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'confirmFinal']);
        Route::post('KaheelOrders/advance', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'advance']);
        Route::post('KaheelOrders/saveComponents', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'saveComponents']);
        Route::post('KaheelOrders/sendDesign', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'sendDesign']);
        Route::post('KaheelOrders/assignSuppliers', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'assignSuppliers']);
        Route::post('KaheelOrders/markDelivered', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'markDelivered']);
        Route::post('KaheelOrders/message', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'message']);
        Route::get('KaheelOrders/messageMedia/{message}', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'messageMedia']);
        Route::post('KaheelOrders/upload', [App\Http\Controllers\Kaheel\AdminOrderController::class, 'upload']);

        Route::get('KaheelAlternatives/getAllItems', [App\Http\Controllers\Kaheel\AlternativeController::class, 'index']);
        Route::post('KaheelAlternatives/save', [App\Http\Controllers\Kaheel\AlternativeController::class, 'save']);

        Route::get('KaheelCategories/getAllItems', [App\Http\Controllers\Kaheel\CategoryController::class, 'index']);
        Route::post('KaheelCategories/save', [App\Http\Controllers\Kaheel\CategoryController::class, 'save']);
        Route::post('KaheelCategories/delete', [App\Http\Controllers\Kaheel\CategoryController::class, 'delete']);




        Route::group(['prefix' => 'Admins'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\UserManagement\AdminController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\UserManagement\AdminController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\UserManagement\AdminController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\UserManagement\AdminController::class,'edit']);
            Route::post('EditItemPassword', [App\Http\Controllers\Web\UserManagement\AdminController::class,'edit_password']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\UserManagement\AdminController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\UserManagement\AdminController::class,'deleteAllItem']);
        
        });


        



        Route::group(['prefix' => 'Users'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\UserManagement\UserController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\UserManagement\UserController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\UserManagement\UserController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\UserManagement\UserController::class,'edit']);
            Route::post('EditItemPassword', [App\Http\Controllers\Web\UserManagement\UserController::class,'edit_password']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\UserManagement\UserController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\UserManagement\UserController::class,'deleteAllItem']);
        
        });
       


        Route::group(['prefix' => 'Roles'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\UserManagement\RoleController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\UserManagement\RoleController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\UserManagement\RoleController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\UserManagement\RoleController::class,'edit']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\UserManagement\RoleController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\UserManagement\RoleController::class,'deleteAllItem']);
            Route::Delete('getAllItemsPermission', [App\Http\Controllers\Web\UserManagement\RoleController::class,'deleteAllItem']);
        
        });



        Route::group(['prefix' => 'Employees'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\HR\EmployeeController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\HR\EmployeeController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\HR\EmployeeController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\HR\EmployeeController::class,'edit']);
            Route::post('EditItemPassword', [App\Http\Controllers\Web\HR\EmployeeController::class,'edit_password']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\HR\EmployeeController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\HR\EmployeeController::class,'deleteAllItem']);
            Route::Delete('getAllItemsPermission', [App\Http\Controllers\Web\HR\EmployeeController::class,'deleteAllItem']);
        
        });



        Route::group(['prefix' => 'Permission'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\UserManagement\PermissionController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\UserManagement\PermissionController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\UserManagement\PermissionController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\UserManagement\PermissionController::class,'edit']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\UserManagement\PermissionController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\UserManagement\PermissionController::class,'deleteAllItem']);
        
        });



        Route::group(['prefix' => 'Pages'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\Setting\PageController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\Setting\PageController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\Setting\PageController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\Setting\PageController::class,'edit']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\Setting\PageController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\Setting\PageController::class,'deleteAllItem']);
        
        });



        Route::group(['prefix' => 'Contacts'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\Setting\ContactController::class,'getAll']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\Setting\ContactController::class,'delete']);
        
        });


       


        Route::group(['prefix' => 'ItemsCategories'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\MasterData\ItemsCategoriesController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\MasterData\ItemsCategoriesController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\MasterData\ItemsCategoriesController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\MasterData\ItemsCategoriesController::class,'edit']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\MasterData\ItemsCategoriesController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\MasterData\ItemsCategoriesController::class,'deleteAllItem']);
        
        });


        // Route::group(['prefix' => 'ResumeTemplate'], function () {

        //     Route::get('getAllItems', [App\Http\Controllers\Web\MasterData\ResumeTemplateController::class,'getAll']);
        //     Route::get('getById', [App\Http\Controllers\Web\MasterData\ResumeTemplateController::class,'getById']);
        //     Route::post('createItem', [App\Http\Controllers\Web\MasterData\ResumeTemplateController::class,'create']);
        //     Route::post('editItem', [App\Http\Controllers\Web\MasterData\ResumeTemplateController::class,'edit']);
        //     Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\MasterData\ResumeTemplateController::class,'delete']);
        //     Route::Delete('deleteAllItem', [App\Http\Controllers\Web\MasterData\ResumeTemplateController::class,'deleteAllItem']);
        
        // });


        Route::group(['prefix' => 'Notifications'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\Setting\NotificationController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\Setting\NotificationController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\Setting\NotificationController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\Setting\NotificationController::class,'edit']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\Setting\NotificationController::class,'delete']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\Setting\NotificationController::class,'deleteAllItem']);
        
        });




        Route::group(['prefix' => 'Ads'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\Ads\AdsController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\Ads\AdsController::class,'getById']);
            Route::post('createItem', [App\Http\Controllers\Web\Ads\AdsController::class,'create']);
            Route::post('editItem', [App\Http\Controllers\Web\Ads\AdsController::class,'edit']);
            Route::Delete('deleteItem/{id}', [App\Http\Controllers\Web\Ads\AdsController::class,'delete']);
            Route::Delete('deleteComment/{id}', [App\Http\Controllers\Web\Ads\AdsController::class,'deleteComment']);
            Route::Delete('deleteAllItem', [App\Http\Controllers\Web\Ads\AdsController::class,'deleteAllItem']);
        
        });



        Route::group(['prefix' => 'Orders'], function () {

            Route::get('getAllItems', [App\Http\Controllers\Web\Orders\OrderController::class,'getAll']);
            Route::get('getById', [App\Http\Controllers\Web\Orders\OrderController::class,'getById']);
            Route::post('editItem', [App\Http\Controllers\Web\Orders\OrderController::class,'edit']);
        
        });


        Route::group(['prefix' => 'Dashboard'], function () {

            Route::get('getAllItemsPercenatge', [App\Http\Controllers\Web\Dashboard\DashboardController::class,'getAllItemsPercenatge']);
            Route::get('getChartsData', [App\Http\Controllers\Web\Dashboard\DashboardController::class,'getChartsData']);
            
        
        });



    }); // End Prefix Web

    


    











}); //End Localizations








