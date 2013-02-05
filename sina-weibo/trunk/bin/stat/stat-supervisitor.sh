#!/bin/sh

#date1=`date -d "1 days ago" +%Y%m%d`
date1='20110920'

prefix='503'
tempdir='/data/stat/island/weibo'
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget -q -O  ${prefix}-${date1}.log.01   http://192.168.0.84/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.02   http://192.168.0.86/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.03   http://192.168.0.40/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.04   http://192.168.0.41/debug/${prefix}-${date1}.log

/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log ${prefix}-${date1}.log.01 ${prefix}-${date1}.log.02 ${prefix}-${date1}.log.03 ${prefix}-${date1}.log.04
 
prefixcollection='505'
tempdir='/data/stat/island/weibo'
rm -rf  ${tempdir}/${prefixcollection}/${date1}
mkdir -p -m 777  ${tempdir}/${prefixcollection}/${date1}
cd ${tempdir}/${prefixcollection}/${date1}

/usr/bin/wget -q -O  ${prefixcollection}-${date1}.log.01   http://192.168.0.84/debug/${prefixcollection}-${date1}.log
/usr/bin/wget -q -O  ${prefixcollection}-${date1}.log.02   http://192.168.0.86/debug/${prefixcollection}-${date1}.log
/usr/bin/wget -q -O  ${prefixcollection}-${date1}.log.03   http://192.168.0.40/debug/${prefixcollection}-${date1}.log
/usr/bin/wget -q -O  ${prefixcollection}-${date1}.log.04   http://192.168.0.41/debug/${prefixcollection}-${date1}.log

/bin/sort -m -t " " -k 1 -o all-${prefixcollection}-${date1}.log ${prefixcollection}-${date1}.log.01 ${prefixcollection}-${date1}.log.02 ${prefixcollection}-${date1}.log.03 ${prefixcollection}-${date1}.log.04

