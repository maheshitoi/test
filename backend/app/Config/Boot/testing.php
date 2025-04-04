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
 | the system. It's not widely used currently, and may not survive
 | release of the framework.
 */

defined('CI_DEBUG') || define('CI_DEBUG', true);
// define('DB_USERNAME','gemsbiha_agoo');
// define('DB_PASSWORD','3RBmWpPU[chF');
// define('MASTER_DB_NAME','gemsbiha_gems_admin');
// define('PAYROLL_DB_NAME','gemsbiha_payroll');
// define('PROCESS_DB_NAME','gemsbiha_process_control');
// define('MAIL_DB_NAME','gems_mail');
// define('MAIL_HOST', 'mail.gemsbihar.online');
// define('MAIL_USER','no-reply@gemsbihar.online');
// define('MAIL_FROM_USER','no-reply@itoi.online');
// define('MAIL_FROM_USER_NAME','ITOI Test');
// define('MAIL_PASSWORD', 'pmX4esk!Adw1');
// define('MAIL_PORT', 465);
// define('MAIL_PROTOCOL','smtp');

// define('TEST_EMAIL','itoitesting01@gmail.com');
// define('TEST_WHATSAPP','918903489173');
