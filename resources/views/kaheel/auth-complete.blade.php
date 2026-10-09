<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>FORMA BY AZURE – KAHEEL</title>
</head>
<body>
    <script>
        const token = @json($token);
        const user = @json($user);
        localStorage.setItem("authToken", token);
        localStorage.setItem("token", token);
        localStorage.setItem("user", JSON.stringify(user));
        location.replace(@json($next));
    </script>
</body>
</html>
