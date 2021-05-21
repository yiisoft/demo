<?php

return [
    'admin' => [
        'name' => 'admin',
        'type' => 'role',
        'updatedAt' => 1599036348,
        'createdAt' => 1599036348,
        'children' => [
            'editPost',
            'editClient',
            'editSetting'
        ],
    ],
    'editPost' => [
        'name' => 'editPost',
        'type' => 'permission',
        'updatedAt' => 1599036348,
        'createdAt' => 1599036348,
    ],
    'editClient' => [
        'name' => 'editClient',
        'type' => 'permission',
        'updatedAt' => 1599036348,
        'createdAt' => 1599036348,
    ],
    'editSetting' => [
        'name' => 'editSetting',
        'type' => 'permission',
        'updatedAt' => 1599036348,
        'createdAt' => 1599036348,
    ],
    
];
