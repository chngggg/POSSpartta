<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #d4af37, #b8942e);
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            color: #0a0a0a;
        }

        .content {
            padding: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #d4af37;
            color: #0a0a0a;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
        }

        .footer {
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            font-size: 12px;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #d4af37;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Reset Password</h1>
        </div>

        <div class="content">
            <h2>Halo!</h2>

            <p>Anda menerima email ini karena ada permintaan reset password untuk akun:</p>

            <div class="info-box">
                <strong>📧 Email Akun:</strong> {{ $originalEmail }}
            </div>

            <p>Klik tombol di bawah untuk mereset password:</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($originalEmail)); }}" class="btn">
                    🔐 Reset Password Sekarang
                </a>
            </div>

            <p>Atau copy link berikut:</p>
            <code style="word-break: break-all; font-size: 12px;">
                {{ $resetUrl }}
            </code>

            <p style="margin-top: 20px; font-size: 12px; color: #666;">
                ⚠️ Link ini akan kadaluarsa dalam 60 menit.
            </p>
        </div>

        <div class="footer">
            <strong>{{ config('app.name') }}</strong><br>
            Sistem Management Toko
        </div>
    </div>
</body>

</html>