<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Laboratorio')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: -1;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            z-index: -1;
        }

        .auth-container {
            width: 100%;
            padding: 20px;
        }

        .auth-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.5s ease-out;
            padding: 40px 30px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 40px;
            padding-top: 30px;
        }

        .auth-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .auth-logo i {
            color: white;
            font-size: 40px;
        }

        .auth-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .auth-header p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            color: white;
            width: 100%;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-check-input {
            border-radius: 5px;
            width: 18px;
            height: 18px;
            margin-top: 3px;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            color: #666;
            font-size: 14px;
            margin-left: 8px;
            cursor: pointer;
        }

        .auth-footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #e9ecef;
        }

        .auth-footer p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            font-weight: 500;
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid:focus, .form-select.is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #999;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #e9ecef;
        }

        .divider span {
            padding: 0 15px;
            font-size: 13px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .auth-card {
                padding: 30px 20px;
            }

            .auth-header {
                margin-bottom: 30px;
                padding-top: 20px;
            }

            .auth-header h1 {
                font-size: 24px;
            }

            .auth-logo {
                width: 70px;
                height: 70px;
            }

            .auth-logo i {
                font-size: 35px;
            }

            .form-label {
                font-size: 13px;
            }

            .form-control, .form-select {
                padding: 10px 12px;
                font-size: 13px;
            }

            .btn-login {
                padding: 10px 18px;
                font-size: 13px;
            }
        }

        @media (max-width: 576px) {
            .auth-container {
                padding: 15px;
            }

            .auth-card {
                padding: 25px 15px;
            }

            .auth-header {
                margin-bottom: 25px;
                padding-top: 15px;
            }

            .auth-header h1 {
                font-size: 22px;
            }

            .auth-header p {
                font-size: 12px;
            }

            .auth-logo {
                width: 65px;
                height: 65px;
            }

            .auth-logo i {
                font-size: 32px;
            }

            .mb-4 {
                margin-bottom: 1rem !important;
            }

            .form-label {
                font-size: 13px;
                margin-bottom: 6px;
            }

            .form-control, .form-select {
                padding: 9px 10px;
                font-size: 13px;
            }

            .btn-login {
                padding: 10px 16px;
                font-size: 13px;
            }

            .divider span {
                padding: 0 10px;
                font-size: 12px;
            }

            .alert {
                padding: 12px 16px;
                font-size: 13px;
                margin-bottom: 15px;
            }

            .form-check-label {
                font-size: 13px;
            }

            .auth-footer p {
                font-size: 13px;
            }

            .auth-footer a {
                font-size: 13px;
            }

            .d-flex {
                flex-direction: column;
                gap: 10px;
            }

            .d-flex .form-check {
                width: 100%;
            }

            .d-flex a {
                text-align: center;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
