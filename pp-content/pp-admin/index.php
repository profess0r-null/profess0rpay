<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if ($global_user_login == true) {

    } else {
        if ($global_user_2fa == true) {
?>
            <script>location.href = "<?php echo rtrim($site_url, '/').'/'.$path_admin ?>/2fa";</script>
<?php
            exit();
        }else{
?>
            <script>location.href = "<?php echo rtrim($site_url, '/').'/'.$path_admin ?>/login";</script>
<?php
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Profess0rPay</title>
    <link rel="shortcut icon" href="<?= $professorpay_favicon ?? '' ?>">
    <?php
        $manifestData = [
            "name" => "Profess0rPay Admin",
            "short_name" => "Admin Panel",
            "description" => "Profess0rPay Admin Dashboard",
            "start_url" => rtrim($site_url, '/').'/'.$path_admin,
            "display" => "standalone",
            "background_color" => "#ffffff",
            "theme_color" => "#4361ee",
            "icons" => [
                [
                    "src" => rtrim($site_url, '/') . "/assets/images/default_favicon.png",
                    "sizes" => "192x192",
                    "type" => "image/png",
                    "purpose" => "any maskable"
                ],
                [
                    "src" => rtrim($site_url, '/') . "/assets/images/default_favicon.png",
                    "sizes" => "512x512",
                    "type" => "image/png",
                    "purpose" => "any maskable"
                ]
            ]
        ];
        $manifestBase64 = base64_encode(json_encode($manifestData));
    ?>
    <link rel="manifest" href="data:application/manifest+json;base64,<?php echo $manifestBase64; ?>">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo $site_url ?>sw.js');
            });
        }
    </script>
    <link rel="stylesheet" href="<?php echo $site_url ?>assets/css/tabler.min.css?v=1.7" />
    <link rel="stylesheet" href="<?php echo $site_url ?>assets/css/choices.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-flags.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-payments.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-socials.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-vendors.min.css" />

    <style>
      @import url("<?php echo $site_url ?>assets/css/inter.css");
    </style>
    <style>
        :root{
            --tblr-font-monospace: Monaco, Consolas, Liberation Mono, Courier New, monospace;
            --tblr-font-sans-serif: Inter Var, Inter, -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --tblr-font-serif: Georgia, Times New Roman, times, serif;
            --tblr-font-comic: Comic Sans MS, Comic Sans, Chalkboard SE, Comic Neue, sans-serif, cursive;
        }
        #sidebarMenu {
            max-width: 310px;
            width: 100%;
        }
        #sidebarMenu ul{
            padding: 15px;
        }
        #sidebarMenu li.nav-item a{
            height: 40px;
        }
        #sidebarMenu li.nav-item a .nav-link-icon{
            width: 1.45rem;
            min-width: 1.45rem;
            height: 1.45rem;
            margin-right: .2rem;
        }
        #sidebarMenu li.nav-item a .nav-link-icon svg{
            width: 1.45rem;
            min-width: 1.45rem;
            height: 1.45rem;
        }
        #sidebarMenu li.card-title{
            margin-left: 15px;
            font-size: .875rem;
            margin-bottom: 0px;
        }
        .page-wrapper{
            margin: 15px;
        }

        .choices {
            font-size: .875rem;
            font-weight: 400;
        }
        .choices__inner {
            display: inline-block;
            vertical-align: top;
            width: 100%;
            background-color: #FFFFFF;
            padding: .5625rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: calc(6px * 1);
            font-size: .875rem;
            font-weight: 400;
            min-height: 0;
            overflow: hidden;
        }
        .choices__list--single{
            padding: 0.8px;
        }
        .choices__list--multiple .choices__item {
            display: inline-block;
            vertical-align: middle;
            border-radius: 15px;
            padding: 3px 10px;
            font-size: 12px;
            font-weight: 400;
            margin-right: 1.75px;
            margin-bottom: 1.75px;
            background-color: var(--tblr-primary);
            border: 1px solid var(--tblr-primary);
            color: #fff;
            word-break: break-all;
            box-sizing: border-box;
        }
        .choices__input {
            display: inline-block;
            vertical-align: baseline;
            background-color: #FFFFFF;
            font-size: .875rem;
            font-weight: 400;
            margin-bottom: 0;
            border: 0;
            border-radius: 0;
            max-width: 100%;
            padding: 0;
        }
        .is-focused .choices__inner, .is-open .choices__inner{
            border: 1px solid #e5e7eb;
            border-radius: calc(6px * 1);
            color: var(--tblr-body-color);
            background-color: var(--tblr-bg-forms);
            border-color: rgb(126, 94, 255);
            outline: 0;
            box-shadow: var(--tblr-shadow-input), 0 0 0 .25rem rgba(var(--tblr-primary-rgb), .25)
        }
        .is-open .choices__list--dropdown, .is-open .choices__list[aria-expanded]{
            border: 1px solid #e5e7eb;
            border-radius: calc(6px * 1);
            box-shadow: 0 0 4px rgba(31, 41, 55, 0.04);
        }
        .choices__list--dropdown, .choices__list[aria-expanded]{
            z-index: 3;
        }

        @media (min-width: 768px) {
            #sidebarMenu {
                height: calc(100vh - 64px); /* full viewport minus header height */
                overflow-y: auto;          /* scroll inside sidebar */
                position: fixed;
                top: 64px;                  /* below header */
                left: 0;
                background: #f8f9fa;
            }
            .page-wrapper{
                margin-left: 325px;
            }
        }
        @media (max-width: 767px) {
            .mobile-center-logo {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
            }
            .notify-dropdown-mobile {
                position: fixed !important;
                top: 60px !important;
                left: 0 !important;
                right: 0 !important;
                width: 100vw !important;
                border-radius: 0;
                transform: none !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="layout-fluid" cz-shortcut-listen="true">
    <div id="topProgress" class="progress d-none" style="position: fixed; top: 0; left: 0; width: 100%; z-index: 1111; height: 3px;"> <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 0%"></div> </div>
    
    <div class="page">
        <!-- BEGIN NAVBAR  -->
        <header class="navbar navbar-expand-md sticky-top d-print-none py-2">
          <div class="container-xl">
            <!-- BEGIN NAVBAR TOGGLER -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"> <span class="navbar-toggler-icon"></span> </button>

            <!-- END NAVBAR TOGGLER -->
            <!-- BEGIN NAVBAR LOGO -->
            <div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3 mobile-center-logo">
              <a href="javascript:void(0)" aria-label="Tabler">
                  <img src="<?= $professorpay_logo_light ?? '' ?>" alt="" style="height: 32px;" onclick="load_content('Dashboard','<?php echo $site_url.$path_admin ?>/dashboard','nav-menu-dashboard')">
              </a>
            </div>
            <!-- END NAVBAR LOGO -->
            <div class="navbar-nav flex-row order-md-last">
              <div class="nav-item dropdown d-flex me-1 me-md-3">
                <style>
                  .notification-bell-icon { width: 24px; height: 24px; }
                  @media (min-width: 768px) { .notification-bell-icon { width: 29px; height: 29px; } }
                </style>
                <a href="#" class="nav-link px-0 position-relative" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications" style="margin-right: 4px;">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon notification-bell-icon" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                  <span class="badge bg-red text-white d-none" id="notification-badge" style="position: absolute; top: 2px; right: 0px; border-radius: 50%; font-size: 10px; padding: 0; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; font-weight: bold;"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card notify-dropdown-mobile" style="width: 350px; max-width: 100vw;">
                  <div class="card border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h3 class="card-title">Notifications</h3>
                      <div>
                        <button type="button" class="btn btn-sm btn-danger btn-pill me-2" data-bs-toggle="modal" data-bs-target="#modal-clear-notifications">Clear All</button>
                        <a href="javascript:void(0)" class="text-muted" onclick="markAllNotificationsRead(event)">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 12l5 5l10 -10" /><path d="M2 12l5 5m5 -5l5 -5" /></svg>
                        </a>
                      </div>
                    </div>
                    <div class="list-group list-group-flush list-group-hoverable" id="notification-list" style="max-height: 300px; overflow-y: auto;">
                      <div class="list-group-item text-center">No new notifications</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu" aria-expanded="false">
                  <span class="avatar avatar-sm" style="background-image: url(https://ui-avatars.com/api/?name=<?php echo getNameChars($global_user_response['response'][0]['full_name'], 2);?>&color=FFFFFF&background=343a40"> </span>
                  <div class="d-none d-xl-block ps-2">
                    <div style="width: 100px;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;"><?php echo $global_user_response['response'][0]['full_name']?></div>
                    <div class="mt-1 small text-secondary"><?php echo ucfirst($global_user_response['response'][0]['role'])?></div>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <a href="javascript:void(0)" class="dropdown-item" onclick="load_content('My Account','<?php echo $site_url.$path_admin ?>/my-account','nav-menu-my-account')">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                      My Account
                  </a>
                  <a href="javascript:void(0)" class="dropdown-item" onclick="load_content('Activities','<?php echo $site_url.$path_admin ?>/activities','nav-item-activities')">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-activity"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12h4l3 8l4 -16l3 8h4" /></svg>
                      Activities
                  </a>
                  <div class="dropdown-divider"></div>
                  <a href="<?php echo $site_url.$path_admin ?>/?logout" class="dropdown-item">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-logout"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
                      Logout
                  </a>
                </div>
              </div>
            </div>
          </div>
        </header>

        <!-- SIDEBAR -->
        <div class="offcanvas-md offcanvas-start sidebar" tabindex="-1" id="sidebarMenu">
          <div class="offcanvas-header d-md-none">
              <a href="javascript:void(0)" aria-label="Tabler" onclick="load_content('Dashboard','<?php echo $site_url.$path_admin ?>/dashboard','nav-menu-dashboard')">
                  <img src="<?= $professorpay_logo_light ?? '' ?>" alt="" style="height: 32px;" onclick="load_content('Dashboard','<?php echo $site_url.$path_admin ?>/dashboard','nav-menu-dashboard')">
              </a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body p-0">
            <ul class="nav w-100 flex-column gap-2">

              <div class="nav-item dropdown mb-5 mt-2">
                <a href="#" class="nav-link d-flex lh-1 p-2 rounded" data-bs-toggle="dropdown" aria-label="Open user menu" aria-expanded="false">
                  <span class="avatar avatar-sm" style="min-width: 32px; background-image: url(https://ui-avatars.com/api/?name=<?php echo getNameChars($global_response_brand['response'][0]['identify_name'], 1);?>&color=FFFFFF&background=343a40"> </span>
                  <div class="ps-2 w-100">
                    <div class="text-black" style="width: 100px;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;"><?php echo $global_response_brand['response'][0]['identify_name'];?></div>
                    <div class="mt-1 small text-secondary">Active brand</div>
                  </div>

                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-caret-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 9c.852 0 1.297 .986 .783 1.623l-.076 .084l-6 6a1 1 0 0 1 -1.32 .083l-.094 -.083l-6 -6l-.083 -.094l-.054 -.077l-.054 -.096l-.017 -.036l-.027 -.067l-.032 -.108l-.01 -.053l-.01 -.06l-.004 -.057v-.118l.005 -.058l.009 -.06l.01 -.052l.032 -.108l.027 -.067l.07 -.132l.065 -.09l.073 -.081l.094 -.083l.077 -.054l.096 -.054l.036 -.017l.067 -.027l.108 -.032l.053 -.01l.06 -.01l.057 -.004l12.059 -.002z" /></svg>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow w-100">
                  <?php
                      $response_permission = json_decode(getData($db_prefix.'permission','WHERE a_id = "'.$global_user_response['response'][0]['a_id'].'" AND status = "active" AND brand_id != "'.$global_response_permission['response'][0]['brand_id'].'"'),true);
                      if($response_permission['status'] == true){
                          foreach($response_permission['response'] as $row){
                              $response_brand = json_decode(getData($db_prefix.'brands','WHERE brand_id = "'.$row['brand_id'].'"'),true);
                  ?>
                              <a href="javascript:void(0)" class="dropdown-item" onclick="set_brand('<?php echo $row['brand_id']?>')">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-store"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" /><path d="M5 21l0 -10.15" /><path d="M19 21l0 -10.15" /><path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" /></svg>
                                  <?php echo $response_brand['response'][0]['identify_name']?>
                              </a>
                  <?php
                          }
                  ?>
                          <div class="dropdown-divider"></div>
                  <?php
                      }
                  ?>
                  
                  <a href="javascript:void(0)" class="dropdown-item <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'brands', 'create', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="load_content('Create New Brand','<?php echo $site_url.$path_admin ?>/brands/create','nav-item-brands')">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                      Create New
                  </a>
                </div>
              </div>

              <!-- Dashboard -->
              <li class="nav-item nav-item-dashboard <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'dashboard', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Dashboard','<?php echo $site_url.$path_admin ?>/dashboard','nav-item-dashboard')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-home"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg></span>
                  <span class="nav-link-title ms-2">Dashboard</span>
                </a>
              </li>

              <!-- Reports -->
              <li class="nav-item nav-item-reports <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'reports', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Reports','<?php echo $site_url.$path_admin ?>/reports','nav-item-reports')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-pie-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3v9h9" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg></span>
                  <span class="nav-link-title ms-2">Reports</span>
                </a>
              </li>

              <!-- Gateways -->
              <li class="nav-item nav-item-gateways <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'gateways', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Gateways','<?php echo $site_url.$path_admin ?>/gateways','nav-item-gateways')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wallet"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg></span>
                  <span class="nav-link-title ms-2">Gateways</span>
                </a>
              </li>

              <!-- Customers -->
              <li class="nav-item nav-item-customers <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'customers', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Customers','<?php echo $site_url.$path_admin ?>/customers','nav-item-customers')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg></span>
                  <span class="nav-link-title ms-2">Customers</span>
                </a>
              </li>

              <!-- Transaction -->
              <li class="nav-item nav-item-transaction <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Transaction','<?php echo $site_url.$path_admin ?>/transaction','nav-item-transaction')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-receipt"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2" /></svg></span>
                  <span class="nav-link-title ms-2">Transaction</span>
                  <?php
                      $count = 0;
                      $response_dashboard_info = json_decode(getData($db_prefix.'transaction',' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status = "pending"'),true);
                      if($response_dashboard_info['status'] == true){
                          foreach($response_dashboard_info['response'] as $row){
                              $count++;
                          }
                      }
                  ?>
                  <span class="badge bg-danger rounded-pill <?= ($count == 0) ? 'd-none' : '' ?> ms-auto text-white"><?php echo number_format($count, 0);?></span>
                </a>
              </li>

              <!-- Invoice -->
              <li class="nav-item nav-item-invoice <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'invoice', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Invoice','<?php echo $site_url.$path_admin ?>/invoice','nav-item-invoice')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-invoice"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25" /></svg></span>
                  <span class="nav-link-title ms-2">Invoice</span>
                </a>
              </li>

              <!-- Payment Link -->
              <li class="nav-item nav-item-payment-link <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Payment Link','<?php echo $site_url.$path_admin ?>/payment-link','nav-item-payment-link')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg></span>
                  <span class="nav-link-title ms-2">Payment Link</span>
                </a>
              </li>

              <!-- Appearance Heading -->
              <li class="card-title pt-3">Appearance</li>

              <!-- Brand Settings -->
              <li class="nav-item nav-item-brand-setting <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Brand Settings','<?php echo $site_url.$path_admin ?>/brand-setting','nav-item-brand-setting')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-cog"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21h9" /><path d="M9 8h1" /><path d="M9 12h1" /><path d="M9 16h1" /><path d="M14 8h1" /><path d="M14 12h1" /><path d="M5 21v-16c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h10c.53 0 1.039 .211 1.414 .586c.375 .375 .586 .884 .586 1.414v7" /><path d="M16 18c0 .53 .211 1.039 .586 1.414c.375 .375 .884 .586 1.414 .586c.53 0 1.039 -.211 1.414 -.586c.375 -.375 .586 -.884 .586 -1.414c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586c-.53 0 -1.039 .211 -1.414 .586c-.375 .375 -.586 .884 -.586 1.414z" /><path d="M18 14.5v1.5" /><path d="M18 20v1.5" /><path d="M21.032 16.25l-1.299 .75" /><path d="M16.27 19l-1.3 .75" /><path d="M14.97 16.25l1.3 .75" /><path d="M19.733 19l1.3 .75" /></svg></span>
                  <span class="nav-link-title ms-2">Brand Settings</span>
                </a>
              </li>

              <!-- MFS Automation Heading -->
              <li class="card-title pt-3">MFS Automation</li>

              <!-- SMS Data -->
              <li class="nav-item nav-item-sms-data <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'sms_data', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('SMS Data','<?php echo $site_url.$path_admin ?>/sms-data','nav-item-sms-data')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cloud-computing"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.657 16c-2.572 0 -4.657 -2.007 -4.657 -4.483c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 1c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.913 0 3.464 1.56 3.464 3.486c0 1.927 -1.551 3.487 -3.465 3.487h-11.878" /><path d="M12 16v5" /><path d="M16 16v4a1 1 0 0 0 1 1h4" /><path d="M8 16v4a1 1 0 0 1 -1 1h-4" /></svg></span>
                  <span class="nav-link-title ms-2">SMS Data</span>
                </a>
              </li>

              <!-- Devices -->
              <li class="nav-item nav-item-devices <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'device', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Devices','<?php echo $site_url.$path_admin ?>/devices','nav-item-devices')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-mobile"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-14z" /><path d="M11 4h2" /><path d="M12 17v.01" /></svg></span>
                  <span class="nav-link-title ms-2">Devices</span>
                </a>
              </li>

              <!-- Administration Heading -->
              <li class="card-title pt-3">Administration</li>
              
              <!-- Addons -->
              <li class="nav-item nav-item-addons <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'addons', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Addons','<?php echo $site_url.$path_admin ?>/addons','nav-item-addons')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-puzzle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7h3a1 1 0 0 0 1 -1v-1a2 2 0 0 1 4 0v1a1 1 0 0 0 1 1h3a1 1 0 0 1 1 1v3a1 1 0 0 0 1 1h1a2 2 0 0 1 0 4h-1a1 1 0 0 0 -1 1v3a1 1 0 0 1 -1 1h-3a1 1 0 0 1 -1 -1v-1a2 2 0 0 0 -4 0v1a1 1 0 0 1 -1 1h-3a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1h1a2 2 0 0 0 0 -4h-1a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1" /></svg></span>
                  <span class="nav-link-title ms-2">Addons</span>
                </a>
              </li>

              <!-- Domains -->
              <li class="nav-item nav-item-domains <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'domains', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Domains','<?php echo $site_url.$path_admin ?>/domains','nav-item-domains')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-world-www"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.5 7a9 9 0 0 0 -7.5 -4a8.991 8.991 0 0 0 -7.484 4" /><path d="M11.5 3a16.989 16.989 0 0 0 -1.826 4" /><path d="M12.5 3a16.989 16.989 0 0 1 1.828 4" /><path d="M19.5 17a9 9 0 0 1 -7.5 4a8.991 8.991 0 0 1 -7.484 -4" /><path d="M11.5 21a16.989 16.989 0 0 1 -1.826 -4" /><path d="M12.5 21a16.989 16.989 0 0 0 1.828 -4" /><path d="M2 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M17 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M9.5 10l1 4l1.5 -4l1.5 4l1 -4" /></svg></span>
                  <span class="nav-link-title ms-2">Domains</span>
                </a>
              </li>

              <!-- Brands -->
              <li class="nav-item nav-item-brands <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'brands', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Brands','<?php echo $site_url.$path_admin ?>/brands','nav-item-brands')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-store"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" /><path d="M5 21l0 -10.15" /><path d="M19 21l0 -10.15" /><path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" /></svg></span>
                  <span class="nav-link-title ms-2">All Brands</span>
                </a>
              </li>

              <!-- Staff Management -->
              <li class="nav-item nav-item-staff-management <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'staff_management', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('Staff Management','<?php echo $site_url.$path_admin ?>/staff-management','nav-item-staff-management')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-password-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17v4" /><path d="M10 20l4 -2" /><path d="M10 18l4 2" /><path d="M5 17v4" /><path d="M3 20l4 -2" /><path d="M3 18l4 2" /><path d="M19 17v4" /><path d="M17 20l4 -2" /><path d="M17 18l4 2" /><path d="M9 6a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M7 14a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2" /></svg></span>
                  <span class="nav-link-title ms-2">Staff Management</span>
                </a>
              </li>

              <!-- System Settings -->
              <li class="nav-item nav-item-system-settings <?= canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'system_settings', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" 
                  onclick="load_content('System Settings','<?php echo $site_url.$path_admin ?>/system-settings','nav-item-system-settings')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg></span>
                  <span class="nav-link-title ms-2">System Settings</span>
                </a>
              </li>

              <!-- Activities -->
              <li class="nav-item nav-item-activities" onclick="load_content('Activities','<?php echo $site_url.$path_admin ?>/activities','nav-item-activities')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-activity"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12h4l3 8l4 -16l3 8h4"></path></svg></span>
                  <span class="nav-link-title ms-2">Activities</span>
                </a>
              </li>

              <!-- Activities -->
              <li class="nav-item" onclick="window.open('https://pg.eps.com.bd/DefaultPaymentLink?id=F3CBE8E6', '_blank')">
                <a href="javascript:void(0)" class="nav-link d-flex align-items-center rounded">
                  <span class="nav-link-icon d-inline-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tip-jar-pound"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 4v1.882c0 .685 .387 1.312 1 1.618s1 .933 1 1.618v8.882a3 3 0 0 1 -3 3h-8a3 3 0 0 1 -3 -3v-8.882c0 -.685 .387 -1.312 1 -1.618s1 -.933 1 -1.618v-1.882" /><path d="M6 4h12l-12 0" /><path d="M14 10h-1a2 2 0 0 0 -2 2v2c0 1.105 -.395 2 -1.5 2h4.5" /><path d="M10 13h3" /></svg></span>
                  <span class="nav-link-title ms-2">Donate Us</span>
                </a>
              </li>

            </ul>
          </div>
        </div>

        <div class="page-wrapper">
            <div class="root-print" style="max-width: 1200px; width: 100%; margin: auto; margin-top: 0px;">
                <center><div class="spinner-border text-primary" style="margin-top: 150px;">  <span class="visually-hidden">Loading...</span></div></center>
            </div>

            <footer class="footer footer-transparent d-print-none" style="max-width: 1200px; width: 100%; margin: auto; margin-top: 0px;">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item"><a href="https://github.com/profess0r-null/profess0rpay#readme" target="_blank" class="link-secondary" rel="noopener">Documentation</a></li>
                                <li class="list-inline-item"><a href="https://github.com/profess0r-null/profess0rpay" target="_blank" class="link-secondary">Modules</a></li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    © <?php echo date('Y');?>
                                    <a href="https://github.com/profess0r-null/profess0rpay" class="link-secondary" target="_blank">Profess0rPay</a>. All rights reserved.
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://github.com/profess0r-null/profess0rpay/releases/tag/<?php echo $professorpay_current_version['version_name'];?>" class="link-secondary" target="_blank"><?php echo $professorpay_current_version['version_name'];?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </div>


    <div class="modal fade" id="model-my-two-step-verify" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scrollableLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scrollableLabel">Two Step Verify</h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"> 
                    <p>To perform this action, you need to complete 2-step verification to prevent unauthorized access.</p>        

                    <input type="hidden" id="my-two-step-verify-btn">

                    <?php
                        if($global_user_response['response'][0]['2fa_status'] == "enable"){
                    ?>
                            <div class="form-group mt-2">
                                <label for="my-two-step-verify-code" class="form-label">Enter the 6-digit code from the authenticator app <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="my-two-step-verify-code" name="my-two-step-verify-code" placeholder="Enter code" required>
                                </div>
                            </div>
                    <?php
                        }else{
                    ?>
                            <div class="form-group mt-1">
                                <label for="my-two-step-verify-code" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="password" class="form-control" id="my-two-step-verify-code" name="my-two-step-verify-code" placeholder="Password" required>
                                </div>
                            </div>
                    <?php
                        }
                    ?>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="model-my-two-step-verify-btn" onclick="two_step_verify_tab_btn()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="model-my-action-confirmation" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scrollableLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title model-my-action-confirmation-btn-title" id="scrollableLabel"></h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"> 
                    <p>Are you sure you would like to do this?</p>             

                    <input type="hidden" id="my-action-confirmation-btn">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="model-my-action-confirmation-btn" onclick="my_action_confirmation_btn()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $site_url ?>assets/js/tabler.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/jquery-3.6.4.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/custom-toast.js?v=1.2"></script>
    <script src="<?php echo $site_url ?>assets/js/apexcharts.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/choices.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hugerte@1/hugerte.min.js"></script>
    
    <input type="hidden" name="csrf_token_default" value="<?= $csrf_token; ?>">
    
    <script data-cfasync="false">
        //all declaration#
        let chartTransactionStatistics = null;
        let chartGatewayStatistics = null;
        window.InvoiceCustomerChoices = null;

        (function () {
            const choicesInstances = new Map();

            window.initChoices = function (selector = '.js-select') {
                document.querySelectorAll(selector).forEach(select => {

                    // Prevent double init
                    if (choicesInstances.has(select)) return;

                    const isMultiple = select.hasAttribute('multiple');

                    const instance = new Choices(select, {
                        removeItemButton: select.dataset.remove === 'true' && isMultiple,
                        searchEnabled: select.dataset.search !== 'false',
                        shouldSort: false,
                        placeholder: true,
                        placeholderValue: select.dataset.placeholder || 'Select option',
                        searchPlaceholderValue: 'Search...',
                        allowHTML: false,
                    });

                    choicesInstances.set(select, instance);
                });
            };

            document.addEventListener('DOMContentLoaded', () => initChoices());
        })();

        function initInvoiceCustomer() {
            const el = document.querySelector('.customersList');
            if (!el) return;

            // ✅ already initialized? then STOP
            if (el.dataset.choicesInitialized === '1') return;

            window.InvoiceCustomerChoices = new Choices(el, {
                removeItemButton: true,
                searchEnabled: true,
                shouldSort: false,
            });

            el.dataset.choicesInitialized = '1';
        }

        function initTags() {
            const tagInputs = document.querySelectorAll('.js-tags');

            tagInputs.forEach(input => {

                // ✅ Prevent duplicate initialization
                if (input.dataset.tagsInitialized === "1") return;
                input.dataset.tagsInitialized = "1";

                let tags = [];

                // Read existing value
                if (input.value.trim() !== '') {
                    tags = input.value.split(',').map(t => t.trim()).filter(Boolean);
                }

                // Create container
                const container = document.createElement('div');
                container.className = 'tag-container d-flex flex-wrap gap-2';
                input.parentNode.insertBefore(container, input);
                container.appendChild(input);

                // Create hidden input ONLY ONCE
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = input.id; // or input.name
                container.appendChild(hidden);

                input.removeAttribute('name');
                input.value = '';

                renderTags();

                // Add tag on Enter
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();

                        const value = input.value.trim();
                        if (!value || tags.includes(value)) return;

                        tags.push(value);
                        input.value = '';
                        renderTags();
                    }
                });

                function renderTags() {
                    container.querySelectorAll('.tag-item').forEach(tag => tag.remove());

                    tags.forEach((tag, index) => {
                        const tagEl = document.createElement('span');
                        tagEl.className = 'badge bg-primary tag-item text-white d-flex align-items-center';
                        tagEl.style.fontWeight = '400';
                        tagEl.innerHTML = `
                            ${tag}
                            <span class="ms-2 cursor-pointer" data-index="${index}">×</span>
                        `;
                        container.insertBefore(tagEl, input);
                    });

                    hidden.value = tags.join(',');
                }

                // Remove tag
                container.addEventListener('click', function (e) {
                    if (e.target.dataset.index !== undefined) {
                        tags.splice(e.target.dataset.index, 1);
                        renderTags();
                    }
                });
            });
        }

        var myModalElTWOSTEPVERIFY = document.getElementById('model-my-action-confirmation');

        myModalElTWOSTEPVERIFY.addEventListener('hidden.bs.modal', function () {
            document.querySelector("#my-action-confirmation-btn").value = '';
        });

        function show_action_confirmation_tab(btnClass, title, btnTitle, btnColor) {
            var myModalEl = document.getElementById('model-my-action-confirmation');

            closeAllBootstrapModals();

            document.querySelector(".model-my-action-confirmation-btn-title").innerHTML = title;
            document.querySelector("#model-my-action-confirmation-btn").innerHTML = btnTitle;

            const btnClasss = document.getElementById('model-my-action-confirmation-btn');

            const keepClasses = ['btn', 'btn-sm'];

            btnClasss.classList.forEach(cls => {
                if (!keepClasses.includes(cls)) {
                    btnClasss.classList.remove(cls);
                }
            });

            document.querySelector("#model-my-action-confirmation-btn").classList.add(btnColor);

            var button = document.getElementById('model-my-action-confirmation-btn');

            document.querySelector("#my-action-confirmation-btn").value = '.'+btnClass;

            $('#model-my-action-confirmation').modal('show');
        }

        function my_action_confirmation_btn(){
            var btnClass = document.querySelector("#my-action-confirmation-btn").value;

            document.querySelector(btnClass).click();
            document.querySelector("#my-action-confirmation-btn").value = '';
        }

        var myModalElTWOSTEPVERIFY = document.getElementById('model-my-two-step-verify');

        myModalElTWOSTEPVERIFY.addEventListener('hidden.bs.modal', function () {
            document.querySelector("#my-two-step-verify-code").value = '';
        });

        function copyContent(content, title, description) {
            if (!content) {
                // Show error if URL is empty
                createToast({
                    title: 'Error!',
                    description: 'No content provided to copy.',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
                return;
            }

            const copySuccess = () => {
                createToast({
                    title: title,
                    description: description,
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                    timeout: 4000,
                    top: 70
                });
            };

            const copyFail = (err) => {
                createToast({
                    title: 'Failed!',
                    description: 'Unable to copy the content. Please try manually.',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
                console.error('Clipboard error:', err);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(content).then(copySuccess).catch(copyFail);
            } else {
                const textArea = document.createElement("textarea");
                textArea.value = content;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    copySuccess();
                } catch (err) {
                    copyFail(err);
                }
                document.body.removeChild(textArea);
            }
        }

        function show_two_step_verify_tab(btnClass) {
            var myModalEl = document.getElementById('model-my-two-step-verify');

            if (myModalEl && myModalEl.classList.contains('show')) {
                var my_two_step_verify_code = document.querySelector("#my-two-step-verify-code").value;

                if(my_two_step_verify_code == ""){
                    document.querySelector("#my-two-step-verify-code").reportValidity();
                }
            }else{
                closeAllBootstrapModals();

                var button = document.getElementById('model-my-two-step-verify-btn');

                document.querySelector("#my-two-step-verify-btn").value = '.'+btnClass;
                document.querySelector("#my-two-step-verify-code").value = '';

                $('#model-my-two-step-verify').modal('show');
            }
        }

        function closeAllBootstrapModals() {
            $('.modal.show').each(function() {
                $(this).modal('hide');
            });
        }

        function two_step_verify_tab_btn(){
            var btnClass = document.querySelector("#my-two-step-verify-btn").value;

            document.querySelector(btnClass).click();
        }

        function isMobileDevice() {
            return window.innerWidth <= 768; 
        }

        function filter_hide_show(tab){
            var element = document.querySelector('.'+tab);

            if (element.classList.contains('d-none')) {
                element.classList.remove('d-none');
            } else {
                element.classList.add('d-none');
            }
        }

        function showProgress() {
            const progress = document.getElementById('topProgress');
            const bar = progress.querySelector('.progress-bar');

            progress.classList.remove('d-none');
            bar.style.width = '30%';

            setTimeout(() => bar.style.width = '60%', 200);
            setTimeout(() => bar.style.width = '85%', 400);
        }

        function hideProgress() {
            const progress = document.getElementById('topProgress');
            const bar = progress.querySelector('.progress-bar');

            bar.style.width = '100%';

            setTimeout(() => {
                progress.classList.add('d-none');
                bar.style.width = '0%';
            }, 300);
        }

        function set_brand(brand_id){
            var csrf_token_default = $('input[name="csrf_token_default"]').val();

            if (isMobileDevice()) {
                const sidebar = document.getElementById('sidebarMenu');

                if (sidebar && sidebar.classList.contains('offcanvas-md') && sidebar.classList.contains('offcanvas-start') && sidebar.classList.contains('sidebar') && sidebar.classList.contains('show')) {
                    const toggleBtn = document.querySelector('.navbar-toggler');
                    if (toggleBtn) toggleBtn.click();
                }
            }

            showProgress();

            $.ajax({
                type: 'POST',
                url: '<?php echo $site_url.$path_admin ?>/dashboard',
                data: {action: "set-default-brand", brand_id: brand_id, csrf_token: csrf_token_default},
                dataType: 'json',
                success: function (response) {
                    $('input[name="csrf_token_default"]').val(response.csrf_token);
                    
                    document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                        input.value = response.csrf_token;
                    });

                    if (response.status === 'true') {
                        location.reload();
                    } else {
                        hideProgress();

                        createToast({
                            title: response.title,
                            description: response.message,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000,
                            top: 70
                        });
                    }
                },
                error: function (xhr, status, error) {
                    hideProgress();
                    
                    createToast({
                        title: 'Something Wrong!',
                        description: 'For further assistance, please contact our support team.',
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });
                }
            });
        }

        function initHugeRTE(selector = '.hugerte-textArea') {
            // Get all textarea elements with the class
            const elements = document.querySelectorAll(selector);

            elements.forEach(el => {
                // Avoid initializing twice
                if (!el.dataset.hugerteInitialized) {
                    let options = {
                        target: el, // Initialize directly on the element
                        height: 250,
                        menubar: false,
                        statusbar: false,
                        plugins: [
                            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
                            'searchreplace', 'visualblocks', 'code', 'fullscreen',
                            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
                        ],
                        toolbar: 'undo redo | formatselect | ' +
                                'bold italic backcolor | alignleft aligncenter ' +
                                'alignright alignjustify | bullist numlist outdent indent | ' +
                                'removeformat',
                        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }'
                    };

                    // Dark mode support
                    if (localStorage.getItem("tablerTheme") === 'dark') {
                        options.skin = 'oxide-dark';
                        options.content_css = 'dark';
                    }

                    hugeRTE.init(options);

                    // Mark as initialized
                    el.dataset.hugerteInitialized = 'true';
                }
            });
        }

        // Run it on all .hugerte-textArea textareas
        initHugeRTE();

        function getAdminPath(url) {
            let cleanUrl = url.split('?')[0];
            let adminBase = '<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>';
            
            if (cleanUrl === adminBase || cleanUrl === adminBase + '/') {
                return 'dashboard';
            }
            
            let index = cleanUrl.indexOf('<?php echo $path_admin?>/');
            if (index === -1) return '';
            
            return cleanUrl.substring(index + '<?php echo $path_admin?>/'.length).replace(/^\/+/, '');
        }

        function getQueryParams(url) {
            const params = {};
            const queryString = url.split('?')[1];
            if (!queryString) return params;

            const searchParams = new URLSearchParams(queryString);
            for (const [key, value] of searchParams.entries()) {
                params[key] = value === '' ? true : value;
            }
            return params;
        }

        function initToolTips(){
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
                const existing = tabler.bootstrap.Tooltip.getInstance(el);
                if (existing) {
                    existing.dispose();
                }

                const config = {
                    html: el.dataset.bsHtml === 'true',
                    placement: el.dataset.bsPlacement || 'top',
                    trigger: el.dataset.bsTrigger || 'hover focus',
                    container: 'body',
                    delay: {
                        show: 100,
                        hide: 100
                    }
                };

                if (el.dataset.bsDelay) {
                    const delay = parseInt(el.dataset.bsDelay, 10);
                    if (!isNaN(delay)) {
                        config.delay = { show: delay, hide: delay };
                    }
                }

                new tabler.bootstrap.Tooltip(el, config);
            });
        }

        function load_content(page, url, nav_id, fromPopState = false) {
            const cleanPath = getAdminPath(url);
            const queryParams = getQueryParams(url);

            showProgress();
            
            if (isMobileDevice()) {
                const sidebar = document.getElementById('sidebarMenu');

                if (sidebar && sidebar.classList.contains('offcanvas-md') && sidebar.classList.contains('offcanvas-start') && sidebar.classList.contains('sidebar') && sidebar.classList.contains('show')) {
                    const toggleBtn = document.querySelector('.navbar-toggler');
                    if (toggleBtn) toggleBtn.click();
                }
            }

            fetch('<?php echo $site_url ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    root: cleanPath,
                    params: JSON.stringify(queryParams)
                })
            })
            .then(res => res.text())
            .then(html => {
                $('.root-print').html(html);

                initHugeRTE();

                initInvoiceCustomer();

                initToolTips();

                initChoices();             
                initChoices('.js-select');

                initTags();

                hideProgress();
                
                if (!fromPopState) {
                    history.pushState({ 
                        page: page, 
                        path: url, 
                        nav_id: nav_id 
                    }, "", url);
                }
            })
            .catch(error => {
                hideProgress();
                console.error('Error:', error);
            });

            document.querySelectorAll('#sidebarMenu .nav-link').forEach(link => {
              link.classList.remove('active');
            });
            const activeLink = document.querySelector('#sidebarMenu .' + nav_id + ' .nav-link');
            if (activeLink) {
                activeLink.classList.add('active');
            }

            document.title = page + ' - Profess0rPay';
        }

        window.addEventListener("popstate", function(event) {
            if (event.state) {
                load_content(event.state.page, event.state.path, event.state.nav_id, true);
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            let currentUrlV = window.location.href;
            let baseUrl = '<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>';

            var currentUrl = currentUrlV;
            if (currentUrlV.split('?')[0] === baseUrl || currentUrlV.split('?')[0] === baseUrl + '/') {
                currentUrl = baseUrl + '/dashboard' + (window.location.search || '');
            }

            const cleanPath = getAdminPath(currentUrl);

            let pageTitle = cleanPath.split('/').map(segment => segment.replace(/-/g, ' ').replace(/\b\w/g, char => char.toUpperCase())).join(' - ') || 'Dashboard';

            let nav_id = 'nav-item-' + (cleanPath.split('/')[0] || 'dashboard');

            load_content(pageTitle, currentUrl, nav_id);

            // Notification System Fetch
            fetchNotifications();
            setInterval(fetchNotifications, 10000);
        });

        function fetchNotifications() {
            fetch('<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/notifications_ajax?action=get_unread')
            .then(res => res.text())
            .then(text => {
                let data = {status: false, count: 0, data: []};
                try {
                    data = JSON.parse(text);
                } catch(e) {
                    console.error("Notifications Parse Error. Server Response:", text);
                }
                
                let badge = document.getElementById('notification-badge');
                let list = document.getElementById('notification-list');
                
                if (data.status && data.data && data.data.length > 0) {
                    if (data.count > 0) {
                        badge.classList.remove('d-none');
                        badge.innerText = data.count > 99 ? '99+' : data.count;
                    } else {
                        badge.classList.add('d-none');
                    }
                    
                    data.data.sort((a, b) => {
                        if (a.is_read != b.is_read) {
                            return a.is_read - b.is_read;
                        }
                        return parseInt(b.id) - parseInt(a.id);
                    });

                    let html = '';
                    data.data.forEach(n => {
                        let iconSvg = '';
                        if (n.type === 'success') {
                            iconSvg = `<div class="bg-green-lt avatar avatar-sm rounded"><svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg></div>`;
                        } else if (n.type === 'warning') {
                            iconSvg = `<div class="bg-yellow-lt avatar avatar-sm rounded"><svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg></div>`;
                        } else if (n.type === 'danger') {
                            iconSvg = `<div class="bg-red-lt avatar avatar-sm rounded"><svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg></div>`;
                        } else {
                            iconSvg = `<div class="bg-blue-lt avatar avatar-sm rounded"><svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7.5" /><path d="M3 6l9 6l9 -6" /><path d="M15 18h6" /><path d="M18 15l3 3l-3 3" /></svg></div>`;
                        }
                        
                        let actionUrl = '#';
                        if(n.title === 'Payment Pending Review') actionUrl = '<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/transaction?status=pending';
                        else if(n.title === 'Payment Successful' || n.title === 'New Payment Initiated' || n.title === 'Webhook Failed') actionUrl = '<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/transaction';
                        else if(n.title === 'Security Alert') actionUrl = '<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/activities';
                        else if(n.title === 'Device Offline Alert' || n.title === 'Device Offline') actionUrl = '<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/devices';
                        else if(n.title === 'System Update Available Alert' || n.title === 'System Update Available') actionUrl = '<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/system-update';

                        let unreadBg = (n.is_read == 0) ? 'bg-muted-lt' : '';
                        let unreadBadge = (n.is_read == 0) ? '<span class="badge bg-red p-1 flex-shrink-0" style="border-radius: 50%;"></span>' : '';
                        
                        html += `
                        <a href="#" onclick="markSingleNotificationRead('${n.id}', '${n.title}', '${actionUrl}')" class="list-group-item list-group-item-action text-decoration-none ${unreadBg}">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    ${iconSvg}
                                </div>
                                <div class="col" style="min-width: 0;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="d-flex align-items-center flex-grow-1" style="min-width: 0;">
                                            <span class="text-body fw-bold text-truncate me-2">${n.title}</span>
                                            ${unreadBadge}
                                        </div>
                                        <span class="text-muted small ms-2 flex-shrink-0">${n.time_formatted}</span>
                                    </div>
                                    <div class="d-block text-secondary small" style="line-height:1.4; word-wrap:break-word; white-space:normal;">${n.message}</div>
                                </div>
                            </div>
                        </a>`;
                    });
                    list.innerHTML = html;
                } else {
                    badge.classList.add('d-none');
                    list.innerHTML = '<div class="list-group-item text-center text-muted py-4">No new notifications</div>';
                }
            });
        }
        function markSingleNotificationRead(id, title, url) {
            fetch('<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/notifications_ajax?action=mark_read&id=' + id)
            .then(() => fetchNotifications());
            
            if (url !== '#') {
                load_content(title, url, '');
            }
        }

        function markAllNotificationsRead(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Hide tooltip properly
                let tooltipEl = e.currentTarget;
                if (tooltipEl && typeof bootstrap !== 'undefined') {
                    let tooltip = bootstrap.Tooltip.getInstance(tooltipEl);
                    if (tooltip) tooltip.hide();
                }
            }
            // Fallback to clear any orphaned tooltips
            document.querySelectorAll('.tooltip').forEach(t => t.remove());

            fetch('<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/notifications_ajax?action=mark_read&id=all')
            .then(() => fetchNotifications());
        }
        function deleteAllNotifications(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            fetch('<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/notifications_ajax?action=clear_all')
            .then(() => {
                fetchNotifications();
            });
        }
    </script>
    <div class="modal modal-blur fade" id="modal-clear-notifications" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="modal-status bg-danger"></div>
          <div class="modal-body text-center py-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
            <h3>Are you sure?</h3>
            <div class="text-muted">Do you really want to clear all notifications? What you've done cannot be undone.</div>
          </div>
          <div class="modal-footer">
            <div class="w-100">
              <div class="row">
                <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">Cancel</a></div>
                <div class="col"><button class="btn btn-danger w-100" data-bs-dismiss="modal" onclick="deleteAllNotifications(event)">Delete</button></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</body>
</html>
