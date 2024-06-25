<?php

declare(strict_types=1);

return [
    'admin' => [
        'name' => 'admin',
        'type' => 'role',
        'updatedAt' => 1599036348,
        'createdAt' => 1599036348,
        'children' => [
            'editPost',
        ],
    ],
    'editPost' => [
        'name' => 'editPost',
        'type' => 'permission',
        'updatedAt' => 1599036348,
        'createdAt' => 1599036348,
    ],
    
    /**
     * Purpose: Effects read-only status of Login text box 
     * @see Auth/Controller/ChangePasswordController 'canChangePasswordForAnyUser'
     * @see views/changepassword/change $canChangePasswordForAnyUser
     */
    'canChangePasswordForAnyUser' => [
        'name' => 'canChangePasswordForAnyUser',
        'type' => 'permission',
        'updatedAt' => 1599036348,
        'createdAt' => 1599036348,
    ],
];
