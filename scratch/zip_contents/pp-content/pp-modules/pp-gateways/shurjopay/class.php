<?php
    class ShurjopayGateway
    {
        public function info()
        {
            return [
                'title'       => 'shurjoPay Gateway',
                'logo'        => 'assets/logo.jpg',
                'currency'        => 'BDT',
                'tab'        => 'mfs',

                'gateway_type'        => 'api',
            ];
        }

        public function color()
        {
            return [
                'primary_color'        => '#229454',
                'text_color'        => '#FFFFFF',
                'btn_color'        => '#229454',
                'btn_text_color'        => '#FFFFFF',
            ];
        }

        public function fields()
        {
            return [
                [
                    'name'  => 'prefix',
                    'label' => 'Transaction Prefix',
                    'type'  => 'text',
                ],
                [
                    'name'  => 'username',
                    'label' => 'Username',
                    'type'  => 'text',
                ],
                [
                    'name'  => 'password',
                    'label' => 'Password',
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

            $base_url = (($data['options']['mode'] ?? 'sandbox') === 'live') ? 'https://engine.shurjopayment.com' : 'https://sandbox.shurjopayment.com';

            $data_token = [
                "username" => ($data['options']['username'] ?? ''),
                "password" => ($data['options']['password'] ?? '')
            ];
            
            $ch = curl_init($base_url."/api/get_token");
            
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // Execute cURL
            $response = curl_exec($ch);

            curl_close($ch);
            
            $response = json_decode($response, true);

            $data_ini = [
                'prefix' => ($data['options']['prefix'] ?? 'bp'),
                'token' => ($response['token'] ?? 'bp'),
                'return_url' => pp_ipn_url($data['gateway']['gateway_id']),
                'cancel_url' => pp_checkout_address(),
                'store_id' => ($response['store_id'] ?? 'bp'),
                'amount' => $data['transaction']['local_net_amount'],
                'order_id' => rand().'-BP-'.$data['transaction']['ref'],
                'currency' => 'BDT',
                'customer_name' => $data['transaction']['customer']['name'],
                'customer_address' => 'dhaka',
                'customer_phone' => $data['transaction']['customer']['mobile'],
                'customer_city' => 'Dhaka',
                // Additional fields
                'client_ip' => '102.101.1.1',
                'discount_amount' => '0',
                'disc_percent' => '0',
                'customer_email' => $data['transaction']['customer']['email'],
                'customer_state' => 'dhaka',
                'customer_postcode' => '2113',
                'customer_country' => 'BD',
                'shipping_address' => '',
                'shipping_city' => '',
                'shipping_country' => '',
                'received_person_name' => '',
                'shipping_phone_number' => $data['transaction']['customer']['mobile']
            ];
            
            $ch = curl_init($base_url."/api/secret-pay");
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_ini);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . ($response['token'] ?? 'bp')
            ]);
            
            $response_curl_before = curl_exec($ch);
            
            curl_close($ch);
                                        
            $response_curl = json_decode($response_curl_before, true);

            if(isset($response_curl['checkout_url'])){
               echo '<script>location.href="' . $response_curl['checkout_url'] . '";</script>';
            }else{
                echo '<div class="alert alert-danger" role="alert">'.$response_curl_before.'</div> <style>.loading-123412341234{display: none;}</style>';
            }
        }

        function ipn($data = []){
            $base_url = (($data['gateway']['options']['mode'] ?? 'sandbox') === 'live') ? 'https://engine.shurjopayment.com' : 'https://sandbox.shurjopayment.com';

            $order_id = $_GET['order_id'] ?? '';

            if($order_id == ""){
                echo 'Direct access detected!';
            }else{
                $data_token = [
                    "username" => ($data['gateway']['options']['username'] ?? ''),
                    "password" => ($data['gateway']['options']['password'] ?? '')
                ];
                
                $ch = curl_init($base_url."/api/get_token");
                
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/json"
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_token));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                // Execute cURL
                $response = curl_exec($ch);

                curl_close($ch);
                
                $response = json_decode($response, true);
                                                
                $data_ipn = [
                    "order_id" => $order_id
                ];
                
                $ch = curl_init($base_url."/api/verification");
                
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . ($response['token'] ?? ''),
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_ipn));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $response = curl_exec($ch);

                curl_close($ch);
                
                $data_gateway = json_decode($response, true);

                $sp_code = $data_gateway[0]['sp_code'] ?? '';
                $bank_status = $data_gateway[0]['bank_status'] ?? '';

                if ($sp_code == "1000" || $bank_status == "Success") {
                    $customer_order_id = $data_gateway[0]['customer_order_id'];

                    $parts = explode('BP-', $customer_order_id);
                    $after_bp = $parts[1] ?? '';

                    $moreinfo = [
                        [
                            'label' => 'PG ID',
                            'value' => $data_gateway[0]['id']
                        ],
                        [
                            'label' => 'Order ID',
                            'value' => $data_gateway[0]['order_id']
                        ],
                        [
                            'label' => 'Financial Entity',
                            'value' => $data_gateway[0]['method']
                        ]
                    ];

                    pp_set_transaction_status($after_bp, 'completed', $data['gateway']['gateway_id'], $data_gateway[0]['bank_trx_id'], $moreinfo);

                    echo "<script>location.href='".pp_checkout_address($after_bp)."';</script>";
                } else {
                    $customer_order_id = $data_gateway[0]['customer_order_id'] ?? '';
                    $parts = explode('BP-', $customer_order_id);
                    $after_bp = $parts[1] ?? '';

                    if (!empty($after_bp)) {
                        pp_set_transaction_status($after_bp, 'canceled', $data['gateway']['gateway_id'], '', []);
                    }

                    echo "<script>location.href='".pp_checkout_address()."';</script>";
                }
            }
        }
        
    }
