<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use Core\Filters\Auth;
use Core\Filters\ModificationVerify;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          =>  Auth::class,
        'modifyVerify' => ModificationVerify::class
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    public array $globals = ['before' => [
            'auth'=>['except' => ['api/v1/Auth/*','api/v1/get/*','api/v1/product/getProduct','api/v1/event/getEvent','api/v1/users/isUnique/*','api/v1/users/isMobileUnique/*','api/v1/order/CustomerOrderSave','api/v1/sendOTP/*','api/v1/getAppVersion','api/v1/staff/profile_print/*']]],
        'after' => [
          
        ]];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don’t expect could bypass the filter.
     */
    public array $methods = ['post' => ['modifyVerify']];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     */
    public array $filters = [];
}
