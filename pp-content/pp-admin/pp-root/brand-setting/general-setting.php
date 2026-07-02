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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">General Settings</a></li>
                    </ol>
                </div>
                <h2 class="page-title">General Settings</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <form action="" class="form-general-setting" enctype="multipart/form-data">
                <input type="hidden" name="action" value="general-setting">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                <div class="col-12 mb-2 d-flex justify-content-center">
                    <div>
                        <div class="card p-2">
                            <ul class="nav nav-pills gap-2" role="tablist" id="statusTabs" style="font-weight: 500; font-size: .875rem;">
                                <li class="nav-item">
                                    <div class="nav-link active" style="cursor: pointer" data-type="general">
                                        General
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <div class="nav-link" style="cursor: pointer" data-type="business_details">
                                        Business Details
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <div class="nav-link" style="cursor: pointer" data-type="contact_social">
                                        Contact & Social
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12 tab-general">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Basic Information</h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="site_name" class="form-label">Site Name<span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo ($global_response_brand['response'][0]['name'] === '--' || $global_response_brand['response'][0]['name'] === '') ? $global_response_brand['response'][0]['identify_name'] : $global_response_brand['response'][0]['name'];?>" placeholder="Enter your site name" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="default_timezone" class="form-label">Default Timezone<span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <?php
                                                $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                            ?>
                                            <select class="js-select" name="default_timezone" data-search="true" data-remove="true" data-placeholder="Select timezone" required>
                                                <?php
                                                    $selectedTimezone = ($global_response_brand['response'][0]['timezone'] === '--' || $global_response_brand['response'][0]['timezone'] === '')
                                                        ? ''
                                                        : $global_response_brand['response'][0]['timezone'];
                                                ?>

                                                <?php foreach ($timezones as $tz): ?>
                                                    <option value="<?= $tz ?>" <?= ($tz === $selectedTimezone) ? 'selected' : '' ?>>
                                                        <?= $tz ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="default_language" class="form-label">Default Language<span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <?php
                                                $selectedLanguage = ($global_response_brand['response'][0]['language'] === '--' || $global_response_brand['response'][0]['language'] === '') 
                                                ? '' 
                                                : $global_response_brand['response'][0]['language'];
                                            ?>

                                            <select class="js-select" name="default_language" data-search="true" data-remove="true" data-placeholder="Select language" required>
                                                <option value="en" <?= ($selectedLanguage === 'en') ? 'selected' : '' ?>>English</option>
                                                <option value="bn" <?= ($selectedLanguage === 'bn') ? 'selected' : '' ?>>Bangla</option>
                                                <option value="hi" <?= ($selectedLanguage === 'hi') ? 'selected' : '' ?>>Hindi</option>
                                                <option value="ur" <?= ($selectedLanguage === 'ur') ? 'selected' : '' ?>>Urdu</option>
                                                <option value="ar" <?= ($selectedLanguage === 'ar') ? 'selected' : '' ?>>Arabic</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="default_currency" class="form-label">Default Currency<span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <select class="js-select" id="default_currency" name="default_currency" data-search="true" data-remove="true" data-placeholder="Select currency" required onchange="FNcurrency()">
                                                <?php
                                                    $selectedCurrency = ($global_response_brand['response'][0]['currency_code'] === '--' || $global_response_brand['response'][0]['currency_code'] === '') 
                                                        ? '' 
                                                        : $global_response_brand['response'][0]['currency_code'];

                                                    $response_brand = json_decode(
                                                        getData($db_prefix . 'currency', 'WHERE brand_id ="'.$global_response_brand['response'][0]['brand_id'].'" ORDER BY 1 DESC'), 
                                                        true
                                                    );

                                                    if ($response_brand['status'] == true) {
                                                        foreach ($response_brand['response'] as $row) {
                                                            $isSelected = ($row['code'] === $selectedCurrency) ? 'selected' : '';
                                                            echo '<option value="'.$row['code'].'" '.$isSelected.'>'.$row['code'].'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="payment_tolerance" class="form-label">Max Payment Tolerance<span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <div class="input-group">
                                                <span class="input-group-text payment_tolerance_currency"> <?php echo $selectedCurrency?> </span>
                                                <input type="number" class="form-control" id="payment_tolerance" name="payment_tolerance" value="<?php echo ($global_response_brand['response'][0]['payment_tolerance'] === '--' || $global_response_brand['response'][0]['payment_tolerance'] === '') ? $global_response_brand['response'][0]['payment_tolerance'] : $global_response_brand['response'][0]['payment_tolerance'];?>" placeholder="Enter payment tolerance" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Automatic Exchange Rates</label>
                                    <div class="form-control-wrap mb-2">
                                        <div class="input-group">
                                            <?php
                                                $autoExchange = ($global_response_brand['response'][0]['autoExchange'] === '--' || $global_response_brand['response'][0]['autoExchange'] === '') ? '' : $global_response_brand['response'][0]['autoExchange'];
                                                $checked = ($autoExchange === 'enabled') ? 'checked' : '';
                                            ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="autoExchange" value="enabled" <?= $checked ?>>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="form-hint">
                                        When enabled, exchange rates are automatically fetched from external providers. When disabled, you must manually configure rates in Currency Settings.
                                    </small>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="redirect_url" class="form-label">Custom Redirect URL (Optional)</label>
                                        <div class="form-control-wrap">
                                            <input type="url" class="form-control" id="redirect_url" name="redirect_url" value="<?php echo (isset($global_response_brand['response'][0]['redirect_url']) && $global_response_brand['response'][0]['redirect_url'] !== '--') ? $global_response_brand['response'][0]['redirect_url'] : ''; ?>" placeholder="https://yourwebsite.com/diamond-topup">
                                        </div>
                                        <small class="form-hint">
                                            If set, users will be automatically redirected to this URL when they cancel or complete a payment via Payment Links.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Logo & Favicon</h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="favicon" class="form-label">Favicon <svg xmlns="http://www.w3.org/2000/svg" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Photo size should in JPG, JPEG, PNG (270 x 97 pixels) format." style=" width: 20px; height: 20px; " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg></label>
                                        <div class="form-control-wrap">
                                            <input type="file" class="form-control img-input" id="favicon" name="favicon" data-preview="preview1" accept="image/*">
                                        </div>
                                    </div>

                                    <div class="border rounded p-2 mt-2 d-flex align-items-center justify-content-center" style=" height: 90px; width: 90px; ">
                                        <img src="<?php echo ($global_response_brand['response'][0]['favicon'] === '--' || $global_response_brand['response'][0]['favicon'] === '') ? $professorpay_favicon : $global_response_brand['response'][0]['favicon'];?>" accept="image/*" alt="" id="preview1">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="primary_logo" class="form-label">Primary Logo <svg xmlns="http://www.w3.org/2000/svg" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Photo size should in JPG, JPEG, PNG (512 x 512 pixels) format." style=" width: 20px; height: 20px; " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg></label>
                                        <div class="form-control-wrap">
                                            <input type="file" class="form-control img-input" id="primary_logo" name="primary_logo" data-preview="preview2" accept="image/*" style=" max-width: 100%; max-height: 100%; ">
                                        </div>
                                    </div>

                                    <div class="border rounded p-2 mt-2 d-flex align-items-center justify-content-center" style=" height: 90px; max-width: 300px; ">
                                        <img src="<?php echo ($global_response_brand['response'][0]['logo'] === '--' || $global_response_brand['response'][0]['logo'] === '') ? $professorpay_logo_light : $global_response_brand['response'][0]['logo'];?>" accept="image/*" alt="" id="preview2" style=" max-width: 100%; max-height: 100%; ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 tab-business_details" style="display: none">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Business Details</h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="street_address" class="form-label">Street Address</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="street_address" name="street_address" value="<?php echo ($global_response_brand['response'][0]['street_address'] === '--' || $global_response_brand['response'][0]['street_address'] === '') ? '' : $global_response_brand['response'][0]['street_address'];?>" placeholder="Enter your street address">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="city_town" class="form-label">City/Town</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="city_town" name="city_town" value="<?php echo ($global_response_brand['response'][0]['city_town'] === '--' || $global_response_brand['response'][0]['city_town'] === '') ? '' : $global_response_brand['response'][0]['city_town'];?>" placeholder="Enter your city/town">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="postal_code" class="form-label">Postal Code</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo ($global_response_brand['response'][0]['postal_code'] === '--' || $global_response_brand['response'][0]['postal_code'] === '') ? '' : $global_response_brand['response'][0]['postal_code'];?>" placeholder="Enter your postal code">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="country" class="form-label">Country</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="country" name="country" value="<?php echo ($global_response_brand['response'][0]['country'] === '--' || $global_response_brand['response'][0]['country'] === '') ? '' : $global_response_brand['response'][0]['country'];?>" placeholder="Enter your country">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 tab-contact_social" style="display: none">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Support Contact Information</h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="support_phone_number" class="form-label">Support Phone Number</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="support_phone_number" name="support_phone_number" value="<?php echo ($global_response_brand['response'][0]['support_phone_number'] === '--' || $global_response_brand['response'][0]['support_phone_number'] === '') ? '' : $global_response_brand['response'][0]['support_phone_number'];?>" placeholder="+1234567890">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="support_email_address" class="form-label">Support Email Address</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="support_email_address" name="support_email_address" value="<?php echo ($global_response_brand['response'][0]['support_email_address'] === '--' || $global_response_brand['response'][0]['support_email_address'] === '') ? '' : $global_response_brand['response'][0]['support_email_address'];?>" placeholder="support@yourdomain.com">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="support_website" class="form-label">Support Website</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="support_website" name="support_website" value="<?php echo ($global_response_brand['response'][0]['support_website'] === '--' || $global_response_brand['response'][0]['support_website'] === '') ? '' : $global_response_brand['response'][0]['support_website'];?>" placeholder="https://yoursite.com">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Social Media Profiles</h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="<?php echo ($global_response_brand['response'][0]['whatsapp_number'] === '--' || $global_response_brand['response'][0]['whatsapp_number'] === '') ? '' : $global_response_brand['response'][0]['whatsapp_number'];?>" placeholder="+1234567890">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="telegram" class="form-label">Telegram</label>

                                        <div class="input-group">
                                            <span class="input-group-text"> https://t.me/ </span>
                                            <input type="text" class="form-control" id="telegram" name="telegram" value="<?php echo ($global_response_brand['response'][0]['telegram'] === '--' || $global_response_brand['response'][0]['telegram'] === '') ? '' : $global_response_brand['response'][0]['telegram'];?>" placeholder="username">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="facebook_messenger" class="form-label">Facebook Messenger</label>

                                        <div class="input-group">
                                            <span class="input-group-text"> https://m.me/ </span>
                                            <input type="text" class="form-control" id="facebook_messenger" name="facebook_messenger" value="<?php echo ($global_response_brand['response'][0]['facebook_messenger'] === '--' || $global_response_brand['response'][0]['facebook_messenger'] === '') ? '' : $global_response_brand['response'][0]['facebook_messenger'];?>" placeholder="username">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="facebook_page" class="form-label">Facebook Page</label>

                                        <div class="input-group">
                                            <span class="input-group-text"> https://facebook.com/ </span>
                                            <input type="text" class="form-control" id="facebook_page" name="facebook_page" value="<?php echo ($global_response_brand['response'][0]['facebook_page'] === '--' || $global_response_brand['response'][0]['facebook_page'] === '') ? '' : $global_response_brand['response'][0]['facebook_page'];?>" placeholder="username">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end pt-3">
                    <button class="btn btn-primary btn-save-changes" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script data-cfasync="false">
    function FNcurrency(){
        var default_currency = document.querySelector("#default_currency").value;

        document.querySelector(".payment_tolerance_currency").innerHTML = default_currency;
    }

    document.querySelectorAll('#statusTabs .nav-link').forEach(btn => {
        btn.addEventListener('click', function () {

            document.querySelectorAll('#statusTabs .nav-link').forEach(b => b.classList.remove('active'));

            this.classList.add('active');

            const type = this.dataset.type;

            document.querySelector('.tab-general').style.display = 'none';
            document.querySelector('.tab-business_details').style.display = 'none';
            document.querySelector('.tab-contact_social').style.display = 'none';

            document.querySelector('.tab-'+type).style.display = 'block';
        });
    });

    function initImagePreview(selector, options = {}) {
        const settings = {
            maxSize: options.maxSize || 2 * 1024 * 1024, // 2MB
            allowedTypes: options.allowedTypes || ['image/jpeg', 'image/png'],
        };

        document.querySelectorAll(selector).forEach(input => {
            input.addEventListener('change', function () {
                const file = this.files[0];
                const previewId = this.dataset.preview;
                const preview = document.getElementById(previewId);

                if (!file || !preview) return;

                if (!settings.allowedTypes.includes(file.type)) {
                    createToast({
                        title: 'Action required!',
                        description: 'The selected file is not a supported image format.',
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });
                    this.value = '';
                    preview.style.display = 'none';
                    return;
                }

                if (file.size > settings.maxSize) {
                    createToast({
                        title: 'Action required!',
                        description: 'Image size exceeds the maximum allowed limit (Max: 2 MB).',
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });

                    this.value = '';
                    preview.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // Init once
    initImagePreview('.img-input', {
        maxSize: 2 * 1024 * 1024 // 2MB
    });

    $('.form-general-setting').submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        // Client-side validation
        $('input[type="file"]').each(function () {
            if (!this.files.length) return;

            let file = this.files[0];

            if (!file.type.startsWith('image/')) {
                createToast({
                    title: 'Action required!',
                    description: 'The selected file is not a supported image format.',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
                return false;
            }

            if (file.size > 2 * 1024 * 1024) {
                createToast({
                    title: 'Action required!',
                    description: 'Image size exceeds the maximum allowed limit (Max: 2 MB).',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
                return false;
            }
        });
        
        var btnClass = 'btn-save-changes';

        var btn = document.querySelector('.'+btnClass).innerHTML;

        document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        const autoExchange = document.getElementById('autoExchange').checked ? 'enabled' : 'disabled';
        formData.set('autoExchange', autoExchange); // add to formData

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: formData,
            contentType: false, // IMPORTANT
            processData: false, // IMPORTANT
            dataType: 'json',
            success: function (response) {
                closeAllBootstrapModals();

                document.querySelector('.'+btnClass).innerHTML = btn;

                document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                    input.value = response.csrf_token;
                });
                document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                    input.value = response.csrf_token;
                });

                if (response.status === 'true') {
                    createToast({
                        title: response.title,
                        description: response.message,
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });
                } else {
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
                createToast({
                    title: 'Something Wrong!',
                    description: 'For further assistance, please contact our support team.',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
            }
        });
    });
</script>