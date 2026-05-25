<!DOCTYPE html>
<html lang="<?= config('App')->defaultLocale ?>">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>">
  <title><?= $title ?? '' ?> | <?= config('Boilerplate')->appName ?></title>
  <link rel="shortcut icon" type="image/png" href="<?= base_url('uploads/logo.png') ?>">
  
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.12.0/css/all.min.css">
  <!-- Sweetalert -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.2/dist/sweetalert2.min.css">
  <!-- Render section boilerplate css -->
  <?= $this->renderSection('css') ?>
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.0.4/dist/css/adminlte.min.css">
  <!-- Google Font: Outfit -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">

  <style>
    body {
        font-family: 'Outfit', sans-serif !important;
        background-color: #fafafa !important;
        color: #09090b !important;
    }
    
    /* 1. Elegant Black & White Sidebar */
    .main-sidebar {
        background-color: #ffffff !important;
        border-right: 1px solid #e4e4e7 !important;
        box-shadow: none !important;
        transition: all 0.2s ease;
    }
    .brand-link {
        border-bottom: 1px solid #e4e4e7 !important;
        padding: 20px 24px !important;
        background-color: #ffffff !important;
    }
    .brand-link .brand-text {
        font-weight: 700 !important;
        color: #09090b !important;
        font-size: 18px !important;
        letter-spacing: -0.02em;
    }
    .brand-link img {
        box-shadow: none !important;
        border: 1px solid #e4e4e7 !important;
    }
    .sidebar {
        padding: 15px 12px !important;
        background-color: #ffffff !important;
        overflow-y: auto !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }
    .sidebar::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    .nav-sidebar .nav-item {
        margin-bottom: 4px !important;
    }
    .nav-sidebar .nav-link {
        border-radius: 10px !important;
        padding: 9px 15px !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        color: #64748b !important;
        font-weight: 500 !important;
        font-size: 13.5px !important;
        border: none !important;
        background: transparent !important;
    }
    .nav-sidebar .nav-link:hover {
        background-color: #eff6ff !important;
        color: #2563eb !important;
        transform: translateX(4px) !important;
    }
    .nav-sidebar .nav-link.active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.22) !important;
        font-weight: 600 !important;
    }
    .nav-sidebar .nav-link.active i {
        color: #ffffff !important;
    }
    .nav-sidebar .nav-link i {
        margin-right: 10px !important;
        font-size: 15px !important;
        color: #94a3b8 !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .nav-sidebar .nav-link:hover i {
        color: #2563eb !important;
        transform: scale(1.1) !important;
    }
    .nav-sidebar .nav-link.active i {
        color: #ffffff !important;
    }
    .nav-header {
        text-transform: uppercase;
        font-size: 10px !important;
        letter-spacing: 0.05em;
        color: #a1a1aa !important;
        padding: 14px 15px 6px 15px !important;
        font-weight: 600 !important;
    }
    
    /* 2. Header (Navbar) clean black & white styles */
    .main-header {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e4e4e7 !important;
        box-shadow: none !important;
        padding: 12px 24px !important;
    }
    .main-header .nav-link {
        color: #71717a !important;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .main-header .nav-link:hover {
        color: #09090b !important;
    }
    .main-header .navbar-search-block .form-control {
        background-color: #f4f4f5 !important;
        border-color: #e4e4e7 !important;
        color: #09090b !important;
        border-radius: 8px !important;
    }
    
    /* 3. Footer clean styles */
    .main-footer {
        background-color: #ffffff !important;
        border-top: 1px solid #e4e4e7 !important;
        color: #71717a !important;
        padding: 16px 24px !important;
        font-size: 13px !important;
        box-shadow: none !important;
    }
    .main-footer a {
        color: #09090b !important;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border-bottom: 1px dotted #09090b;
    }
    .main-footer a:hover {
        color: #71717a !important;
        border-bottom-color: #71717a;
    }
    
    /* Content Wrapper Background */
    .content-wrapper {
        background-color: #fafafa !important;
    }
    
    /* Modern Black & White Card styles */
    .card-premium {
        background-color: #ffffff !important;
        border: 1px solid #e4e4e7 !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02), 0 2px 8px -1px rgba(0, 0, 0, 0.01) !important;
        transition: all 0.2s ease;
    }
    .card-premium:hover {
        box-shadow: 0 6px 24px -2px rgba(0, 0, 0, 0.04), 0 4px 12px -1px rgba(0, 0, 0, 0.02) !important;
    }
    .btn-rounded {
        border-radius: 10px !important;
        padding: 7px 16px !important;
        font-weight: 600 !important;
    }
    
    /* Clean button overrides: Premium Royal Blue Gradient! */
    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        border: none !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.18) !important;
        transition: all 0.2s ease !important;
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

    /* Force card header styling to push create buttons to the far top-right corner */
    .card-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        width: 100% !important;
    }
    .card-header::after {
        display: none !important;
    }
    .card-header .card-tools {
        margin: 0 !important;
    }

    .btn-secondary {
        background-color: #f4f4f5 !important;
        border: 1px solid #e4e4e7 !important;
        color: #09090b !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }
    .btn-secondary:hover {
        background-color: #e4e4e7 !important;
        border-color: #d4d4d8 !important;
    }

    .btn-success {
        background-color: #f0fdf4 !important;
        border: 1px solid #dcfce7 !important;
        color: #166534 !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }
    .btn-success:hover {
        background-color: #dcfce7 !important;
        color: #14532d !important;
    }

    .btn-danger {
        background-color: #fef2f2 !important;
        border: 1px solid #fee2e2 !important;
        color: #991b1b !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }
    .btn-danger:hover {
        background-color: #fee2e2 !important;
        color: #7f1d1d !important;
    }

    .btn-warning {
        background-color: #fffbeb !important;
        border: 1px solid #fef3c7 !important;
        color: #92400e !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }
    .btn-warning:hover {
        background-color: #fef3c7 !important;
        color: #78350f !important;
    }

    .btn-info {
        background-color: #f0f9ff !important;
        border: 1px solid #e0f2fe !important;
        color: #0369a1 !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }
    .btn-info:hover {
        background-color: #e0f2fe !important;
        color: #075985 !important;
    }

    /* Clean badge overrides */
    .badge-success {
        background-color: #f0fdf4 !important;
        color: #166534 !important;
        border: 1px solid #dcfce7 !important;
        font-weight: 600 !important;
        padding: 5px 10px !important;
        border-radius: 20px !important;
    }
    .badge-danger {
        background-color: #fef2f2 !important;
        color: #991b1b !important;
        border: 1px solid #fee2e2 !important;
        font-weight: 600 !important;
        padding: 5px 10px !important;
        border-radius: 20px !important;
    }
    .badge-warning {
        background-color: #fffbeb !important;
        color: #92400e !important;
        border: 1px solid #fef3c7 !important;
        font-weight: 600 !important;
        padding: 5px 10px !important;
        border-radius: 20px !important;
    }
    .badge-info {
        background-color: #f0f9ff !important;
        color: #0369a1 !important;
        border: 1px solid #e0f2fe !important;
        font-weight: 600 !important;
        padding: 5px 10px !important;
        border-radius: 20px !important;
    }
    .badge-primary {
        background-color: #f4f4f5 !important;
        color: #09090b !important;
        border: 1px solid #e4e4e7 !important;
        font-weight: 600 !important;
        padding: 5px 10px !important;
        border-radius: 20px !important;
    }
    .badge-secondary {
        background-color: #f4f4f5 !important;
        color: #71717a !important;
        border: 1px solid #e4e4e7 !important;
        font-weight: 600 !important;
        padding: 5px 10px !important;
        border-radius: 20px !important;
    }
  </style>

</head>

<body class="layout-fixed layout-navbar-fixed sidebar-mini <?= config('Boilerplate')->theme['footer']['fixed'] ? 'layout-footer-fixed' : '' ?> <?= config('Boilerplate')->theme['body-sm'] ? 'text-sm' : '' ?>">
  <div class="wrapper">

    <!-- Navbar -->
    <?= $this->include('Boilerplate\Views\layout\header') ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?= $this->include('Boilerplate\Views\layout\mainsidebar') ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <?= $this->include('Boilerplate\Views\layout\contentheader') ?>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <?= $this->renderSection('content') ?>
        </div>
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
      <!-- <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
      </div> -->
    </aside>
    <!-- /.control-sidebar -->

    <footer class="main-footer text-center">
      Sistema Manajementu Dados Populasaun Suco Laisorolai de Baixo Postu Administrativu Matebian Munisipiu Baucau &copy; 2026 <strong>RUMAH HANTU</strong>. All rights reserved.
    </footer>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->

  <!-- jQuery -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
  <!-- Global DataTables Tetum Language Interceptor -->
  <script>
    $.ajaxTransport("json", function(options, originalOptions, jqXHR) {
        if (options.url && (options.url.indexOf('Indonesian.json') !== -1 || options.url.indexOf('i18n') !== -1)) {
            return {
                send: function(headers, callback) {
                    var tetumLang = {
                        "sProcessing":   "Prosesu daudaun...",
                        "sLengthMenu":   "Hatudu _MENU_ entri",
                        "sZeroRecords":  "La iha dadus ne'ebé tuir filtru",
                        "sInfo":         "Hatudu _START_ to'o _END_ husi total _TOTAL_ entri",
                        "sInfoEmpty":    "Hatudu 0 to'o 0 husi total 0 entri",
                        "sInfoFiltered": "(buka husi total _MAX_ entri)",
                        "sInfoPostFix":  "",
                        "sSearch":       "Buka:",
                        "sUrl":          "",
                        "oPaginate": {
                            "sFirst":    "Primeiru",
                            "sPrevious": "Antes",
                            "sNext":     "Tuirmai",
                            "sLast":     "Ultimu"
                        }
                    };
                    callback(200, "success", { json: tetumLang });
                },
                abort: function() {
                    // Noop
                }
            };
        }
    });
  </script>
  <!-- Bootstrap 4 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.0.4/dist/js/adminlte.min.js"></script>
  <!-- Preload Scriptt -->
  <script>
  $('.sidebar-toggle').on('click',function(event){event.preventDefault();if(Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))){sessionStorage.setItem('sidebar-toggle-collapsed','')}else{sessionStorage.setItem('sidebar-toggle-collapsed','1')}});(function(){if(Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))){var body=document.getElementsByTagName('body')[0];body.className=body.className+' sidebar-collapse'}})()
  </script>
  <!-- Render section boilerplate js -->
  <?= $this->renderSection('js') ?>
  <script>
  $.ajaxSetup({headers:{'<?= csrf_header() ?>':$('meta[name="<?= csrf_token() ?>"]').attr('content')}})
  </script>
  <!-- Sweeat alert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.2/dist/sweetalert2.all.min.js"></script>
  <script>
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  });

  <?php if (session('sweet-success')) { ?>
    Toast.fire({
      icon: 'success',
      title: '<?= session('sweet-success.') ?>'
    });
  <?php } ?>
  <?php if (session('sweet-warning')) { ?>
    Toast.fire({
      icon: 'warning',
      title: '<?= session('sweet-warning.') ?>'
    });
  <?php } ?>
  <?php if (session('sweet-error')) { ?>
    Toast.fire({
      icon: 'error',
      title: '<?= session('sweet-error.') ?>'
    });
  <?php } ?>
  </script>
</body>

</html>
