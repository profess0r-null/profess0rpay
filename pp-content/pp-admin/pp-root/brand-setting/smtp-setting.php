<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', 'view', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
?>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">
                    <ol class="breadcrumb breadcrumb-arrow mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Brand Settings','<?php echo $site_url.$path_admin ?>/brand-setting','nav-item-brand-setting')">Brand Settings</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">SMTP Settings</a></li>
                    </ol>
                </div>
                <h2 class="page-title">SMTP Settings</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <form action="" class="form-smtp-setting" enctype="multipart/form-data">
                <input type="hidden" name="action" value="smtp-setting">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">SMTP Configuration</h3>
                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal-smtp-docs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                   <circle cx="12" cy="12" r="9"></circle>
                                   <line x1="12" y1="17" x2="12" y2="17.01"></line>
                                   <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4"></path>
                                </svg>
                                Setup Guide
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Status</label>
                                    <select class="form-select" name="smtp_status">
                                        <option value="enabled" <?= get_env('smtp_status', $global_response_brand['response'][0]['brand_id']) == 'enabled' ? 'selected' : '' ?>>Enabled</option>
                                        <option value="disabled" <?= get_env('smtp_status', $global_response_brand['response'][0]['brand_id']) == 'disabled' ? 'selected' : '' ?>>Disabled</option>
                                    </select>
                                    <small class="form-hint">Enable SMTP to securely route emails through an external server. If disabled, defaults to native mail().</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Host</label>
                                    <input type="text" class="form-control" name="smtp_host" placeholder="e.g. smtp.gmail.com" value="<?= get_env('smtp_host', $global_response_brand['response'][0]['brand_id']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Port</label>
                                    <input type="number" class="form-control" name="smtp_port" placeholder="e.g. 465 or 587" value="<?= get_env('smtp_port', $global_response_brand['response'][0]['brand_id']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Encryption</label>
                                    <select class="form-select" name="smtp_encryption">
                                        <option value="tls" <?= get_env('smtp_encryption', $global_response_brand['response'][0]['brand_id']) == 'tls' ? 'selected' : '' ?>>TLS (Recommended for 587)</option>
                                        <option value="ssl" <?= get_env('smtp_encryption', $global_response_brand['response'][0]['brand_id']) == 'ssl' ? 'selected' : '' ?>>SSL (Recommended for 465)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Username</label>
                                    <input type="text" class="form-control" name="smtp_username" placeholder="e.g. your_email@gmail.com" value="<?= get_env('smtp_username', $global_response_brand['response'][0]['brand_id']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Password</label>
                                    <input type="password" class="form-control" name="smtp_password" placeholder="Enter SMTP password or App Password" value="<?= get_env('smtp_password', $global_response_brand['response'][0]['brand_id']); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', 'edit', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>">
                            <button type="submit" class="btn btn-primary btn-save-smtp">Save Settings</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('.form-smtp-setting').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let submitBtn = $('.btn-save-smtp');
    let originalText = submitBtn.text();

    $.ajax({
        type: 'POST',
        url: '<?php echo $site_url.$path_admin ?>/dashboard',
        data: form.serialize(),
        beforeSend: function () {
            submitBtn.text('Saving...').prop('disabled', true);
        },
        success: function (response) {
            let res = JSON.parse(response);
            if (res.status == 'true') {
                createToast({
                    title: res.title,
                    description: res.message,
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
                $("input[name='csrf_token']").val(res.csrf_token);
            } else {
                createToast({
                    title: res.title,
                    description: res.message,
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
                $("input[name='csrf_token']").val(res.csrf_token);
            }
        },
        error: function (xhr) {
            let errorMsg = 'Something went wrong. Please try again.';
            console.error('Server responded with:', xhr.status, xhr.responseText);
            try {
                let res = JSON.parse(xhr.responseText);
                if (res.title) errorMsg = res.title + ': ' + res.message;
            } catch (e) {}

            createToast({
                title: 'Error',
                description: errorMsg,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                timeout: 6000,
                top: 70
            });
        },
        complete: function () {
            submitBtn.text(originalText).prop('disabled', false);
        }
    });
});
</script>

<div class="modal modal-blur fade" id="modal-smtp-docs" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">📧 How to Setup SMTP (User Guide)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">
            <strong>SMTP কী এবং এর কাজ কী?</strong><br>
            SMTP (Simple Mail Transfer Protocol) হলো ইমেইল আদান-প্রদানের একটি প্রোটোকল। সার্ভার থেকে সরাসরি ইমেইল পাঠালে স্প্যামারদের কারণে জিমেইল তা অনেক সময় ব্লক করে দেয়। কিন্তু আপনি যদি আপনার নিজস্ব জিমেইল বা প্রফেশনাল ইমেইলের সিকিউর SMTP ব্যবহার করেন, তবে ইমেইলগুলো ১০০% ভেরিফায়েড হয়ে সরাসরি গ্রাহকের ইনবক্সে (Inbox) ডেলিভারি হবে।
        </p>
        <p class="mb-3">আপনার ওয়েবসাইটের ইমেইলগুলো যাতে স্প্যামে না গিয়ে সরাসরি গ্রাহকের ইনবক্সে যায়, সেজন্য নিচের যেকোনো একটি অপশন ব্যবহার করে SMTP কনফিগার করুন।</p>
        
        <h4 class="text-primary">Option 1: Gmail SMTP Setup (সবচেয়ে সহজ)</h4>
        <p>জিমেইল ব্যবহার করতে চাইলে আপনাকে আপনার জিমেইল একাউন্টের একটি <strong>App Password</strong> তৈরি করতে হবে (আপনার রেগুলার পাসওয়ার্ড দিলে কাজ করবে না)।</p>
        <ol>
            <li>আপনার জিমেইলের <strong>Manage your Google Account</strong> এ যান।</li>
            <li><strong>Security</strong> ট্যাবে গিয়ে <strong>2-Step Verification</strong> অন করুন।</li>
            <li>এরপর সার্চ বক্সে <strong>App Passwords</strong> লিখে সার্চ করুন।</li>
            <li>অ্যাপের নাম হিসেবে 'Profess0rPay' লিখে Create এ ক্লিক করুন। ১৬ অক্ষরের একটি স্পেশাল পাসওয়ার্ড পাবেন, সেটি কপি করুন।</li>
        </ol>
        <ul class="mb-4">
            <li><strong>SMTP Host:</strong> <code>smtp.gmail.com</code></li>
            <li><strong>SMTP Port:</strong> <code>465</code> (অথবা 587)</li>
            <li><strong>Encryption:</strong> <code>ssl</code> (অথবা tls)</li>
            <li><strong>SMTP Username:</strong> আপনার রেগুলার জিমেইল (যেমন: example@gmail.com)</li>
            <li><strong>SMTP Password:</strong> ওই ১৬ অক্ষরের App Password টি।</li>
        </ul>
        
        <hr>
        
        <h4 class="text-primary mt-4">Option 2: cPanel / Webmail SMTP Setup (প্রফেশনাল)</h4>
        <p>আপনি যদি আপনার ডোমেইনের নিজস্ব ইমেইল (যেমন: support@yourdomain.com) ব্যবহার করতে চান, তবে নিচের নিয়ম ফলো করুন:</p>
        <ol>
            <li>cPanel-এর <strong>Email Accounts</strong>-এ গিয়ে একটি ইমেইল ক্রিয়েট করুন।</li>
            <li>cPanel-এর <strong>Email Deliverability</strong>-তে গিয়ে আপনার ডোমেইনের <strong>SPF</strong>, <strong>DKIM</strong>, এবং <strong>DMARC</strong> রেকর্ডগুলো কপি করে আপনার ডোমেইনের DNS-এ (যেমন Namecheap/Cloudflare-এ) TXT রেকর্ড হিসেবে অ্যাড করুন। <em>(এটি না করলে মেইল স্প্যামে যাবে বা ব্লক হবে)</em>।</li>
            <li>cPanel-এর <strong>Email Routing</strong>-এ গিয়ে ডোমেইনটি সিলেক্ট করে <strong>Local Mail Exchanger</strong> সেভ করুন।</li>
        </ol>
        <ul>
            <li><strong>SMTP Host:</strong> <code>localhost</code> (যদি স্ক্রিপ্ট এবং মেইল একই সার্ভারে থাকে) অথবা <code>mail.yourdomain.com</code></li>
            <li><strong>SMTP Port:</strong> <code>465</code></li>
            <li><strong>Encryption:</strong> <code>ssl</code></li>
            <li><strong>SMTP Username:</strong> আপনার তৈরি করা ইমেইল (যেমন: support@yourdomain.com)</li>
            <li><strong>SMTP Password:</strong> ইমেইলটি খোলার সময় যে পাসওয়ার্ড দিয়েছিলেন সেটি।</li>
        </ul>
        <div class="alert alert-info mt-3" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <div>
                    <strong>নোট:</strong> Brand Settings-এর "Support Email" এর ঘরে অবশ্যই উপরের SMTP Username টি হুবহু বসাবেন, অন্যথায় জিমেইল মেইলগুলোকে স্প্যাম মনে করতে পারে।
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Understood</button>
      </div>
    </div>
  </div>
</div>
