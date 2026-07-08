<?php
    class SslcommerzGateway
    {
        public function info()
        {
            return [
                'title'       => 'SSLCommerz Gateway',
                'logo'        => 'assets/logo.jpg',
                'currency'        => 'BDT',
                'tab'        => 'mfs',

                'gateway_type'        => 'api',
            ];
        }

        public function color()
        {
            return [
                'primary_color'        => '#295cab',
                'text_color'        => '#FFFFFF',
                'btn_color'        => '#295cab',
                'btn_text_color'        => '#FFFFFF',
            ];
        }

        public function fields()
        {
            return [
                [
                    'name'  => 'store_id',
                    'label' => 'Store ID',
                    'type'  => 'text',
                ],
                [
                    'name'  => 'store_password',
                    'label' => 'Store Password',
                    'type'  => 'text',
                ],
                [
                    'name'  => 'product_category',
                    'label' => 'Product Category',
                    'type'  => 'text',
                ],
                [
                    'name'  => 'mode',
                    'label' => 'Mode',
                    'type'  => 'select',
                    'options' => [
                        'live'  => 'Live',
                        'sandbox' => 'Sandbox',
                    ],
                    'value' => 'live',
                    'required' => true,
                    'multiple' => false,
                ],
            ];
        }

        function process_payment($data = []){
            echo '<center><div class="spinner-border text-primary m-3 loading-123412341234" role="status"><span class="visually-hidden">Loading...</span></div></center>';

            $base_url = (($data['options']['mode'] ?? 'sandbox') === 'live') ? 'https://securepay.sslcommerz.com' : 'https://sandbox.sslcommerz.com';

            $url = $base_url."/gwprocess/v4/api.php";
                
            $tran_id = rand();
            
            $data_ini = array(
                "store_id" => ($data['options']['store_id'] ?? ''),
                "store_passwd" => ($data['options']['store_password'] ?? ''),
                "total_amount" => $data['transaction']['local_net_amount'],
                "currency" => $data['transaction']['local_currency'],
                "tran_id" => $tran_id,
                "success_url" => pp_callback_url(),
                "fail_url" => pp_checkout_address(),
                "cancel_url" => pp_checkout_address(),
                "ipn_url" => pp_checkout_address(),
                "emi_option" => "0",
                "cus_name" => $data['transaction']['customer']['name'],
                "cus_email" => $data['transaction']['customer']['email'],
                "cus_phone" => $data['transaction']['customer']['mobile'],
                "cus_add1" => "Suite 101",
                "cus_add2" => "Suite 101",
                "cus_city" => "Dhaka",
                "cus_state" => "Dhaka",
                "cus_postcode" => "1207",
                "cus_country" => "Bangladesh",
                "cus_fax" => "",
                "shipping_method" => "NO",
                "num_of_item" => "1",
                "product_name" => ($data['options']['product_category'] ?? ''),
                "product_category" => ($data['options']['product_category'] ?? ''),
                "product_profile" => "general",
                "product_amount" => $data['transaction']['local_net_amount'],
                "vat" => "0.00",
                "discount_amount" => "0.00",
                "convenience_fee" => "0.00",
                "value_a" => $data['transaction']['ref']
            );
            
            // Initialize cURL session
            $ch = curl_init($url);
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_ini));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL check for sandbox
            
            // Execute cURL request
            $response_curl_before = curl_exec($ch);
            $response_curl = json_decode($response_curl_before, true);
            
            curl_close($ch);
            
            if($response_curl['status'] == "SUCCESS"){
                echo '<script>location.href="'.$response_curl['GatewayPageURL'].'"</script>';
            }else{
                echo '<div class="alert alert-danger" role="alert">'.$response_curl_before.'</div> <style>.loading-123412341234{display: none;}</style>';
            }
        }

        function callback($data = []){
            echo '<center><div class="spinner-border text-primary m-3 loading-123412341234" role="status"><span class="visually-hidden">Loading...</span></div></center>';

            $base_url = (($data['options']['mode'] ?? 'sandbox') === 'live') ? 'https://securepay.sslcommerz.com' : 'https://sandbox.sslcommerz.com';

            $tran_id = $_POST['tran_id'] ?? '';

            $url = $base_url."/validator/api/merchantTransIDvalidationAPI.php";
            $url .= "?tran_id=".$tran_id."&store_id=".($data['options']['store_id'] ?? '')."&store_passwd=".($data['options']['store_password'] ?? '')."&format=json";
            
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);

            $data_verify = json_decode($response, true);

            if (isset($data_verify['APIConnect']) && $data_verify['APIConnect'] === 'DONE' && isset($data_verify['no_of_trans_found']) && $data_verify['no_of_trans_found'] > 0 && isset($data_verify['element'][0]['status']) && in_array($data_verify['element'][0]['status'], ['VALID', 'VALIDATED'])){
                $transaction = $data_verify['element'][0];

                if($transaction['value_a'] == $data['transaction']['ref']){
                    $moreinfo = [
                        [
                            'label' => 'SSLCommerz TransactionId',
                            'value' => $transaction['val_id']
                        ],
                        [
                            'label' => 'Financial Entity',
                            'value' => $transaction['bank_gw']
                        ],
                        [
                            'label' => 'PG Transaction ID',
                            'value' => $transaction['bank_tran_id']
                        ]
                    ];

                    pp_set_transaction_status($data['transaction']['ref'], 'completed', $data['gateway']['gateway_id'], $transaction['tran_id'], $moreinfo);

                    echo "<script>location.reload();</script>";
                }else{
                    echo '<div class="alert alert-danger" role="alert">Transaction not valid or not found.</div><style>.loading-123412341234{display: none;}</style>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">Transaction not valid or not found.</div>';
            }
            
            curl_close($ch);
        }
    }