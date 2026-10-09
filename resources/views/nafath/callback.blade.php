<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نفاذ - تم التحقق</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; padding: 2rem; text-align: center; background: #f5f5f5; }
        .box { background: #fff; padding: 2rem; border-radius: 8px; max-width: 400px; margin: 2rem auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #0d6efd; margin-bottom: 1rem; }
        p { color: #666; }
    </style>
</head>
<body>
    <div class="box">
        <h1>{{ $status === 'success' ? 'تم التحقق بنجاح' : 'انتهت العملية' }}</h1>
        <p id="msg">جاري تحويلك لصفحة الإعلان...</p>
    </div>
    <script>
        (function() {
            var status = @json($status);
            var nationalId = @json($national_id);
            var params = @json($params);
            var returnUrl = @json($return_url);
            var nid = nationalId || params.national_id || params.nationalId || '';
            if (window.opener && !window.opener.closed) {
                window.opener.postMessage({
                    type: status === 'success' ? 'nafath_callback' : 'nafath_error',
                    national_id: nid,
                    status: status,
                    message: status === 'success' ? 'تم التحقق بنجاح' : (params.message || 'فشل التحقق'),
                    verified: status === 'success'
                }, '*');
                setTimeout(function() { window.close(); }, 1200);
            } else {
                var sep = returnUrl.indexOf('?') >= 0 ? '&' : '?';
                var target = returnUrl + sep + 'nafath=' + (status === 'success' ? 'success' : 'fail');
                if (nid) target += '&national_id=' + encodeURIComponent(nid);
                window.location.href = target;
            }
        })();
    </script>
</body>
</html>
