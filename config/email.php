<?php

return [
    
    'default' => 'sendcloud',
    
    'mailers' => [
        'sendcloud' => [
            
            'driver' => 'SendCloud',
            
            'api-user' => '',
            'api-key' => '',
            'from' => '大贸世达<damostar@126.com>',
        ]
    ]

    // SMTP SendCloud SES(Amazon) mailgun mailchimp
];
