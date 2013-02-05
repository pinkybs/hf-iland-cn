<?php

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('ENABLE_DEBUG', true);

define('SERVER_ID', '9');

define('APP_ID', '1142074347');
define('APP_KEY', '1142074347');
define('APP_SECRET', 'bd178abd634fa4e336642d4f25c6084d');
define('APP_NAME', 'island_dev');

define('DATABASE_NODE_NUM', 4);
define('MEMCACHED_NODE_NUM', 10);

define('HOST', 'http://weibo-t1.happyfish001.com');
define('STATIC_HOST', 'http://testislandstatic.happyfish001.com/weibo');
//define('STATIC_HOST', 'http://static.hapyfish.com/weibo');

define('SEND_ACTIVITY', true);
define('SEND_MESSAGE', false);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 1);

define('ECODE_NUM', 4);

define('WB_RANK_FRIEND', 1);
define('WB_RANK_EXP', 2);
define('WB_RANK_COST', 3);