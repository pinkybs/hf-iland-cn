<?php
define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', '/home/user_00/log/debug');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('ENABLE_DEBUG', true);

define('SERVER_ID', '1');

define('APP_ID', 610);
define('APP_KEY', '0d726d24360147da8c8079bac1837dfa');
define('APP_NAME', 'island');

define('MEMCACHED_SECTION_NUM', 16);
define('API_HOST', 'http://119.147.75.204');
define('QPOINT_PAY_HOST_GET', 'http://119.147.75.204');
define('QPOINT_PAY_HOST_BUY', 'https://119.147.75.204');

define('HOST', 'http://main.island.qzoneapp.com');
define('PENGYOU_HOST', 'http://main.island.qzoneapp.com');
define('QZONE_HOST', 'http://island.qzone.qzoneapp.com');
define('STATIC_HOST', 'http://main.island.qzoneapp.com/static');

define('ECODE_NUM', 4);

define('SEND_ACTIVITY', false);
define('SEND_MESSAGE', false);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 1);

define('PLATFORM_SOURCE', '2');//1 Qzone  2 pengyou