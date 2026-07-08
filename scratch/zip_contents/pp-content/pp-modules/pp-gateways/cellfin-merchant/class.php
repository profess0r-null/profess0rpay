<?php
    class CellfinMerchantGateway
    {
        public function info()
        {
            return [
                'title'       => 'Cellfin Merchant',
                'logo'        => 'assets/logo.jpg',
                'currency'        => 'BDT',
                'tab'        => 'mfs',

                'gateway_type'        => 'automation',
                'sender_key'        => 'cellfin',
                'sender_type'        => 'Merchant',
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

        public function fields()
        {
            return [
                [
                    'name'  => 'qr_code',
                    'label' => 'Qr Code',
                    'type'  => 'image',
                ]
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
                    'en' => 'Or Scan the QR Code',
                    'bn' => 'অথবা কিউআর কোড স্ক্যান করুন',
                ],

                '5' => [
                    'en' => 'Enter Amount: {amount} {currency} and SUBMIT.',
                    'bn' => 'পরিমাণ: {amount} {currency} দিয়ে সাবমিট করুন।',
                ],

                '6' => [
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
                    'action' => [
                        'type'  => 'image',
                        'label' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-qrcode"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" /><path d="M7 17l0 .01" /><path d="M14 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" /><path d="M7 7l0 .01" /><path d="M4 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" /><path d="M17 7l0 .01" /><path d="M14 14l3 0" /><path d="M20 14l0 .01" /><path d="M14 14l0 3" /><path d="M14 20l3 0" /><path d="M17 17l3 0" /><path d="M20 17l0 3" /></svg>',
                        'value' => $data['options']['qr_code'] ?? '',
                    ]
                ],
                [
                    'icon' => '',
                    'text' => '5',
                    'copy' => true,
                    'value' => $data['transaction']['local_net_amount'],
                    'vars' => [
                        '{amount}' => $data['transaction']['local_net_amount'],
                        '{currency}' => $data['transaction']['local_currency']
                    ]
                ],
                [
                    'icon' => '',
                    'text' => '6',
                    'copy' => false
                ],

            ];
        }
    }
