<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Verification</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f5f8fa;
      padding: 20px;
    }
    .email-wrapper {
      max-width: 600px;
      margin: auto;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      padding: 40px;
      color: #3f4254;
    }
    .header {
      text-align: center;
      margin-bottom: 30px;
    }
    .header h1 {
      color: #009ef7;
      font-size: 24px;
      margin: 0;
    }
    .content {
      font-size: 16px;
      line-height: 1.6;
    }
    .btn {
      display: inline-block;
      margin-top: 30px;
      padding: 12px 24px;
      background-color: #009ef7;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 500;
    }
    .footer {
      margin-top: 40px;
      font-size: 13px;
      text-align: center;
      color: #a1a5b7;
    }
  </style>
</head>
<body>
  <div class="email-wrapper">
    <div class="header"> 

    <img src="https://seratech.ai/assets/media/svg/brand-logos/logo.png" alt="SeraTech - Logo">



    <title>Welcome to SeraTech - Verify Your Email</title>
    </div>
    <div class="content">
      <p><strong>Welcome, {{ $user->name }} </strong>,</p>
      <p>Thanks for signing up for <strong>Seratech</strong>! Please click the button below to verify your email address:</p>
    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="margin-top: 30px;">
  <tr>
    <td align="center">
      <a href="{{ $verificationUrl }}"
         target="_blank"
         style="background-color:#009ef7;border-radius:6px;color:#ffffff;display:inline-block;font-size:16px;font-weight:500;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;">
         Verify Email
      </a>
    </td>
  </tr>
</table>

<p style="margin-top: 20px;">
    If you're having trouble clicking the "Verify Email" button, copy and paste the URL below into your browser:
    <br>
    <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
</p>
    </div>
    <div class="footer">
      &copy; 2025 Seratech. All rights reserved.
    </div>
  </div>
</body>
</html>
