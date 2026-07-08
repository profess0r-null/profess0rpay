<?php
    class StripeGateway
    {
        public function info()
        {
            return [
                'title'       => 'Stripe Gateway',
                'logo'        => 'assets/logo.jpg',
                'currency'        => 'USD',
                'tab'        => 'global',

                'gateway_type'        => 'api',
            ];
        }

        public function color()
        {
            return [
                'primary_color'        => '#635bff',
                'text_color'        => '#FFFFFF',
                'btn_color'        => '#635bff',
                'btn_text_color'        => '#FFFFFF',
            ];
        }

        public function fields()
        {
            return [
                [
                    'name'  => 'secret_key',
                    'label' => 'Stripe Secret Key',
                    'type'  => 'text',
                ],
                [
                    'name'  => 'webhook_secret',
                    'label' => 'Stripe Webhook Secret',
                    'type'  => 'text',
                ],
            ];
        }

        function process_payment($data = []){
            echo '<center><div class="spinner-border text-primary m-3 loading-123412341234" role="status"><span class="visually-hidden">Loading...</span></div></center>';

            $success_url = pp_callback_url();

            // Append properly
            $success_url .= (strpos($success_url, '?') === false ? '?' : '&') . "session_id={CHECKOUT_SESSION_ID}";

            $dataINT = [
                "payment_method_types[]" => "card",

                "line_items[0][price_data][currency]" => $data['transaction']['local_currency'],
                "line_items[0][price_data][product_data][name]" => "Digital Product",
                "line_items[0][price_data][unit_amount]" => (int) round($data['transaction']['local_net_amount'] * 100), // amount in cents ($50.00)
                "line_items[0][quantity]" => 1,
                "mode" => "payment",
                "success_url" => $success_url,
                "cancel_url" => pp_checkout_address(),

                "metadata[invoice_id]" => $data['transaction']['ref'],
            ];

            // Initialize cURL
            $ch = curl_init("https://api.stripe.com/v1/checkout/sessions");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, ($data['options']['secret_key'] ?? '') . ":"); // Basic Auth with secret key
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dataINT));

            // Execute request
            $response = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response
            $session = json_decode($response, true);

            if (isset($session['id'])) {
                //echo "Checkout session created!<br>";
                //echo "Session ID: " . $session['id'];

                echo '<script>location.href="' . $session['url'] . '";</script>';
            } else {
                echo "<center>Error creating session: ".$response."</center> <style>.loading-123412341234{display: none;}</style>";
            }
        }

        function callback($data = []){
            echo '<center><div class="spinner-border text-primary m-3 loading-123412341234" role="status"><span class="visually-hidden">Loading...</span></div></center>';

            $session_id = $_GET['session_id'] ?? '';

            $ch = curl_init("https://api.stripe.com/v1/checkout/sessions/$session_id");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, ($data['options']['secret_key'] ?? '') . ":"); // Basic Auth
            $response = curl_exec($ch);
            curl_close($ch);

            $session = json_decode($response, true);

            if (isset($session['payment_status']) && $session['payment_status'] === 'paid') {
                $payment_intent_id = $session['payment_intent'] ?? '';

                if ($payment_intent_id) {
                    $ch = curl_init("https://api.stripe.com/v1/payment_intents/$payment_intent_id");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_USERPWD, ($data['options']['secret_key'] ?? '') . ":");
                    $pi_response = curl_exec($ch);
                    curl_close($ch);

                    $payment_intent = json_decode($pi_response, true);

                    if (isset($payment_intent['status']) && $payment_intent['status'] === 'succeeded') {
                        if($session['metadata']['invoice_id'] == $data['transaction']['ref']){
                            $transactionID = $payment_intent['id'];

                            $moreinfo = [
                                [
                                    'label' => 'Session ID',
                                    'value' => $session['id']
                                ]
                            ];

                            pp_set_transaction_status($data['transaction']['ref'], 'completed', $data['gateway']['gateway_id'], $payment_intent['id'], $moreinfo);

                            echo "<script>location.reload();</script>";
                        }else{
                            echo "<center>Payment not completed or failed!</center> <style>.loading-123412341234{display: none;}</style>";
                        }
                    } else {
                        echo "<center>Payment not completed or failed!</center> <style>.loading-123412341234{display: none;}</style>";
                    }
                }
            } else {
                echo "<center>Payment not completed or failed!</center> <style>.loading-123412341234{display: none;}</style>";
            }
        }
    }
