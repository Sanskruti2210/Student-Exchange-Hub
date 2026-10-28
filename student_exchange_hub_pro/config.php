<?php
/*
* Local configuration. For production, set these values in your hosting environment
* or edit this file outside the public web root. Never commit real credentials.
*/
define('DB_HOST', getenv('SEH_DB_HOST') ?: '127.0.0.1');
define('DB_PORT', (int)(getenv('SEH_DB_PORT') ?: 3306));
define('DB_NAME', getenv('SEH_DB_NAME') ?: 'student_exchange_hub_pro');
define('DB_USER', getenv('SEH_DB_USER') ?: 'root');
define('DB_PASS', getenv('SEH_DB_PASS') !== false ? getenv('SEH_DB_PASS') : '');
define('APP_NAME', 'Student Exchange Hub Pro');
define('APP_ENV', getenv('SEH_APP_ENV') ?: 'development');
