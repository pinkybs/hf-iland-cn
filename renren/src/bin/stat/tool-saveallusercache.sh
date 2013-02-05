#!/bin/sh

cd /home/admin/website/island/renren/bin/
/usr/local/php-cgi/bin/php -c /usr/local/php-cgi/lib/php.bin.ini /home/admin/website/island/renren/bin/tool_saveallusercache.php $1 $2
