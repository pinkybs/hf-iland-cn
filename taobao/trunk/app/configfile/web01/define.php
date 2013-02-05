<?php

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', '/home/admin/logs/island/taobao/debug');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('ENABLE_DEBUG', true);

define('SERVER_ID', '1');

define('APP_ID', '12029234');
define('APP_KEY', '12029234');
define('APP_SECRET', '96ad573ff3fef48a84b3fcf7e7da605c');
define('APP_NAME', 'island');

define('DATABASE_NODE_NUM', 8);
define('MEMCACHED_NODE_NUM', 16);

define('HOST', 'http://island.hapyfish.com');
define('STATIC_HOST', 'http://tbcdn.playwhale.com');
//define('STATIC_HOST', 'http://tbstatic.hapyfish.com');

define('SEND_ACTIVITY', false);
define('SEND_MESSAGE', true);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 1);

define('ECODE_NUM', 4);