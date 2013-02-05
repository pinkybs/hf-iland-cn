#!/bin/sh

#date1=`date -d "1 days ago" +%Y%m%d`
date1='20110715'

prefix='tutorial'
tempdir='/data/stat/island/weibo'
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget -q -O  ${prefix}-${date1}.log.01   http://192.168.0.84/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.02   http://192.168.0.86/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.03   http://192.168.0.40/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.04   http://192.168.0.41/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.05   http://192.168.0.60/debug/${prefix}-${date1}.log

/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log ${prefix}-${date1}.log.01 ${prefix}-${date1}.log.02 ${prefix}-${date1}.log.03 ${prefix}-${date1}.log.04 ${prefix}-${date1}.log.05

cd /home/admin/website/island/weibo/bin
/usr/local/php-cgi/bin/php /home/admin/website/island/weibo/bin/stat-tutorial.php
