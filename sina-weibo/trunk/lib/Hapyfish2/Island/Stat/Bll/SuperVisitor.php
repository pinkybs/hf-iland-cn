<?php

class Hapyfish2_Island_Stat_Bll_SuperVisitor
{
    public static function saveSvInfoToDb($day, $time, $file)
    {
        $content = file_get_contents($file);
        if (empty($content)) {
            info_log('no data-supervisitor', 'stat.log.err');
            return;
        }
        
        $temp = explode("\n", $content);
        
        $logDate = $day;
        $data = array();
        
        $data = array('log_time' => $logDate,
                      'all_count' => 0,
                      'user_count' => 0);
        
        $demandArray = array();
        $uidList = array();
        foreach($temp as $line) {
            if (empty($line)) {
                continue;
            }
            
            $r = explode("\t", $line);
            $uid = $r[2];
            $demandId = $r[3];
            
            $key = 'demand_'.$demandId;
            if (isset($data[$key])) {
                $data[$key] += 1;
            }else {
                $data[$key] = 1;
            }
            
            $data['all_count'] += 1;
            if (!isset($uidList[$uid])) {
                $data['user_count'] += 1;
                $uidList[$uid] = $uid;
            }
        }
        
        try {
            $dalSuperVisitor = Hapyfish2_Island_Stat_Dal_SuperVisitor::getDefaultInstance();
            $dalSuperVisitor->insertSvInfo($data); 
        } catch (Exception $e) {
        }
        
        return 'ok';
    }
    
    public static function saveCollectionInfoToDb($day, $time, $file)
    {
        $content = file_get_contents($file);
        if (empty($content)) {
            info_log('no data-supervisitor', 'stat.log.err');
            return;
        }
        
        $temp = explode("\n", $content);
        
        $logDate = $day;
        $data = array();
        
        $data = array('log_time' => $logDate,
                      'all_count' => 0,
                      'user_count' => 0);
        
        $demandArray = array();
        $uidList = array();
        foreach($temp as $line) {
            if (empty($line)) {
                continue;
            }
            
            $r = explode("\t", $line);
            $uid = $r[2];
            $cid = $r[3];
            
            $key = 'cid_'.$cid;
            if (isset($data[$key])) {
                $data[$key] += 1;
            }else {
                $data[$key] = 1;
            }
        
            $data['all_count'] += 1;
            if (!isset($uidList[$uid])) {
                $data['user_count'] += 1;
                $uidList[$uid] = $uid;
            }
        }
        
        try {
            $dalSuperVisitor = Hapyfish2_Island_Stat_Dal_SuperVisitor::getDefaultInstance();
            $dalSuperVisitor->insertCollectionInfo($data); 
        } catch (Exception $e) {
        }
        
        return 'ok';
    }
    
}