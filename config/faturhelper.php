<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    */

    'models' => [
        'menuheader' => \Ajifatur\FaturHelper\Models\MenuHeader::class,
        'menuitem' => \Ajifatur\FaturHelper\Models\MenuItem::class,
        'meta' => \Ajifatur\FaturHelper\Models\Meta::class,
        'permission' => \Ajifatur\FaturHelper\Models\Permission::class,
        'role' => \Ajifatur\FaturHelper\Models\Role::class,
        'setting' => \Ajifatur\FaturHelper\Models\Setting::class,
        'user' => \Ajifatur\FaturHelper\Models\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    |
    */

    'auth' => [
        'allow_login_by_email' => false,
        'non_admin_can_login' => false,
        'socialite' => false
    ],
    
];