#!/bin/sh

date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110724'

date2=`date -d "2 days ago" +%Y%m%d`
#date2='20110723'

prefix='100'
statdbhost='192.168.0.126'
statdbuser='worker'
statdb='sinawb_island_log_stat'
tempdir='/data/stat/island/weibo'
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget -q -O  ${prefix}-${date1}.log.01   http://192.168.0.84/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.02   http://192.168.0.86/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.03   http://192.168.0.40/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.04   http://192.168.0.41/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.05   http://192.168.0.60/debug/${prefix}-${date1}.log

/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01  ${prefix}-${date1}.log.02  ${prefix}-${date1}.log.03  ${prefix}-${date1}.log.04  ${prefix}-${date1}.log.05

num1=`cat all-${prefix}-${date1}.log | wc -l`
num2=`cat all-${prefix}-${date1}.log | awk '{print $5}' | grep 1 | wc -l`
num3=`cat all-${prefix}-${date1}.log | awk '{print $5}' | grep 0 | wc -l`

##printf "${date1}\t${num1}\t${num2}\t${num3}\n"

num9=`/usr/local/mysql/bin/mysql -u ${statdbuser} -p'r$6i7kP#xp' -h ${statdbhost}  ${statdb}  -s  -e "select total_count from  day_main where log_time=${date2}" | awk '{ lf = $NF }; END{ print lf }'`
num10=`expr $num9 + $num1`

/usr/local/mysql/bin/mysql -u ${statdbuser} -p'r$6i7kP#xp' -h ${statdbhost}  ${statdb}  -e "insert into day_main(log_time,total_count,add_user,add_user_male,add_user_female) values(${date1},${num10},${num1},${num2},${num3})"
