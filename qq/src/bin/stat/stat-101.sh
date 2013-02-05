#!/bin/sh

#date1=`date  +%Y%m%d`
date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110518'

prefix='101'
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

num4=`cat all-${prefix}-${date1}.log | wc -l`
num5=`cat all-${prefix}-${date1}.log | awk '{print $5}' | grep 1 | wc -l`
num6=`cat all-${prefix}-${date1}.log | awk '{print $5}' | grep 0 | wc -l`

##printf "${date1}\t${num4}\t${num5}\t${num6}\n"

/usr/local/mysql/bin/mysql -u logger -p'hfqq2010' -h 10.135.130.203  ${statdb}  -e "update day_main set active=${num4},active_male=${num5},active_male=${num5},active_female=${num6} where log_time=${date1}"

cat all-${prefix}-${date1}.log  | awk '{print $6}' | sort | uniq -c  | sort -n  -k 2 >  tmp_${date1}_level

while read i   
do  
num7=`echo $i | awk '{print $2}'`
num8=`echo $i | awk '{print $1}'`

echo -n "$num7:$num8,"  >>  ${date1}_level

done < tmp_${date1}_level

level=`cat ${date1}_level`

##printf "${date1}\t${level}\n"

/usr/local/mysql/bin/mysql -u logger -p'hfqq2010' -h 10.135.130.203 ${statdb} -e "insert into  day_active_user_level  values('${date1}','${level}')"

######################################

/usr/local/mysql/bin/mysql -u logger -p'hfqq2010' -h 10.135.130.203 ${statdb} -e "delete from day_user_retention where log_time=${date1}"

/usr/local/mysql/bin/mysql -u logger -p'hfqq2010' -h 10.135.130.203 ${statdb} -e "insert into day_user_retention(log_time) values(${date1})"

s=0
j=0
k=0
n=0

for (( i=1;  i<=30;  i=i+1 ))
do
    s=$(date -d "$i day ago 00:00:00" +%s)
    #s=`expr $s - 172800`
    j=`expr $s - 86400`
    n=`expr $n + 1`
    k=`cat all-${prefix}-${date1}.log | awk '$4>"'$j'" && $4 < "'$s'" {print $1}' | wc -l`
    
    ##printf "${n}:${k}\t"

    /usr/local/mysql/bin/mysql -u logger -p'hfqq2010' -h 10.135.130.203 ${statdb} -e "update day_user_retention set day_${n}=${k} where log_time=${date1}"
done
