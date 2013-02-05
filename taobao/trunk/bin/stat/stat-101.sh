#!/bin/sh

#date1=`date  +%Y%m%d`
#date1=`date -d "1 days ago" +%Y%m%d`
date1='20110527'

prefix='101'
statdb='taobao_island_log_stat'
tempdir='/home/admin/data/stat-data'
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget -q -O  ${prefix}-${date1}.log.01   http://192.168.15.18/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.02   http://192.168.15.19/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.03   http://192.168.15.12/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.04   http://192.168.15.13/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.05   http://192.168.15.14/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.06   http://192.168.15.20/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.07   http://192.168.15.21/debug/${prefix}-${date1}.log

/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01  ${prefix}-${date1}.log.02  ${prefix}-${date1}.log.03  ${prefix}-${date1}.log.04  ${prefix}-${date1}.log.05  ${prefix}-${date1}.log.06  ${prefix}-${date1}.log.07

num4=`cat all-${prefix}-${date1}.log | wc -l`
num5=`cat all-${prefix}-${date1}.log | awk '{print $5}' | grep 1 | wc -l`
num6=`cat all-${prefix}-${date1}.log | awk '{print $5}' | grep 0 | wc -l`

##printf "${date1}\t${num4}\t${num5}\t${num6}\n"

/home/mysql/bin/mysql -u worker -p'r$6i7kP#xp' -h 192.168.15.9  ${statdb}  -e "update day_main set active=${num4},active_male=${num5},active_male=${num5},active_female=${num6} where log_time=${date1}"

cat all-${prefix}-${date1}.log  | awk '{print $6}' | sort | uniq -c  | sort -n  -k 2 >  tmp_${date1}_level

while read i   
do  
num7=`echo $i | awk '{print $2}'`
num8=`echo $i | awk '{print $1}'`

echo -n "$num7:$num8,"  >>  ${date1}_level

done < tmp_${date1}_level

level=`cat ${date1}_level`

##printf "${date1}\t${level}\n"

/home/mysql/bin/mysql -u worker -p'r$6i7kP#xp' -h 192.168.15.9 ${statdb} -e "insert into  day_active_user_level  values('${date1}','${level}')"

######################################

/home/mysql/bin/mysql -u worker -p'r$6i7kP#xp' -h 192.168.15.9 ${statdb} -e "delete from day_user_retention where log_time=${date1}"

/home/mysql/bin/mysql -u worker -p'r$6i7kP#xp' -h 192.168.15.9 ${statdb} -e "insert into day_user_retention(log_time) values(${date1})"

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

    /home/mysql/bin/mysql -u worker -p'r$6i7kP#xp' -h 192.168.15.9 ${statdb} -e "update day_user_retention set day_${n}=${k} where log_time=${date1}"
done

