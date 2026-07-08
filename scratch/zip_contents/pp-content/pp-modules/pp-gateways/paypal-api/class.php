<?php
    class PaypalApiGateway
    {
        public function info()
        {
            return [
                'title'       => 'PayPal API',
                'logo'        => 'assets/logo.jpg',
                'currency'    => 'USD',
                'tab'         => 'global',

                'gateway_type' => 'api',
            ];
        }

        public function color()
        {
            return [
                'primary_color'   => '#003087',
                'text_color'      => '#FFFFFF',
                'btn_color'       => '#003087',
                'btn_text_color'  => '#FFFFFF',
            ];
        }

        public function fields()
        {
            return [
                [
                    'name'  => 'client_id',
                    'label' => 'PayPal Client ID',
                    'type'  => 'text',
                ],
                [
                    'name'  => 'client_secret',
                    'label' => 'PayPal Client Secret',
                    'type'  => 'text',
                ],
                [
                    'name'  => 'mode',
                    'label' => 'Mode',
                    'type'  => 'select',
                    'options' => [
                        'live'    => 'Live',
                        'sandbox' => 'Sandbox',
                    ],
                    'value'    => 'live',
                    'required' => true,
                    'multiple' => false,
                ],
            ];
        }

        function process_payment($data = []){
            echo '<center><div class="spinner-border text-primary m-3 loading-123412341234" role="status"><span class="visually-hidden">Loading...</span></div></center>';

            $token_response = $this->get_access_token($data);
            if(!$token_response['status']){
                $this->render_error('Unable to connect with PayPal.', $token_response['message']);
                return;
            }

            $amount = number_format((float) ($data['transaction']['local_net_amount'] ?? 0), 2, '.', '');
            $currency = strtoupper((string) ($data['transaction']['local_currency'] ?? 'USD'));
            $transaction_ref = (string) ($data['transaction']['ref'] ?? '');

            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $transaction_ref,
                        'custom_id' => $transaction_ref,
                        'invoice_id' => $transaction_ref,
                        'description' => 'Payment for transaction '.$transaction_ref,
                        'amount' => [
                            'currency_code' => $currency,
                            'value' => $amount
                        ],
                    ],
                ],
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'return_url' => pp_callback_url(),
                            'cancel_url' => pp_checkout_address(),
                            'shipping_preference' => 'NO_SHIPPING',
                            'user_action' => 'PAY_NOW'
                        ]
                    ]
                ]
            ];

            $request_headers = [
                'PayPal-Request-Id: '.$this->generate_request_id('PP-ORDER-'.$transaction_ref)
            ];

            $create_order = $this->paypal_request(
                'POST',
                $this->base_url($data).'/v2/checkout/orders',
                $token_response['access_token'],
                $payload,
                $request_headers
            );

            if(!$create_order['status']){
                $this->render_error('Unable to create PayPal order.', $create_order['message']);
                return;
            }

            $approval_url = $this->find_approval_url($create_order['data']['links'] ?? []);

            if($approval_url == ''){
                $this->render_error('PayPal approval URL was not found.', 'Please check your PayPal app configuration.');
                if(!empty($_GET['pp_debug'])){
                    $debug_json = json_encode(
                        $create_order,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    );
                    if($debug_json === false){
                        $debug_json = 'Unable to encode debug JSON.';
                    }

                    $debug_json = substr($debug_json, 0, 8000);
                    $safe_debug = htmlspecialchars((string) $debug_json, ENT_QUOTES, 'UTF-8');
                    echo '<pre class="pp-debug">'.$safe_debug.'</pre>';
                    echo '<style>.pp-debug{background:#fff;border:1px solid #f2c2c2;padding:12px;max-width:100%;overflow:auto;}</style>';
                }
                return;
            }

            echo '<script>location.href=' . json_encode($approval_url) . ';</script>';
        }

        function callback($data = []){
            echo '<center><div class="spinner-border text-primary m-3 loading-123412341234" role="status"><span class="visually-hidden">Loading...</span></div></center>';

            $order_id = trim((string) ($_GET['token'] ?? ''));
            if($order_id === ''){
                $this->render_error('PayPal order token is missing.', 'Please try again from checkout.');
                return;
            }

            $token_response = $this->get_access_token($data);
            if(!$token_response['status']){
                $this->render_error('Unable to connect with PayPal.', $token_response['message']);
                return;
            }

            $access_token = $token_response['access_token'];
            $order_details = $this->paypal_request(
                'GET',
                $this->base_url($data).'/v2/checkout/orders/'.rawurlencode($order_id),
                $access_token
            );

            if(!$order_details['status']){
                $this->render_error('Unable to verify PayPal order.', $order_details['message']);
                return;
            }

            if(!$this->order_matches_transaction($order_details['data'], $data)){
                $this->render_error('Payment verification failed.', 'Transaction data does not match this order.');
                return;
            }

            $order_status = strtoupper((string) ($order_details['data']['status'] ?? ''));
            $final_order_payload = $order_details['data'];

            if($order_status === 'APPROVED'){
                $capture_headers = [
                    'PayPal-Request-Id: '.$this->generate_request_id('PP-CAPTURE-'.$order_id),
                    'Prefer: return=representation'
                ];

                $capture_order = $this->paypal_request(
                    'POST',
                    $this->base_url($data).'/v2/checkout/orders/'.rawurlencode($order_id).'/capture',
                    $access_token,
                    new stdClass(),
                    $capture_headers
                );

                if(!$capture_order['status']){
                    $refresh_order = $this->paypal_request(
                        'GET',
                        $this->base_url($data).'/v2/checkout/orders/'.rawurlencode($order_id),
                        $access_token
                    );

                    if(!$refresh_order['status']){
                        $this->render_error('PayPal capture failed.', $capture_order['message']);
                        return;
                    }

                    $final_order_payload = $refresh_order['data'];
                }else{
                    $final_order_payload = $capture_order['data'];
                }
            }

            if(!$this->order_matches_transaction($final_order_payload, $data)){
                $this->render_error('Payment verification failed.', 'Captured amount or transaction reference is invalid.');
                return;
            }

            $final_status = strtoupper((string) ($final_order_payload['status'] ?? ''));
            if($final_status !== 'COMPLETED'){
                $this->render_error('Payment not completed.', 'Current PayPal order status: '.$final_status);
                return;
            }

            $capture_id = $this->extract_capture_id($final_order_payload);
            if($capture_id == ''){
                $capture_id = $order_id;
            }

            $payer_id = (string) ($final_order_payload['payer']['payer_id'] ?? '');
            $payer_email = (string) ($final_order_payload['payer']['email_address'] ?? '');

            $moreinfo = [
                [
                    'label' => 'PayPal Order ID',
                    'value' => $order_id
                ]
            ];

            if($payer_id !== ''){
                $moreinfo[] = [
                    'label' => 'PayPal Payer ID',
                    'value' => $payer_id
                ];
            }

            if($payer_email !== ''){
                $moreinfo[] = [
                    'label' => 'PayPal Payer Email',
                    'value' => $payer_email
                ];
            }

            pp_set_transaction_status(
                $data['transaction']['ref'],
                'completed',
                $data['gateway']['gateway_id'],
                $capture_id,
                $moreinfo
            );

            echo "<script>location.reload();</script>";
        }

        private function base_url($data = [])
        {
            return (($data['options']['mode'] ?? 'sandbox') === 'live')
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';
        }

        private function get_access_token($data = [])
        {
            $client_id = trim((string) ($data['options']['client_id'] ?? ''));
            $client_secret = trim((string) ($data['options']['client_secret'] ?? ''));

            if($client_id === '' || $client_secret === ''){
                return [
                    'status' => false,
                    'message' => 'PayPal Client ID or Client Secret is missing.'
                ];
            }

            $ch = curl_init($this->base_url($data).'/v1/oauth2/token');

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_USERPWD => $client_id.':'.$client_secret,
                CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Accept-Language: en_US',
                    'Content-Type: application/x-www-form-urlencoded'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if($curl_error){
                return [
                    'status' => false,
                    'message' => 'cURL error: '.$curl_error
                ];
            }

            $decoded = json_decode((string) $response, true);
            if(!is_array($decoded)){
                $decoded = [];
            }
            if($http_code < 200 || $http_code >= 300){
                return [
                    'status' => false,
                    'message' => $this->extract_api_message($decoded, 'HTTP '.$http_code)
                ];
            }

            if(!isset($decoded['access_token'])){
                return [
                    'status' => false,
                    'message' => $this->extract_api_message($decoded, 'Access token not found in response.')
                ];
            }

            return [
                'status' => true,
                'access_token' => $decoded['access_token']
            ];
        }

        private function paypal_request($method, $url, $access_token, $payload = null, $extra_headers = [])
        {
            $headers = [
                'Accept: application/json',
                'Authorization: Bearer '.$access_token
            ];

            if($payload !== null){
                $headers[] = 'Content-Type: application/json';
            }

            if(!empty($extra_headers)){
                $headers = array_merge($headers, $extra_headers);
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper((string) $method));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            if($payload !== null){
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }

            $response = curl_exec($ch);
            $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if($curl_error){
                return [
                    'status' => false,
                    'message' => 'cURL error: '.$curl_error,
                    'data' => []
                ];
            }

            $decoded = json_decode((string) $response, true);
            if(!is_array($decoded)){
                $decoded = [];
            }

            if($http_code < 200 || $http_code >= 300){
                return [
                    'status' => false,
                    'message' => $this->extract_api_message($decoded, 'HTTP '.$http_code),
                    'data' => $decoded
                ];
            }

            return [
                'status' => true,
                'message' => '',
                'data' => $decoded
            ];
        }

        private function extract_api_message($payload = [], $fallback = 'Unknown error')
        {
            if(isset($payload['message']) && $payload['message'] !== ''){
                if(isset($payload['details'][0]['description']) && $payload['details'][0]['description'] !== ''){
                    return $payload['message'].' - '.$payload['details'][0]['description'];
                }

                return $payload['message'];
            }

            if(isset($payload['error_description']) && $payload['error_description'] !== ''){
                return $payload['error_description'];
            }

            if(isset($payload['error']) && $payload['error'] !== ''){
                return $payload['error'];
            }

            return $fallback;
        }

        private function find_approval_url($links = [])
        {
            if(!is_array($links)){
                return '';
            }

            $priority = ['approve', 'payer-action', 'approval_url'];

            foreach($priority as $target_rel){
                foreach($links as $link){
                    $rel = strtolower((string) ($link['rel'] ?? ''));
                    if($rel === $target_rel && !empty($link['href'])){
                        return (string) $link['href'];
                    }
                }
            }

            return '';
        }

        private function order_matches_transaction($paypal_order = [], $data = [])
        {
            if(!is_array($paypal_order) || empty($paypal_order['purchase_units'][0])){
                return false;
            }

            $purchase_unit = $paypal_order['purchase_units'][0];

            $expected_ref = (string) ($data['transaction']['ref'] ?? '');
            $expected_currency = strtoupper((string) ($data['transaction']['local_currency'] ?? ''));
            $expected_amount = number_format((float) ($data['transaction']['local_net_amount'] ?? 0), 2, '.', '');

            $possible_refs = [
                (string) ($purchase_unit['custom_id'] ?? ''),
                (string) ($purchase_unit['invoice_id'] ?? ''),
                (string) ($purchase_unit['reference_id'] ?? ''),
            ];

            $ref_matched = false;
            foreach($possible_refs as $ref_item){
                if($ref_item !== '' && $ref_item === $expected_ref){
                    $ref_matched = true;
                    break;
                }
            }

            if(!$ref_matched){
                return false;
            }

            $amount_currency = strtoupper((string) ($purchase_unit['amount']['currency_code'] ?? ''));
            $amount_value = (string) ($purchase_unit['amount']['value'] ?? '');

            if($amount_currency === '' || $amount_value === ''){
                $capture_amount = $purchase_unit['payments']['captures'][0]['amount'] ?? [];
                $amount_currency = strtoupper((string) ($capture_amount['currency_code'] ?? ''));
                $amount_value = (string) ($capture_amount['value'] ?? '');
            }

            if($amount_currency !== $expected_currency){
                return false;
            }

            $actual_float = (float) $amount_value;
            $expected_float = (float) $expected_amount;

            return abs($actual_float - $expected_float) <= 0.01;
        }

        private function extract_capture_id($paypal_order = [])
        {
            $capture_id = (string) ($paypal_order['purchase_units'][0]['payments']['captures'][0]['id'] ?? '');
            if($capture_id !== ''){
                return $capture_id;
            }

            $capture_id = (string) ($paypal_order['purchase_units'][0]['payments']['authorizations'][0]['id'] ?? '');
            return $capture_id;
        }

        private function generate_request_id($prefix = 'PP')
        {
            $unique = uniqid($prefix.'-', true);
            $unique = str_replace('.', '-', $unique);

            if(strlen($unique) > 100){
                $unique = substr($unique, 0, 100);
            }

            return $unique;
        }

        private function render_error($title, $details = '')
        {
            $safe_title = htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8');
            $safe_details = htmlspecialchars((string) $details, ENT_QUOTES, 'UTF-8');

            echo '<div class="alert alert-danger" role="alert">'.$safe_title;
            if($safe_details !== ''){
                echo '<br><small>'.$safe_details.'</small>';
            }
            echo '</div><style>.loading-123412341234{display: none;}</style>';
        }
    }
