<?php
    class AamarpayGateway
    {
        public function info()
        {
            return [
                'title'       => 'aamarPay Gateway',
                'logo'        => 'assets/logo.jpg',
                'currency'        => 'BDT',
                'tab'        => 'mfs',

                'gateway_type'        => 'api',
            ];
        }

        public function color()
        {
            return [
                'primary_color'        => '#f39700',
                'text_color'        => '#504e52',
                'btn_color'        => '#f39700',
                'btn_text_color'        => '#504e52',
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
                    'name'  => 'signature_key',
                    'label' => 'Signature Key',
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

            $base_url = (($data['options']['mode'] ?? 'sandbox') === 'live') ? 'https://secure.aamarpay.com' : 'https://sandbox.aamarpay.com';

            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => $base_url.'/jsonpost.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                "store_id": "'.($data['options']['store_id'] ?? '').'",
                "tran_id": "'.rand().'",
                "success_url": "'.pp_callback_url().'",
                "fail_url": "'.pp_checkout_address().'",
                "cancel_url": "'.pp_checkout_address().'",
                "amount": "'.$data['transaction']['local_net_amount'].'",
                "currency": "'.$data['transaction']['local_currency'].'",
                "signature_key": "'.($data['options']['signature_key'] ?? '').'",
                "desc": "Payment",
                "cus_name": "'.trim($data['transaction']['customer']['name']).'",
                "cus_email": "'.$data['transaction']['customer']['email'].'",
                "cus_add1": "House B-158 Road 22",
                "cus_add2": "Mohakhali DOHS",
                "cus_city": "Dhaka",
                "cus_state": "Dhaka",
                "cus_postcode": "1206",
                "cus_country": "Bangladesh",
                "cus_phone": "'.$data['transaction']['customer']['mobile'].'",
                "type": "json",
                "opt_a": "'.$data['transaction']['ref'].'"
            }',
                CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
                ),
            ));
            
            $response_curl_before = curl_exec($curl);
            
            curl_close($curl);
                                        
            $response_curl = json_decode($response_curl_before, true);

            if(isset($response_curl['payment_url'])){
               echo '<script>location.href="' . $response_curl['payment_url'] . '";</script>';
            }else{
                echo '<div class="alert alert-danger" role="alert">'.$response_curl_before.'</div> <style>.loading-123412341234{display: none;}</style>';
            }
        }

        function callback($data = []){
            echo '<center><div class="spinner-border text-primary m-3 loading-123412341234" role="status"><span class="visually-hidden">Loading...</span></div></center>';

            $base_url = (($data['options']['mode'] ?? 'sandbox') === 'live') ? 'https://secure.aamarpay.com' : 'https://sandbox.aamarpay.com';

            $mer_txnid = $_POST['mer_txnid'] ?? '';

            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => $base_url.'/api/v1/trxcheck/request.php?request_id='.$mer_txnid.'&store_id='.($data['options']['store_id'] ?? '').'&signature_key='.($data['options']['signature_key'] ?? '').'&type=json',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array()  // <-- empty array instead of empty string
            ));
            
            $response = curl_exec($curl);

            curl_close($curl);

            $aamarpaydata = json_decode($response, true);

            if (isset($aamarpaydata['pay_status']) && $aamarpaydata['pay_status'] == "Successful" && isset($aamarpaydata['status_code']) && $aamarpaydata['status_code'] == "2") {
                if($aamarpaydata['opt_a'] == $data['transaction']['ref']){
                    $moreinfo = [
                        [
                            'label' => 'ammarPay TransactionId',
                            'value' => $aamarpaydata['bank_trxid']
                        ],
                        [
                            'label' => 'Financial Entity',
                            'value' => $aamarpaydata['payment_processor']
                        ],
                        [
                            'label' => 'PG Transaction ID',
                            'value' => $aamarpaydata['pg_txnid']
                        ]
                    ];

                    pp_set_transaction_status($data['transaction']['ref'], 'completed', $data['gateway']['gateway_id'], $aamarpaydata['bank_trxid'], $moreinfo);

                    echo "<script>location.reload();</script>";
                }else{
                    echo '<div class="alert alert-danger" role="alert">Transaction not valid or not found.</div><style>.loading-123412341234{display: none;}</style>';
                }
            }else{
                echo '<div class="alert alert-danger" role="alert">Transaction not valid or not found.</div><style>.loading-123412341234{display: none;}</style>';
            }
        }
    }
