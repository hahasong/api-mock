<?php
return [
    'method' => 'GET',
    'input' => [
        [
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'valid_email'
        ],
        [
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required'
        ]
    ],
    'output' => [
        'content_type' => 'json',
        'content' => '{}',
        'file' => 'show.1.json'
    ]

];