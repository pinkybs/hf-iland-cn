<?php

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', '/home/admin/logs/island/weibo/debug');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('ENABLE_DEBUG', true);

define('SERVER_ID', '1001');

define('APP_ID', '401380203');
define('APP_KEY', '401380203');
define('APP_SECRET', 'd988d7bd4860c756f8af347ec815aa51');
define('APP_NAME', 'happyisland');

define('DATABASE_NODE_NUM', 8);
define('MEMCACHED_NODE_NUM', 16);

define('HOST', 'http://t.happyfishgame.com.cn');
//define('STATIC_HOST', 'http://staticwb01.happyfishgame.com.cn');
define('STATIC_HOST', 'http://static.hapyfish.com/weibo');

define('SEND_ACTIVITY', true);
define('SEND_MESSAGE', false);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 1);

define('ECODE_NUM', 4);

define('WB_RANK_FRIEND', 1);
define('WB_RANK_EXP', 2);
define('WB_RANK_COST', 3);