#!/bin/sh

#date1=`date  +%Y%m%d`
date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110518'

date2=`date -d "2 days ago" +%Y%m%d`
#date2='20110517'

prefix='100'
statdb='qzone_island_log_stat'
tempdir='/usr/local/services/tools/stat-data'
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget -q -O  ${prefix}-${date1}.log.02   http://10.135.129.208/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.03   http://10.135.129.148/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.04   http://10.135.129.149/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.05   http://10.135.130.86/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.06   http://10.135.130.87/debug/${prefix}-${date1}.log
#/usr/bin/wget -q -O  ${prefix}-${date1}.log.07   http://10.135.130.88/debug/${prefix}-${date1}.log
#/usr/bin/wget -q -O  ${prefix}-${date1}.log.08   http://10.135.130.89/debug/${prefix}-${date1}.log
#/usr/bin/wget -q -O  ${prefix}-${date1}.log.09   http://10.135.136.139/debug/${prefix}-${date1}.log

/usr/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.02  ${prefix}-${date1}.log.03  ${prefix}-${date1}.log.04  ${prefix}-${date1}.log.05  ${prefix}-${date1}.log.06


num1=`cat all-${prefix}-${date1}.log | wc -l`
num2=`cat all-${prefix}-${date1}.log | awk '{print $5}' | grep 1 | wc -l`
num3=`cat all-${prefix}-${date1}.log | awk '{print $5}' | grep 0 | wc -l`

##printf "${date1}\t${num1}\t${num2}\t${num3}\n"

num9=`/usr/local/mysql/bin/mysql -u logger -p'hfqq2010' -h 10.135.130.203  ${statdb}  -s  -e "select total_count from  day_main where log_time=${date2}" | awk '{ lf = $NF }; END{ print lf }'`
num10=`expr $num9 + $num1`

/usr/local/mysql/bin/mysql -u logger -p'hfqq2010' -h 10.135.130.203  ${statdb}  -e "insert into day_main(log_time,total_count,add_user,add_user_male,add_user_female) values(${date1},${num10},${num1},${num2},${num3})"
