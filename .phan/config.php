<?php

return [
    'target_php_version' => 8.2,

    'directory_list' => [
        '.',
        'vendor/easyrdf/easyrdf/lib',
    ],

    'exclude_file_regex' => '@^vendor/.*/(tests?|Tests?)/@',

    'exclude_analysis_directory_list' => [
        'vendor/'
    ],
];
