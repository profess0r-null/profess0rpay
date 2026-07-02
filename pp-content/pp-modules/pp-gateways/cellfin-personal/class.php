<?php
    class CellfinPersonalGateway
    {
        public function info()
        {
            return [
                'title'       => 'Cellfin Personal',
                'logo'        => 'assets/logo.jpg',
                'currency'        => 'BDT',
                'tab'        => 'mfs',

                'gateway_type'        => 'automation',
                'sender_key'        => 'cellfin',
                'sender_type'        => 'Personal',
            ];
        }

        public function color()
        {
            return [
                'primary_color'        => '#00803d',
                'text_color'        => '#FFFFFF',
                'btn_color'        => '#00803d',
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
                    'en' => 'Open CELLFIN App.',
                    'bn' => 'সেলফিন অ্যাপ ওপেন করুন।',
                ],

                '2' => [
                    'en' => 'Select "Fund Transfer".',
                    'bn' => '"ফান্ড ট্রান্সফার" সিলেক্ট করুন।',
                ],

                '3' => [
                    'en' => 'Enter Receiver Number: {mobile_number}',
                    'bn' => 'প্রাপক নম্বর দিন: {mobile_number}',
                ],

                '4' => [
                    'en' => 'Enter Amount: {amount} {currency} and SUBMIT.',
                    'bn' => 'পরিমাণ: {amount} {currency} দিয়ে সাবমিট করুন।',
                ],

                '5' => [
                    'en' => 'Verify with Transaction ID.',
                    'bn' => 'ট্রানজ্যাকশন আইডি দিয়ে ভেরিফাই করুন।',
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
                    'value' => $data['options']['mobile_number'],
                    'vars' => [
                        '{mobile_number}' => $data['options']['mobile_number']
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

            ];
        }
    }
