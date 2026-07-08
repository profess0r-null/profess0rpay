<?php
    class BinancePersonalGateway
    {
        public function info()
        {
            return [
                'title'        => 'Binance Personal',
                'logo'         => 'assets/logo.jpg',
                'currency'     => 'USDT',
                'tab'          => 'global',
                'gateway_type' => 'api',
            ];
        }

        public function color()
        {
            return [
                'primary_color'  => '#f0b90b',
                'text_color'     => '#000000',
                'btn_color'      => '#f0b90b',
                'btn_text_color' => '#000000',
            ];
        }

        public function fields()
        {
            return [
                ['name' => 'binance_uid',    'label' => 'Binance UID',  'type' => 'text'],
                ['name' => 'api_key',        'label' => 'Api Key',      'type' => 'text'],
                ['name' => 'secret_key',     'label' => 'Secret Key',   'type' => 'text'],
                ['name' => 'qr_code',        'label' => 'QR Code',      'type' => 'image'],
                ['name' => 'conversion_rate','label' => 'Conversion Rate — কত টাকায় ১ USDT? (e.g. 130 = 130 BDT per 1 USDT)', 'type' => 'text'],
            ];
        }

        public function supported_languages()
        {
            return ['en' => 'English', 'bn' => 'বাংলা', 'hi' => 'हिन्दी', 'ur' => 'اردو', 'ar' => 'العربية'];
        }

        public function lang_text()
        {
            return [
                '1'             => ['en' => 'Go to your Binance App or Website',            'bn' => 'আপনার Binance অ্যাপ বা ওয়েবসাইটে যান'],
                '2'             => ['en' => 'Choose "Send to Binance user"',                'bn' => '"Send to Binance user" নির্বাচন করুন'],
                '3'             => ['en' => 'Enter the Binance UID "{binance_uid}"',        'bn' => 'Binance UID লিখুন "{binance_uid}"'],
                '4'             => ['en' => 'Or scan the QR Code',                         'bn' => 'অথবা QR কোড স্ক্যান করুন'],
                '5'             => ['en' => 'Enter exact amount: {amount} USDT',            'bn' => 'সঠিক পরিমাণ লিখুন: {amount} USDT'],
                '6'             => ['en' => 'Confirm the transfer',                         'bn' => 'ট্রান্সফার নিশ্চিত করুন'],
                '7'             => ['en' => 'Enter the Order ID below and click Verify',    'bn' => 'নিচে Order ID লিখুন এবং Verify চাপুন'],
                'order_id'      => ['en' => 'Order ID',       'bn' => 'অর্ডার আইডি'],
                'enter_order_id'=> ['en' => 'Enter Binance Order ID', 'bn' => 'Binance Order ID লিখুন'],
                'verify'        => ['en' => 'Verify Payment', 'bn' => 'পেমেন্ট যাচাই করুন'],
            ];
        }

        public function instructions($data)
        {
            return [
                ['icon' => '', 'text' => '1', 'copy' => false],
                ['icon' => '', 'text' => '2', 'copy' => false],
                [
                    'icon'  => '', 'text' => '3', 'copy' => true,
                    'value' => $data['options']['binance_uid'] ?? '',
                    'vars'  => ['{binance_uid}' => $data['options']['binance_uid'] ?? ''],
                ],
                [
                    'icon'   => '', 'text' => '4',
                    'action' => [
                        'type'  => 'image',
                        'label' => 'Show QR Code',
                        'value' => $data['options']['qr_code'] ?? '',
                    ],
                ],
                [
                    'icon'  => '', 'text' => '5', 'copy' => true,
                    'value' => '', // filled below in process_payment display
                    'vars'  => ['{amount}' => '', '{currency}' => 'USDT'],
                ],
                ['icon' => '', 'text' => '6', 'copy' => false],
                ['icon' => '', 'text' => '7', 'copy' => false],
            ];
        }

        // ── Helper: compute USDT amount (2 decimals) ───────────────────────────
        private function calc_usdt($data)
        {
            $local_amount    = floatval($data['transaction']['local_net_amount'] ?? 0);
            $conversion_rate = floatval($data['options']['conversion_rate']      ?? 0);
            $txnCurrency     = $data['transaction']['currency'] ?? '';

            if ($txnCurrency === 'USDT' || $txnCurrency === 'USD') {
                return round($local_amount, 2);
            }

            if ($conversion_rate > 0) {
                // local currency → USDT
                $usdt = $local_amount / $conversion_rate;
            } else {
                $usdt = $local_amount;
            }

            return round($usdt, 2);
        }

        function process_payment($data = [])
        {
            global $db_prefix;

            // ── Generate standard 2-decimal amount ─────────────────────────────
            $binance_amount = $this->calc_usdt($data);

            // Store expected amount in DB so ipn() can verify exact match
            $ref        = $data['transaction']['ref'];
            $existingRow = json_decode(getData($db_prefix.'transaction', "WHERE ref='".$ref."'", '* FROM', []), true);
            if (!empty($existingRow['response'][0])) {
                $storedSender = $existingRow['response'][0]['sender'] ?? '';
                if (strpos($storedSender, 'bnc_expect:') === false) {
                    updateData($db_prefix.'transaction', ['sender'], ['bnc_expect:'.$binance_amount], "ref='".$ref."'");
                } else {
                    // Already stored — use stored value for UI consistency
                    $binance_amount = floatval(str_replace('bnc_expect:', '', $storedSender));
                }
            }

            $uid = $data['options']['binance_uid'] ?? '';
            $qr  = $data['options']['qr_code']    ?? '';
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700&display=swap');
/* Hide the default gateway logo since we have our own in the yellow header */
.company-logo { display: none !important; }
/* Parent card has 20px padding. -20px aligns it perfectly to the edges. */
.zini-gateway-card { border-radius: 12px !important; overflow: hidden !important; padding-top: 20px !important; }
.zini-bnc-wrap { font-family: 'Anek Bangla', sans-serif; color: #1f2937; margin: 5px -20px 0px -20px; }
.zini-bnc-header { background: #fcd535; padding: 20px 24px 24px 24px; text-align: center; border-radius: 0; }
.zini-bnc-header-title { font-weight: 700; font-size: 20px; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 0px; color: #111827; }
.zini-bnc-header-sub { font-size: 14px; color: #4b5563; font-weight: 500; margin-top: 4px; }
.zini-bnc-body { padding: 32px 24px; }
.zini-bnc-amount-row { display: flex; justify-content: space-between; align-items: center; background: #f9fafb; border-radius: 12px; padding: 16px; margin-bottom: 24px; border: 1px solid #e5e7eb; }
.zini-bnc-amount-label { font-size: 14px; color: #6b7280; font-weight: 500; letter-spacing: 0.05em; }
.zini-bnc-amount-val { font-size: 30px; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 8px; }
.zini-bnc-amount-val span { font-size: 18px; color: #4b5563; font-weight: 600; }
.zini-bnc-copy { cursor: pointer; color: #6b7280; display: flex; align-items: center; justify-content: center; padding: 8px; transition: color 0.2s; font-size: 20px; margin-left: 8px; }
.zini-bnc-copy:hover { color: #fcd535; }
.zini-bnc-step { display: flex; gap: 16px; margin-bottom: 24px; }
.zini-bnc-step-num { width: 28px; height: 28px; border-radius: 50%; background: #fcd535; color: #111827; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0; margin-top: 2px; }
.zini-bnc-step-content { flex-grow: 1; }
.zini-bnc-step-title { font-weight: 600; font-size: 18px; margin-bottom: 4px; color: #1f2937; }
.zini-bnc-step-desc { font-size: 14px; color: #4b5563; line-height: 1.5; margin-bottom: 16px; }
.zini-bnc-box { background: #fff; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 16px; margin-top: 8px; }
.zini-bnc-box-label { font-size: 14px; color: #6b7280; margin-bottom: 12px; }
.zini-bnc-input-wrapper { display: flex; justify-content: space-between; align-items: center; background: #fff; border: 1px solid #e5e7eb; border-radius: 4px; padding: 12px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
.zini-bnc-input-readonly { font-family: monospace; font-size: 18px; letter-spacing: 0.05em; font-weight: 600; color: #111827; border: none; outline: none; background: transparent; width: 100%; }
.zini-bnc-qr { margin-top: 16px; text-align: center; }
.zini-bnc-qr img { width: 160px; height: 160px; border-radius: 8px; }
.zini-bnc-form-group { margin-bottom: 16px; }
.zini-bnc-form-group label { display: block; font-size: 14px; color: #4b5563; margin-bottom: 8px; font-weight: 500; }
.zini-bnc-input { width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px; outline: none; box-sizing: border-box; transition: 0.2s; color: #111827; }
.zini-bnc-input:focus { border-color: #fcd535; outline: 2px solid rgba(252,213,53,0.5); }
.zini-bnc-btn { background: #fcd535; color: #111827; border: none; border-radius: 6px; padding: 12px 24px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; transition: 0.2s; }
.zini-bnc-btn:hover { background: #f0c314; }
.zini-bnc-btn:disabled { background: #f5f5f5; color: #b7bdc6; cursor: not-allowed; }
.zini-bnc-footer { background: transparent; padding: 0 24px 20px 24px; display: flex; align-items: center; justify-content: center; gap: 8px; border-top: none; }
.zini-bnc-footer svg { color: #f59e0b; width: 18px; height: 18px; flex-shrink: 0; }
.zini-bnc-footer span { font-size: 13px; color: #6b7280; font-weight: 500; }
@media (max-width: 640px) {
    .zini-bnc-body { padding: 20px 16px; }
    .zini-bnc-amount-row { padding: 16px; }
    .zini-bnc-amount-val { font-size: 26px; }
    .zini-bnc-step { gap: 12px; }
    .zini-bnc-box { padding: 12px; }
    .zini-bnc-input-wrapper { padding: 10px; }
    .zini-bnc-input-readonly { font-size: 16px; }
    .zini-bnc-footer { padding: 0 16px 20px 16px; }
}
</style>

<div class="zini-bnc-wrap">
    <div class="zini-bnc-header">
        <div class="zini-bnc-header-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#1e2329"><path d="M12 24c6.627 0 12-5.373 12-12S18.627 0 12 0 0 5.373 0 12s5.373 12 12 12z" fill="#fcd535"/><path d="M11.996 15.656l-3.327-3.326-1.584 1.584 4.91 4.91 4.91-4.91-1.583-1.583-3.326 3.325zm0-7.312l3.327 3.326 1.583-1.584-4.91-4.91-4.91 4.91 1.584 1.583 3.326-3.325zm0 1.636l-1.69 1.69 1.69 1.69 1.69-1.69-1.69-1.69zm-4.73 1.69l-1.69 1.69 1.69 1.69 1.69-1.69-1.69-1.69zm9.46 0l-1.69 1.69 1.69 1.69 1.69-1.69-1.69-1.69z" fill="#1e2329"/></svg>
            Binance Pay
        </div>
        <div class="zini-bnc-header-sub">Secure and fast crypto checkout</div>
    </div>

    <div class="zini-bnc-body">
        <div class="zini-bnc-amount-row">
            <div class="zini-bnc-amount-label">AMOUNT TO PAY</div>
            <div class="zini-bnc-amount-val">
                <?php echo $binance_amount ?> <span>USDT</span>
                <div class="zini-bnc-copy" onclick="pp_copy('<?php echo $binance_amount ?>', 'Amount Copied!')" title="Copy Amount">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg>
                </div>
            </div>
        </div>

        <div class="zini-bnc-step">
            <div class="zini-bnc-step-num">1</div>
            <div class="zini-bnc-step-content">
                <div class="zini-bnc-step-title">Open Binance App</div>
                <div class="zini-bnc-step-desc">Go to your Binance Mobile App or Website and choose "<b>Send to Binance User</b>".</div>
            </div>
        </div>

        <div class="zini-bnc-step">
            <div class="zini-bnc-step-num">2</div>
            <div class="zini-bnc-step-content">
                <div class="zini-bnc-step-title">Transfer Details</div>
                <div class="zini-bnc-box">
                    <div class="zini-bnc-box-label">Send exactly to this Binance UID:</div>
                    <div class="zini-bnc-input-wrapper">
                        <input type="text" class="zini-bnc-input-readonly" value="<?php echo htmlspecialchars($uid) ?>" readonly>
                        <div class="zini-bnc-copy" onclick="pp_copy('<?php echo htmlspecialchars($uid) ?>', 'Binance UID Copied!')" title="Copy UID">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg>
                        </div>
                    </div>
                    <?php if (!empty($qr)): ?>
                    <div class="zini-bnc-qr">
                        <div class="zini-bnc-box-label">Or scan QR Code to Pay</div>
                        <img src="<?php echo $qr ?>" alt="Binance QR Code">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="zini-bnc-step" style="margin-bottom: 0px;">
            <div class="zini-bnc-step-num">3</div>
            <div class="zini-bnc-step-content">
                <div class="zini-bnc-step-title">Verify Payment</div>
                <div class="zini-bnc-step-desc">After transferring, enter the <b>Order ID</b> (Transaction Hash/ID) below to verify your payment.</div>
                <form class="payment-form-submit" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="bpid" value="<?php echo $data['transaction']['ref'] ?>">
                    <input type="hidden" name="binance_amount_usdt" value="<?php echo $binance_amount ?>">
                    
                    <div class="zini-bnc-form-group">
                        <label>Binance Order ID</label>
                        <input type="text" name="order_id" class="zini-bnc-input" placeholder="e.g. 1928374650" required autocomplete="off">
                    </div>
                    
                    <button class="zini-bnc-btn payment-form-btn" type="submit">Verify Payment</button>
                </form>
            </div>
        </div>
    </div>

    <div class="zini-bnc-footer">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
        <span>Please confirm the network is correct before sending.</span>
    </div>
</div>

<script data-cfasync="false">

document.addEventListener("DOMContentLoaded",function(){
    var form=document.querySelector(".payment-form-submit");
    var btn=form.querySelector(".payment-form-btn");
    form.addEventListener("submit",function(e){
        e.preventDefault();
        var fd=new FormData(form);
        var orig=btn.innerHTML;
        btn.innerHTML='<span class="spinner-border spinner-border-sm"></span>';
        btn.disabled=true;
        fetch("<?php echo pp_ipn_url($data['gateway']['gateway_id']) ?>",{method:"POST",body:fd})
        .then(function(r){return r.json();})
        .then(function(d){
            btn.innerHTML=orig; btn.disabled=false;
            if(d.status==="true"){
                if(d.redirect&&d.redirect!==''){window.location.href=d.redirect;}
                else if(typeof success==='function'){success(d);}
            } else {
                if(typeof failed==='function'){failed(d.title,d.message);}
                else{alert(d.message||'Verification failed');}
            }
        })
        .catch(function(){
            btn.innerHTML=orig; btn.disabled=false;
            if(typeof failed==='function'){failed("Error","Connection error. Please try again.");}
        });
    });
});
</script>
<?php
        }

        function ipn($data = [])
        {
            error_reporting(0);
            if (ob_get_length()) { ob_clean(); }
            header('Content-Type: application/json');

            $bpid     = trim($_POST['bpid']     ?? '');
            $order_id = trim($_POST['order_id'] ?? '');

            // 1. Basic validation
            if ($order_id === '') {
                echo json_encode(['status'=>'false','title'=>'Missing Order ID','message'=>'Please enter the Binance Order ID.']);
                return;
            }

            // 2. Duplicate check
            if (pp_check_transaction_id($order_id)) {
                echo json_encode(['status'=>'false','title'=>'Already Used','message'=>'This Order ID has already been used.']);
                return;
            }

            // 3. Valid invoice
            if (!pp_check_transaction($bpid)) {
                echo json_encode(['status'=>'false','title'=>'Invalid Request','message'=>'Payment reference not found.']);
                return;
            }

            // 4. Fetch transaction row
            global $db_prefix;
            $txRow = json_decode(getData($db_prefix.'transaction', "WHERE ref='".addslashes($bpid)."'", '* FROM', []), true);
            if (empty($txRow['response'][0])) {
                echo json_encode(['status'=>'false','title'=>'Error','message'=>'Transaction not found.']);
                return;
            }
            $tx = $txRow['response'][0];

            // 5. Gateway options
            $gateway_options = $data['gateway']['options'] ?? [];
            if (!is_array($gateway_options)) {
                $gateway_options = json_decode($gateway_options, true) ?? [];
            }
            $api_key    = trim($gateway_options['api_key']    ?? '');
            $secret_key = trim($gateway_options['secret_key'] ?? '');

            // 6. Recover expected USDT amount from DB
            $stored_sender  = $tx['sender'] ?? '';
            $expected_usdt  = null;
            if (strpos($stored_sender, 'bnc_expect:') === 0) {
                $expected_usdt = floatval(str_replace('bnc_expect:', '', $stored_sender));
            }
            
            if ($expected_usdt === null) {
                echo json_encode(['status'=>'false','title'=>'Error','message'=>'System could not find the expected offset amount for this invoice.']);
                return;
            }

            // 7. No API keys -> return error
            if (empty($api_key) || $api_key === '--' || empty($secret_key) || $secret_key === '--') {
                echo json_encode(['status'=>'false','title'=>'Configuration Error','message'=>'API Key or Secret Key is missing in admin panel.']);
                return;
            }

            // 8. Call Binance /sapi/v1/pay/transactions directly with Retry Logic
            $max_retries = 3;
            $attempt = 0;
            $response = false;
            $http_code = 0;
            $curl_error = '';

            while ($attempt < $max_retries) {
                $ts     = round(microtime(true) * 1000);
                $params = http_build_query(['limit' => 100, 'timestamp' => $ts]);
                $sig    = hash_hmac('sha256', $params, $secret_key);
                $url    = 'https://binance-proxy-mauve.vercel.app/api/proxy/sapi/v1/pay/transactions?' . $params . '&signature=' . $sig;

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $url,
                    CURLOPT_HTTPGET        => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => 15,
                    CURLOPT_SSL_VERIFYPEER => false, // Sometimes local XAMPP misses CA certs causing reset
                    CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4, // Force IPv4 to prevent IPv6 routing issues
                    CURLOPT_HTTPHEADER     => [
                        'X-MBX-APIKEY: ' . $api_key,
                        'Connection: keep-alive'
                    ],
                ]);
                
                $response  = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                if ($response === false || $response === '' || $http_code === 0) {
                    $curl_error = curl_error($ch);
                    curl_close($ch);
                    $attempt++;
                    sleep(1); // wait 1 second before retry
                    continue;
                }
                
                curl_close($ch);
                break; // success
            }

            // 9. cURL network error (Timeout/No response after retries)
            if ($response === false || $response === '' || $http_code === 0) {
                echo json_encode(['status'=>'false','title'=>'Network Error','message'=>'cURL Error after ' . $max_retries . ' attempts: ' . ($curl_error ? $curl_error : 'Unknown Network Timeout')]);
                return;
            }

            // 10. Parse API response
            $res      = json_decode($response, true);
            $res_code = (string)($res['code'] ?? 'NO_CODE');
            $err_msg  = $res['msg'] ?? ($res['message'] ?? 'Unknown API Error');

            if ($res_code !== '000000' || !isset($res['data'])) {
                echo json_encode(['status'=>'false','title'=>'Binance API Error','message' => 'Binance error ('.$res_code.'): '.$err_msg]);
                return;
            }

            // 11. Search for matching transaction
            $matched         = null;
            $received_amount = 0.0;
            $received_curr   = '';

            foreach ($res['data'] as $entry) {
                $entry_order_id = trim((string)($entry['orderId']       ?? ''));
                $entry_txn_id   = trim((string)($entry['transactionId'] ?? ''));
                $entry_amount   = floatval($entry['amount']             ?? 0);
                $entry_currency = strtoupper(trim($entry['currency']    ?? ''));
                
                // Binance Pay direction: we only care about incoming money
                if ($entry_amount <= 0) continue;

                // Match by orderId or transactionId
                if ($entry_order_id !== $order_id && $entry_txn_id !== $order_id) continue;

                // Amount check (±0.05 USDT tolerance)
                $diff = abs($entry_amount - $expected_usdt);
                if ($diff > 0.05) {
                    echo json_encode([
                        'status'  => 'false',
                        'title'   => 'Amount Mismatch',
                        'message' => sprintf(
                            'Found Order ID, but amount doesn\'t match. Expected: %.2f USDT, Received: %.2f %s.',
                            $expected_usdt, $entry_amount, $entry_currency
                        )
                    ]);
                    return;
                }

                // Currency check
                if ($entry_currency !== 'USDT' && $entry_currency !== '') {
                    echo json_encode(['status'=>'false','title'=>'Wrong Currency','message'=>'Payment must be in USDT. Received: '.$entry_currency]);
                    return;
                }

                $matched         = $entry;
                $received_amount = $entry_amount;
                $received_curr   = $entry_currency ?: 'USDT';
                break;
            }

            // Not found in ledger
            if ($matched === null) {
                echo json_encode(['status'=>'false','title'=>'Transaction Not Found',
                    'message'=>'Order ID "'.htmlspecialchars($order_id).'" not found in Binance Pay history. Check carefully and try again.'
                ]);
                return;
            }

            // 12. Mark completed
            $source_info = [
                ['label' => 'Binance Order ID',   'value' => $order_id],
                ['label' => 'Amount Paid (USDT)', 'value' => number_format($received_amount, 2)],
                ['label' => 'Expected (USDT)',    'value' => number_format($expected_usdt, 2)],
            ];

            $set_result = pp_set_transaction_status($bpid, 'completed', $data['gateway']['gateway_id'], $order_id, $source_info);

            if (!$set_result) {
                updateData($db_prefix.'transaction',
                    ['status','trx_id','gateway_id','updated_date'],
                    ['completed', $order_id, $data['gateway']['gateway_id'], getCurrentDatetime('Y-m-d H:i:s')],
                    "ref='".addslashes($bpid)."'"
                );
            }

            echo json_encode([
                'status'   => 'true',
                'title'    => 'Payment Verified!',
                'message'  => 'Your Binance payment of '.number_format($received_amount, 2).' USDT verified successfully.',
                'redirect' => pp_checkout_address($bpid),
            ]);
        }
    }
