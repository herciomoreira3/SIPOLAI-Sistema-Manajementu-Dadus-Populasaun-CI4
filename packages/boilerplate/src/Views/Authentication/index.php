<!DOCTYPE html>
<html lang="<?= config('App')->defaultLocale ?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= config('Boilerplate')->appName ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.12.0/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ionicons@2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/icheck-bootstrap@3.0.1/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.0.2/dist/css/adminlte.min.css">
  <!-- Google Font: Outfit -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">

  <style>
    body.login-page {
        background-color: #fafafa !important;
        font-family: 'Outfit', sans-serif;
        color: #09090b;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
        overflow: hidden;
    }
    .login-box {
        width: 420px;
        background-color: #ffffff !important;
        border: 1px solid #e4e4e7 !important;
        border-radius: 16px !important;
        padding: 40px 30px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02), 0 2px 8px -1px rgba(0, 0, 0, 0.01) !important;
        transition: all 0.2s ease;
    }
    .login-logo {
        margin-bottom: 25px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .login-logo b {
        color: #09090b !important;
        font-weight: 700;
        font-size: 24px;
        letter-spacing: -0.025em;
        display: block;
        margin-top: 8px;
    }
    .login-logo img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        box-shadow: none !important;
        border: 1px solid #e4e4e7 !important;
        object-fit: cover;
    }
    .card {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
    .login-card-body {
        background: transparent !important;
        padding: 0 !important;
        color: #71717a !important;
    }
    .login-box-msg {
        color: #71717a !important;
        font-size: 14px;
        margin-bottom: 25px !important;
        padding: 0 !important;
        text-align: center;
    }
    .form-control {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        color: #09090b !important;
        padding: 11px 15px !important;
        height: auto !important;
        font-size: 14px !important;
        transition: all 0.2s ease !important;
    }
    .form-control:focus {
        border-color: #09090b !important;
        box-shadow: 0 0 0 3px rgba(9, 9, 11, 0.05) !important;
        background-color: #ffffff !important;
    }
    .form-control::placeholder {
        color: #a1a1aa !important;
    }
    .form-control {
        padding-right: 40px !important;
    }
    .input-icon {
        position: absolute !important;
        right: 14px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        color: #a1a1aa !important;
        pointer-events: none !important;
        font-size: 15px !important;
        z-index: 5 !important;
    }
    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        border: none !important;
        border-radius: 10px !important;
        padding: 11px 20px !important;
        font-weight: 600 !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        width: 100%;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.18) !important;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af) !important;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25) !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }
    .btn-primary:active {
        transform: translateY(0) !important;
    }
    .icheck-primary label {
        color: #71717a !important;
        font-size: 13.5px;
        font-weight: 400 !important;
    }
    .icheck-primary input[type="checkbox"]:checked + label::before {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
    }
    a {
        color: #2563eb !important;
        font-size: 13.5px;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    a:hover {
        color: #1d4ed8 !important;
        text-decoration: underline !important;
    }
    .alert-danger {
        background-color: #fef2f2 !important;
        border: 1px solid #fee2e2 !important;
        color: #b91c1c !important;
        border-radius: 10px !important;
        padding: 11px 15px !important;
        font-size: 13.5px !important;
        margin-bottom: 20px !important;
    }
    .alert-success {
        background-color: #ecfdf5 !important;
        border: 1px solid #d1fae5 !important;
        color: #047857 !important;
        border-radius: 10px !important;
        padding: 11px 15px !important;
        font-size: 13.5px !important;
        margin-bottom: 20px !important;
    }
  </style>
</head>
 
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <b style="font-size: 19px !important; letter-spacing: -0.01em !important; line-height: 1.3 !important; display: block !important; margin-top: 12px !important; color: #09090b !important;">Sistema Manajementu Dados Populasaun</b>
      <img src="<?= base_url('uploads/banner.png') ?>" alt="Banner" class="img-fluid mt-3" style="width: 100% !important; height: auto !important; border-radius: 8px !important; box-shadow: 0 4px 12px rgba(0,0,0,0.02) !important; border: 1px solid #e2e8f0 !important; object-fit: contain !important;">
    </div>
    <?= $this->renderSection('content') ?>
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.0.2/dist/js/adminlte.min.js"></script>

</body>

</html>
