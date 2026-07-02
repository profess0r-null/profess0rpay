<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="QubePlug Bangladesh">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Requirement - Profess0rPay</title>
    <link rel="shortcut icon" href="<?= $professorpay_favicon ?? '--' ?>">

    <link rel="stylesheet" href="<?php echo $site_url ?>assets/css/tabler.min.css?v=1.5" />
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
    </style>
</head>
<body>
    <div class="container p-2 p-sm-4">
        <div class="text-center mb-5">
            <div class="brand-logo mb-1">
                <a href="#" class="logo-link">
                    <div class="logo-wrap">
                        <img src="<?= $professorpay_logo_light ?? '' ?>" alt="" style=" height: 40px; ">
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-5 mx-auto all-pages">
            <!-- Page 1: Requirements Check -->
            <div class="card active" id="page1">
                <div class="card-header d-grid">
                    <h3 class="card-title mb-1">System Requirements Check</h3>
                    <p class="card-subtitle">Please wait while we check your server requirements.</p>
                </div>

                <div class="card-body">
                    <div class="requirements-grid">
                        <div class="requirement-groups">
                            <div id="phpRequirements">
                                <?php
                                    $satisfied_btn = true;

                                    foreach ($requirements as $req) {

                                        if (!$req['check']) {
                                            $satisfied_btn = false;
                                        }

                                        // Set status classes and icons
                                        $statusClass = $req['check'] ? 'text-success' : 'text-danger';
                                        $statusIcon  = $req['check'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; // using Bootstrap Icons
                                        $statusText  = $req['check'] ? 'Passed' : 'Failed';

                                    ?>
                                        <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
                                            <div>
                                                <strong><?= $req['name'] ?></strong>
                                                <div class="small text-muted">
                                                    Required: <?= $req['required'] ?> | Current: <?= $req['current'] ?>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="<?= $statusIcon ?> <?= $statusClass ?>" style="font-size: 1.25rem;"></i>
                                                <span class="<?= $statusClass ?> fw-bold"><?= $statusText ?></span>
                                            </div>
                                        </div>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="<?php echo $site_url ?>assets/js/tabler.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/jquery-3.6.4.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/custom-toast.js?v=1.2"></script>
</body>
</html>
