<?php
    class NagadAgentGateway
    {
        public function info()
        {
            return [
                'title'       => 'Nagad Agent',
                'logo'        => 'assets/logo.jpg',
                'currency'        => 'BDT',
                'tab'        => 'mfs',

                'gateway_type'        => 'automation',
                'sender_key'        => 'nagad',
                'sender_type'        => 'Agent',
            ];
        }

        public function color()
        {
            return [
                'primary_color'        => '#ed1c24',
                'text_color'        => '#FFFFFF',
                'btn_color'        => '#ed1c24',
                'btn_text_color'        => '#FFFFFF',
            ];
        }

        public function supported_languages()
        {
            return [
                'en' => 'English',
                'bn' => 'বাংলা',
            ];
        }

        public function lang_text()
        {
            return [
                '1' => [
                    'en' => 'Go to your Nagad Mobile App.',
                    'bn' => 'আপনার নগদ মোবাইল অ্যাপে যান।',
                ],

                '2' => [
                    'en' => 'Choose "Cash Out"',
                    'bn' => '“Cash Out” নির্বাচন করুন',
                ],

                '3' => [
                    'en' => 'Enter the Number: {mobile_number}',
                    'bn' => 'নম্বর লিখুন: {mobile_number}',
                ],

                '4' => [
                    'en' => 'Enter the Amount: {amount} {currency}',
                    'bn' => 'পরিমাণ লিখুন: {amount} {currency}',
                ],

                '5' => [
                    'en' => 'Now enter your Nagad PIN to confirm.',
                    'bn' => 'এখন নিশ্চিত করতে আপনার নগদ পিন লিখুন।',
                ],

                '6' => [
                    'en' => 'Put the Transaction ID in the box below and press Verify',
                    'bn' => 'ট্রানজ্যাকশন আইডি নিচের বক্সে লিখুন এবং যাচাই করুন চাপুন।',
                ],
            ];
        }

        public function instructions($data)
        {
            return [
                [
                    'icon' => '',
                    'text' => '1',
                    'copy' => false,
                ],
                [
                    'icon' => '',
                    'text' => '2',
                    'copy' => false
                ],
                [
                    'icon' => '',
                    'text' => '3',
                    'copy' => true,
                    'value' => $data['options']['mobile_number'] ?? '',
                    'vars' => [
                        '{mobile_number}' => $data['options']['mobile_number'] ?? ''
                    ]
                ],
                [
                    'icon' => '',
                    'text' => '4',
                    'copy' => true,
                    'value' => $data['transaction']['local_net_amount'],
                    'vars' => [
                        '{amount}' => $data['transaction']['local_net_amount'],
                        '{currency}' => $data['transaction']['local_currency']
                    ]
                ],
                [
                    'icon' => '',
                    'text' => '5',
                    'copy' => false
                ],
                [
                    'icon' => '',
                    'text' => '6',
                    'copy' => false
                ],


            ];
        }
    }
