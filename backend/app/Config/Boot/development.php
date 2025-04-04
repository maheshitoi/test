<?php

/*
|--------------------------------------------------------------------------
| ERROR DISPLAY
|--------------------------------------------------------------------------
| In development, we want to show as many errors as possible to help
| make sure they don't make it to production. And save us hours of
| painful debugging.
 */
error_reporting(-1);
ini_set('display_errors', '1');

/*
|--------------------------------------------------------------------------
| DEBUG BACKTRACES
|--------------------------------------------------------------------------
| If true, this constant will tell the error screens to display debug
| backtraces along with the other error information. If you would
| prefer to not see this, set this value to false.
 */
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', true);

/*
|--------------------------------------------------------------------------
| DEBUG MODE
|--------------------------------------------------------------------------
| Debug mode is an experimental flag that can allow changes throughout
| the system. This will control whether Kint is loaded, and a few other
| items. It can always be used within your own application too.
 */

defined('CI_DEBUG') || define('CI_DEBUG', true);
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('MASTER_DB_NAME', 'test');
define('MAIL_HOST', 'vps.agoo.in');
define('MAIL_USER', 'noreplay@iaoi.in');
define('MAIL_FROM_USER_NAME', 'IAOI');
define('MAIL_FROM_USER', 'noreplay@iaoi.in');
// define('MAIL_PASSWORD', 'tpqgzsovcdqbthcf');
define('MAIL_PASSWORD', '#o501ybPh');
define('MAIL_PORT', 465);
define('MAIL_PROTOCOL', 'smtp');
define('TEST_EMAIL', 'judha@itoi.org');
define('TEST_WHATSAPP','919384579716'); //santhosh whatsapp
