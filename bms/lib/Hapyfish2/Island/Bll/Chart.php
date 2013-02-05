<?php

class Hapyfish2_Island_Bll_Chart
{
	public static function createMainContent($info, $reverse = false)
	{
		if ($reverse) {
			$info = array_reverse($info);
		}
		$count = count($info);
		$beginTime = strtotime($info[0]['log_time']);
		$endTime = strtotime($info[$count - 1]['log_time']);
		$beginDay = date('Y-m-d', $beginTime);
		$endDay = date('Y-m-d', $endTime);

		require_once('OFC/OFC_Chart.php');
		$title = new OFC_Elements_Title('活跃用户/新增用户 趋势图');
		$title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');

		//x轴开始的数字（日期尾数）
		$minx = 1;
		//x轴结束位置
		$maxx = $count;

		$addCntArr = array();
		$activeCntArr = array();
		$activeSeconddayCntArr = array();

		$dateArr = array();
		$lables = array('');
		$y_axis_maxV = 0;

		foreach ($info as $k => $v) {
			$t = strtotime($v['log_time']);
			$dateArr[] = date("Y-m-d", $t);
			$lables[] = date("Y-m-d", $t);
			//get y_axis maxValue and stepValue
			$y_axis_maxV = $v['add_user'] > $y_axis_maxV ? $v['add_user'] : $y_axis_maxV;
			$y_axis_maxV = $v['active'] > $y_axis_maxV ? $v['active'] : $y_axis_maxV;
		}

		$y_axis_stepV = round($y_axis_maxV/5);
		$y_axis_maxV = $y_axis_stepV*6;

		//将该日用户数为0 的 补入$aryDayInfo
		for ($i = 0; $i < $count; $i++) {
			$index = array_search(date("Y-m-d", $beginTime + $i * 3600 * 24), $dateArr);
			if ($index === false ) {
				$addCntArr[] = 0;
				$activeCntArr[] = 0;
				$activeSeconddayCntArr[] = 0;
			} else {
				$addCntArr[] = (int)$info[$index]['add_user'];
				$activeCntArr[] = (int)$info[$index]['active'];
                $activeSeconddayCntArr[] = (int)$info[$index]['active_secondday'];
			}
		}

		$dot1 = new OFC_Charts_Dot_Solid();
		$dot1->size(3);
		$line_dot_add = new OFC_Charts_Line();
		$line_dot_add->set_values($addCntArr);
		$line_dot_add->set_colour('#0066FF');
		$line_dot_add->set_key('日增长用户', 12);
		$line_dot_add->set_default_dot_style($dot1);

		$line_dot_active = new OFC_Charts_Line();
		$line_dot_active->set_values($activeCntArr);
		$line_dot_active->set_colour('#FF3300');
		$line_dot_active->set_key('日活跃用户-旧', 12);
		$line_dot_active->set_colour('#ff0000');
		$line_dot_active->set_default_dot_style($dot1);

        $line_dot_active_secondday = new OFC_Charts_Line();
        $line_dot_active_secondday->set_values($activeSeconddayCntArr);
        $line_dot_active_secondday->set_colour('#009933');
        $line_dot_active_secondday->set_key('日活跃用户-新(历史登录两天以上)', 12);
        $line_dot_active_secondday->set_colour('#009933');
        $line_dot_active_secondday->set_default_dot_style($dot1);

		$chart = new OFC_Chart();
		$chart->set_title($title);
		$chart->set_bg_colour('#fafafa');
		$chart->add_element($line_dot_active);
        $chart->add_element($line_dot_active_secondday);
		$chart->add_element($line_dot_add);

		$y_axis = new OFC_Elements_Axis_Y();
		$y_axis->set_colours('#000000', '#c1c1c1');
		$y_axis->set_range(0, $y_axis_maxV, $y_axis_stepV);
		$y_axis->labels = null;
		$y_axis->set_offset( false );

		$x_axis = new OFC_Elements_Axis_X();
		$x_axis->set_colours('#000000', '#c1c1c1');
		$x_axis->set_labels_from_array($lables, 30);
		//$x_axis->labels = null;
		//x 坐标间隔
		//$x_axis->set_steps( 0.8 );
		$x_axis->set_range( $minx, $maxx, 1 );

		//$x_labels = new OFC_Elements_Axis_X_Label_Set();
		//x背景网格间隔
		//$x_labels->set_steps( 0.5 );
		//$x_labels->set_vertical();
		//Add the X Axis Labels to the X Axis
		//$x_axis->set_labels( $x_labels );

		$chart->add_y_axis($y_axis);
		$chart->x_axis = $x_axis;

		//get json data
		$fstream = $chart->toString();

		return $fstream;
	}

	public static function createActiveUserLevelContent($day, $info, $type = 1)
	{
		if ( $type == 2 ) {
			$title = '所有用户等级分布';
		}
        else if( $type == 3 ) {
            $title = '每日升级人数';
        }
        else if ( $type == 4 ) {
        	$title = '七天未登录用户-等级分布';
        }
        else if ( $type == 5 ) {
            $title = '七天未登录用户-爱心分布';
        }
		else {
			$title = '活跃用户等级分布';
		}


		$count = count($info);
		$time = strtotime($day);
		$d = date('Y-m-d', $time);

		require_once('OFC/OFC_Chart.php');
		$title = new OFC_Elements_Title($title.'(' . $d . ')');
		$title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');
		$data = array(null);
		$labels = array('', '0');

		$baseLevel = 1;
		$y_axis_maxV = 0;
		foreach ($info as $k => $v) {
			$levelAdd = $v['level'] - $baseLevel;
			for ($i = 0; $i < $levelAdd-1; $i++) {
				$data[] = null;
				$labels[] = '' . ($baseLevel + $i + 1);
			}
			$baseLevel += $levelAdd;
			$data[] = (int)$v['count'];
			$labels[] = $v['level'];
			$y_axis_maxV = $v['count'] > $y_axis_maxV ? $v['count'] : $y_axis_maxV;
		}

		$y_axis_stepV = round($y_axis_maxV/5);
		$y_axis_maxV = $y_axis_stepV*6;
		$minx = (int)$info[0]['level'];
		$maxx = (int)$info[$count-1]['level'] + 1;

		$bar = new OFC_Charts_Bar_3d();
		$bar->set_values($data);
		$bar->colour = '#D54C78';

		$y_axis = new OFC_Elements_Axis_Y();
		$y_axis->set_range(0, $y_axis_maxV, $y_axis_stepV);
		$y_axis->set_colours('#000000', '#CDCDCD');
		//$y_axis->labels = ( array(1,2,3,4,5,6,7,8,10) );
		$y_axis->set_offset(false);

		$x_axis = new OFC_Elements_Axis_X();
		$x_axis->set_3d(5);
		$x_axis->set_colours('#909090', '#FAFAFA');
		$x_axis->set_labels_from_array($labels);
		$x_axis->set_range($minx, $maxx, 1);

		$chart = new OFC_Chart();
		$chart->set_title($title);
		$chart->set_bg_colour('#FAFAFA');
		$chart->add_element($bar);
		$chart->add_y_axis($y_axis);
		$chart->x_axis = $x_axis;

		//get json data
		$fstream = $chart->toString();

		return $fstream;
	}

	public static function createDayContent($day, $info, $type = 1)
	{
        if ( $type == 1 ) {
            $title = '佣兵-雇佣-各佣兵星级分布';
        }
        else if ( $type == 2 ) {
            $title = '佣兵-雇佣-雇佣时用户经营等级分布';
        }
        else if ( $type == 3 ) {
            $title = '佣兵-雇佣-雇佣时主角战斗等级分布';
        }
        else if ( $type == 4 ) {
            $title = '佣兵-培养-所培养佣兵等级分布';
        }
        else if ( $type == 5 ) {
            $title = '道具-各道具使用情况分布';
        }
        else if ( $type == 6 ) {
            $title = '商店-物品购买个数分布';
        }
        else if ( $type == 7 ) {
            $title = '合成-合成物品个数分布';
        }
		else {
			$title = 'test';
		}

		$count = count($info);
		$time = strtotime($day);
		$d = date('Y-m-d', $time);

		require_once('OFC/OFC_Chart.php');
		$title = new OFC_Elements_Title($title.'(' . $d . ')');
		$title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');
		if ( $type == 5 || $type == 6 || $type == 7 ) {
			$labels = array('');
			$data = array();
		}
		else {
			$labels = array('', '0');
			$data = array(null);
		}

		$baseLevel = 1;
		$y_axis_maxV = 0;
		foreach ($info as $k => $v) {
			$levelAdd = $v['level'] - $baseLevel;
			for ($i = 0; $i < $levelAdd-1; $i++) {
				$data[] = null;
				$labels[] = '' . ($baseLevel + $i + 1);
			}
			$baseLevel += $levelAdd;
			$data[] = (int)$v['count'];
			if ( $type == 5 ) {
				$labels[] = $v['cid'];
			}
			else if ( $type == 6 || $type == 7 ) {
				$labels[] = $v['cid'];
			}
			else {
				$labels[] = $v['level'];
			}
			
			$y_axis_maxV = $v['count'] > $y_axis_maxV ? $v['count'] : $y_axis_maxV;
		}

		$y_axis_stepV = round($y_axis_maxV/5);
		$y_axis_maxV = $y_axis_stepV*6;
		$minx = (int)$info[0]['level'];
		$maxx = (int)$info[$count-1]['level'] + 1;

		$bar = new OFC_Charts_Bar_3d();
		$bar->set_values($data);
		$bar->colour = '#D54C78';

		$y_axis = new OFC_Elements_Axis_Y();
		$y_axis->set_range(0, $y_axis_maxV, $y_axis_stepV);
		$y_axis->set_colours('#000000', '#CDCDCD');
		//$y_axis->labels = ( array(1,2,3,4,5,6,7,8,10) );
		$y_axis->set_offset(false);

		$x_axis = new OFC_Elements_Axis_X();
		$x_axis->set_3d(5);
		$x_axis->set_colours('#909090', '#FAFAFA');
		$x_axis->set_labels_from_array($labels);
		$x_axis->set_range($minx, $maxx, 1);

		$chart = new OFC_Chart();
		$chart->set_title($title);
		$chart->set_bg_colour('#FAFAFA');
		$chart->add_element($bar);
		$chart->add_y_axis($y_axis);
		$chart->x_axis = $x_axis;

		//get json data
		$fstream = $chart->toString();

		return $fstream;
	}
	
    public static function createPaylist($day, $info, $type = 1)
    {
        $count = count($info);
        $time = strtotime($day);
        $d = date('Y-m-d', $time);

        if ( $type == 1 ) {
            $title = '各充值额度次数分布（全程数据）';
	        $minx = (int)$info[0]['level'];
	        $maxx = (int)30;
            $labels = array('', '', '', '', '', '', '', '', '', '', '');
        }
        else if ( $type == 2 ) {
            $title = '每日首次充值的等级分布（每日数据）';
            $minx = (int)0;
            $maxx = (int)$info[$count-1]['level'] + 1;
            $labels = array('');
        }
        else if ( $type == 3 ) {
            $title = '所有等级玩家充值次数（全程数据）';
            $minx = (int)$info[0]['level'];
            $maxx = (int)$info[$count-1]['level'] + 1;
            $labels = array('', '0');
        }
        else {
            $minx = (int)$info[0]['level'];
            $maxx = (int)$info[$count-1]['level'] + 1;
            $labels = array('', '0');
        }

        require_once('OFC/OFC_Chart.php');
        $title = new OFC_Elements_Title($title.'(' . $d . ')');
        $title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');
        $data = array(null);

        $baseLevel = 1;
        $y_axis_maxV = 0;
        foreach ($info as $k => $v) {
            $levelAdd = $v['level'] - $baseLevel;
            /*for ($i = 0; $i < $levelAdd-1; $i++) {
                $data[] = null;
                $labels[] = '' . ($baseLevel + $i + 1);
            }*/
            if ( $type == 1 || $type == 2 ) {
	            $data[] = null;
	            $labels[] = '';
            }
            else if ( $type == 3 ) {
	            for ($i = 0; $i < $levelAdd-1; $i++) {
	                $data[] = null;
	                $labels[] = '' . ($baseLevel + $i + 1);
	            }
            }
            else {
                $data[] = null;
                $labels[] = '' . ($baseLevel + $i + 1);
            }
            $baseLevel += $levelAdd;
            $data[] = (int)$v['count'];
            $labels[] = $v['level'];
            $y_axis_maxV = $v['count'] > $y_axis_maxV ? $v['count'] : $y_axis_maxV;
        }
        $y_axis_stepV = round($y_axis_maxV/5);
        $y_axis_stepV = $y_axis_stepV > 0 ? $y_axis_stepV : 1;
        $y_axis_maxV = $y_axis_stepV*6;

        $bar = new OFC_Charts_Bar_3d();
        $bar->set_values($data);
        $bar->colour = '#D54C78';

        $y_axis = new OFC_Elements_Axis_Y();
        $y_axis->set_range(0, $y_axis_maxV, $y_axis_stepV);
        $y_axis->set_colours('#000000', '#CDCDCD');
        //$y_axis->labels = ( array(1,2,3,4,5,6,7,8,10) );
        $y_axis->set_offset(false);

        $x_axis = new OFC_Elements_Axis_X();
        $x_axis->set_3d(5);
        $x_axis->set_colours('#909090', '#FAFAFA');
        $x_axis->set_labels_from_array($labels);
        $x_axis->set_range($minx, $maxx, 1);

        $chart = new OFC_Chart();
        $chart->set_title($title);
        $chart->set_bg_colour('#FAFAFA');
        $chart->add_element($bar);
        $chart->add_y_axis($y_axis);
        $chart->x_axis = $x_axis;

        //get json data
        $fstream = $chart->toString();

        return $fstream;
    }

	public static function createAddUserHour($day, $info)
	{
		$count = count($info);
		$time = strtotime($day);
		$d = date('Y-m-d', $time);

		require_once('OFC/OFC_Chart.php');
		$title = new OFC_Elements_Title('新增用户小时分布(' . $d . ')');
		$title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');

		//x轴开始的数字（日期尾数）
		$minx = 1;
		//x轴结束位置
		$maxx = $count;

		$addCntArr = array();

		$lables = array('');
		$y_axis_maxV = 0;
		$i = 1;

		foreach ($info as $k => $v) {
			$addCntArr[] = (int)$v['add_user'];
			$t = strtotime($v['log_time']);
			$lables[] = $i . '';
			$i++;
			//get y_axis maxValue and stepValue
			$y_axis_maxV = $v['add_user'] > $y_axis_maxV ? $v['add_user'] : $y_axis_maxV;
		}

		$y_axis_stepV = round($y_axis_maxV/5);
		$y_axis_maxV = $y_axis_stepV*6;

		$line_dot_add = new OFC_Charts_Line();
		$line_dot_add->set_values($addCntArr);

		$chart = new OFC_Chart();
		$chart->set_title($title);
		$chart->set_bg_colour('#fafafa');
		$chart->add_element($line_dot_add);

		$y_axis = new OFC_Elements_Axis_Y();
		$y_axis->set_range(0, $y_axis_maxV, $y_axis_stepV);
		$y_axis->labels = null;
		$y_axis->set_offset( false );
		$y_axis->set_colours('#000000', '#CDCDCD');

		$x_axis = new OFC_Elements_Axis_X();
		$x_axis->set_labels_from_array($lables);
		$x_axis->set_range( $minx, $maxx, 1 );
		$x_axis->set_colours('#000000', '#CDCDCD');

		$chart->add_y_axis($y_axis);
		$chart->x_axis = $x_axis;

		//get json data
		$fstream = $chart->toString();

		return $fstream;
	}

	public static function createActiveUserHour($day, $info)
	{
		$count = count($info);
		$time = strtotime($day);
		$d = date('Y-m-d', $time);

		require_once('OFC/OFC_Chart.php');
		$title = new OFC_Elements_Title('用户每日首次登陆分布(' . $d . ')');
		$title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');

		//x轴开始的数字（日期尾数）
		$minx = 1;
		//x轴结束位置
		$maxx = $count;

		$activeCntArr = array();

		$lables = array('');
		$y_axis_maxV = 0;
		$i = 1;

		foreach ($info as $k => $v) {
			$activeCntArr[] = (int)$v['active_user'];
			$t = strtotime($v['log_time']);
			$lables[] = $i . '';
			$i++;
			//get y_axis maxValue and stepValue
			$y_axis_maxV = $v['active_user'] > $y_axis_maxV ? $v['active_user'] : $y_axis_maxV;
		}

		$y_axis_stepV = round($y_axis_maxV/5);
		$y_axis_maxV = $y_axis_stepV*6;

		$line_dot_active = new OFC_Charts_Line();
		$line_dot_active->set_values($activeCntArr);
		$line_dot_active->set_colour('#ff0000');

		$chart = new OFC_Chart();
		$chart->set_title($title);
		$chart->set_bg_colour('#fafafa');
		$chart->add_element($line_dot_active);

		$y_axis = new OFC_Elements_Axis_Y();
		$y_axis->set_range(0, $y_axis_maxV, $y_axis_stepV);
		$y_axis->labels = null;
		$y_axis->set_offset( false );
		$y_axis->set_colours('#000000', '#CDCDCD');

		$x_axis = new OFC_Elements_Axis_X();
		$x_axis->set_labels_from_array($lables);
		$x_axis->set_range( $minx, $maxx, 1 );
		$x_axis->set_colours('#000000', '#CDCDCD');

		$chart->add_y_axis($y_axis);
		$chart->x_axis = $x_axis;

		//get json data
		$fstream = $chart->toString();

		return $fstream;
	}

	public static function createRetention($info, $reverse = false, $dayIndex = '7', $color = '#0000ff')
	{
		if ($reverse) {
			$info = array_reverse($info);
		}

		$count = count($info);
		$beginTime = strtotime($info[0]['log_time']);
		$endTime = strtotime($info[$count - 1]['log_time']);

		require_once('OFC/OFC_Chart.php');
		$title = new OFC_Elements_Title('保留率趋势图(day' . $dayIndex . ')');
		$title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');

		$minx = 1;
		$maxx = $count;

		$miny = 0;
		$maxy = 0;
		$stepy = 0;

		$lables = array('');
		$dataArr = array();

		foreach ($info as $k => $v) {
			$t = strtotime($v['log_time']);
			$lables[] = date("Y-m-d", $t);
			if ($v['day_' . $dayIndex] == '0') {
				$dataArr[] = null;
				$r = 0;
			} else {
				$r = round($v['day_' . $dayIndex]*10000/$v['add_user'])/100;
				$dataArr[] = $r;
			}
			$maxy = $r > $maxy ? $r : $maxy;
		}

		$stepy = round($maxy/5);
		$maxy = $stepy * 6;

		$dot1 = new OFC_Charts_Dot_Solid();
		$dot1->size(3);
		$line_dot_retention = new OFC_Charts_Line();
		$line_dot_retention->set_values($dataArr);
		//$line_dot_retention->set_key($dayIndex . '天保留率', 12);
		$line_dot_retention->set_colour($color);
		$line_dot_retention->set_default_dot_style($dot1);

		$chart = new OFC_Chart();
		$chart->set_title($title);
		$chart->set_bg_colour('#fafafa');
		$chart->add_element($line_dot_retention);

		$y_axis = new OFC_Elements_Axis_Y();
		$y_axis->set_range(0, $maxy, $stepy);
		$y_axis->set_colours('#000000', '#c1c1c1');
		$y_axis->labels = null;
		$y_axis->set_offset( false );

		$x_axis = new OFC_Elements_Axis_X();
		$x_axis->set_labels_from_array($lables, 30);
		$x_axis->set_colours('#000000', '#c1c1c1');
		$x_axis->set_range( $minx, $maxx, 1 );

		$chart->add_y_axis($y_axis);
		$chart->x_axis = $x_axis;

		//get json data
		$fstream = $chart->toString();

		return $fstream;

	}

    public static function createDonateSpread($day, $info)
    {
        $count = count($info);
        $time = strtotime($day);
        $d = date('Y-m-d', $time);

        $title = '捐赠额度分布';
        $minx = (int)0;
        $maxx = 5;

        require_once('OFC/OFC_Chart.php');
        $title = new OFC_Elements_Title($title.'(' . $d . ')');
        $title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');
        $data = $labels = array();

        $baseLevel = 1;
        $y_axis_maxV = 0;
        foreach ($info as $k => $v) {
            $data[] = (int)$v['count'];
            $labels[] = $v['level'];
            $y_axis_maxV = $v['count'] > $y_axis_maxV ? $v['count'] : $y_axis_maxV;
        }
        $y_axis_stepV = round($y_axis_maxV/5);
        $y_axis_stepV = $y_axis_stepV > 0 ? $y_axis_stepV : 1;
        $y_axis_maxV = $y_axis_stepV*6;

        $bar = new OFC_Charts_Bar_3d();
        $bar->set_values($data);
        $bar->colour = '#D54C78';

        $y_axis = new OFC_Elements_Axis_Y();
        $y_axis->set_range(0, $y_axis_maxV, $y_axis_stepV);
        $y_axis->set_colours('#000000', '#CDCDCD');
        //$y_axis->labels = ( array(1,2,3,4,5,6,7,8,10) );
        $y_axis->set_offset(false);

        $x_axis = new OFC_Elements_Axis_X();
        $x_axis->set_3d(5);
        $x_axis->set_colours('#909090', '#FAFAFA');
        $x_axis->set_labels_from_array($labels);
        $x_axis->set_range($minx, $maxx, 1);

        $chart = new OFC_Chart();
        $chart->set_title($title);
        $chart->set_bg_colour('#FAFAFA');
        $chart->add_element($bar);
        $chart->add_y_axis($y_axis);
        $chart->x_axis = $x_axis;

        //get json data
        $fstream = $chart->toString();

        return $fstream;
    }

    public static function createDonate($info)
	{

		$count = count($info);
		$beginTime = strtotime($info[0]['log_time']);
		$endTime = strtotime($info[$count - 1]['log_time']);

		require_once('OFC/OFC_Chart.php');
		$title = new OFC_Elements_Title('捐赠金额走势图');
		$title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');

		$minx = 1;
		$maxx = $count;

		$miny = 0;
		$maxy = 0;
		$stepy = 0;

		$lables = array('');
		$dataArr = array();

		foreach ($info as $k => $v) {
			$t = strtotime($v['log_time']);
			$lables[] = date("Y-m-d", $t);
			//all donate
			$tmp = $v['donate'];
			$r = $tmp['0']; //$tmp['1'] $tmp['5'] $tmp['10'] $tmp['50'] $tmp['100']
			$dataArr[] = $r;
			$maxy = $r > $maxy ? $r : $maxy;
		}

		$stepy = round($maxy/5);
		$maxy = $stepy * 6;

		$dot1 = new OFC_Charts_Dot_Solid();
		$dot1->size(3);
		$line_dot_retention = new OFC_Charts_Line();
		$line_dot_retention->set_values($dataArr);
		$line_dot_retention->set_colour('#0000ff');
		$line_dot_retention->set_default_dot_style($dot1);

		$chart = new OFC_Chart();
		$chart->set_title($title);
		$chart->set_bg_colour('#fafafa');
		$chart->add_element($line_dot_retention);

		$y_axis = new OFC_Elements_Axis_Y();
		$y_axis->set_range(0, $maxy, $stepy);
		$y_axis->set_colours('#000000', '#c1c1c1');
		$y_axis->labels = null;
		$y_axis->set_offset( false );

		$x_axis = new OFC_Elements_Axis_X();
		$x_axis->set_labels_from_array($lables, 30);
		$x_axis->set_colours('#000000', '#c1c1c1');
		$x_axis->set_range( $minx, $maxx, 1 );

		$chart->add_y_axis($y_axis);
		$chart->x_axis = $x_axis;

		//get json data
		$fstream = $chart->toString();

		return $fstream;
	}
	
	public static function createDayFight($day, $info, $map, $type = 1)
	{
        if ( $type == 1 ) {
            $title = '战斗-进入副本'.$map.'-经营等级分布';
        }
        else if ( $type == 2 ) {
            $title = '战斗-进入副本'.$map.'-战斗等级分布';
        }

		$count = count($info);
		$time = strtotime($day);
		$d = date('Y-m-d', $time);

		require_once('OFC/OFC_Chart.php');
		$title = new OFC_Elements_Title($title.'(' . $d . ')');
		$title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');
		if ( $type == 5 ) {
			$labels = array('');
			$data = array();
		}
		else {
			$labels = array('', '0');
			$data = array(null);
		}

		$baseLevel = 1;
		$y_axis_maxV = 0;
		foreach ($info as $k => $v) {
			$levelAdd = $v['level'] - $baseLevel;
			for ($i = 0; $i < $levelAdd-1; $i++) {
				$data[] = null;
				$labels[] = '' . ($baseLevel + $i + 1);
			}
			$baseLevel += $levelAdd;
			$data[] = (int)$v['count'];
			if ( $type == 5 ) {
				$labels[] = $v['cid'];
			}
			else {
				$labels[] = $v['level'];
			}
			
			$y_axis_maxV = $v['count'] > $y_axis_maxV ? $v['count'] : $y_axis_maxV;
		}

		$y_axis_stepV = round($y_axis_maxV/5);
		$y_axis_maxV = $y_axis_stepV*6;
		$minx = (int)$info[0]['level'];
		$maxx = (int)$info[$count-1]['level'] + 1;

		$bar = new OFC_Charts_Bar_3d();
		$bar->set_values($data);
		$bar->colour = '#D54C78';

		$y_axis = new OFC_Elements_Axis_Y();
		$y_axis->set_range(0, $y_axis_maxV, $y_axis_stepV);
		$y_axis->set_colours('#000000', '#CDCDCD');
		//$y_axis->labels = ( array(1,2,3,4,5,6,7,8,10) );
		$y_axis->set_offset(false);

		$x_axis = new OFC_Elements_Axis_X();
		$x_axis->set_3d(5);
		$x_axis->set_colours('#909090', '#FAFAFA');
		$x_axis->set_labels_from_array($labels);
		$x_axis->set_range($minx, $maxx, 1);

		$chart = new OFC_Chart();
		$chart->set_title($title);
		$chart->set_bg_colour('#FAFAFA');
		$chart->add_element($bar);
		$chart->add_y_axis($y_axis);
		$chart->x_axis = $x_axis;

		//get json data
		$fstream = $chart->toString();

		return $fstream;
	}
	
	public static function createDayUpgrade($day, $info, $type)
	{
		 if ( $type == 1 ) {
            $title = '主角屋等级分布';
        }
        else if ( $type == 2 ) {
            $title = '酒馆1等级分布';
        }
 		else if ( $type == 3 ) {
            $title = '酒馆2等级分布';
        }
         else if ( $type == 4 ) {
             $title = '酒馆3等级分布';
        }
         else if ( $type == 5 ) {
            $title = '铁匠铺等级分布';
        }
        
		$count = count($info);
		$time = strtotime($day);
		$d = date('Y-m-d', $time);

		require_once('OFC/OFC_Chart.php');
		$title = new OFC_Elements_Title($title.'(' . $d . ')');
		$title->set_style('{font-size:12px;font-weight:bold;margin:10px;}');
		$labels = array('', '0');
		$data = array(null);

		$baseLevel = 1;
		$y_axis_maxV = 0;
		foreach ($info as $k => $v) {
			$levelAdd = $v['level'] - $baseLevel;
			for ($i = 0; $i < $levelAdd-1; $i++) {
				$data[] = null;
				$labels[] = '' . ($baseLevel + $i + 1);
			}
			$baseLevel += $levelAdd;
			$data[] = (int)$v['count'];
			$labels[] = $v['level'];
			
			$y_axis_maxV = $v['count'] > $y_axis_maxV ? $v['count'] : $y_axis_maxV;
		}

		$y_axis_stepV = round($y_axis_maxV/5);
		$y_axis_maxV = $y_axis_stepV*6;
		$minx = (int)$info[0]['level'];
		$maxx = (int)$info[$count-1]['level'] + 1;

		$bar = new OFC_Charts_Bar_3d();
		$bar->set_values($data);
		$bar->colour = '#D54C78';

		$y_axis = new OFC_Elements_Axis_Y();
		$y_axis->set_range(0, $y_axis_maxV, $y_axis_stepV);
		$y_axis->set_colours('#000000', '#CDCDCD');
		//$y_axis->labels = ( array(1,2,3,4,5,6,7,8,10) );
		$y_axis->set_offset(false);

		$x_axis = new OFC_Elements_Axis_X();
		$x_axis->set_3d(5);
		$x_axis->set_colours('#909090', '#FAFAFA');
		$x_axis->set_labels_from_array($labels);
		$x_axis->set_range($minx, $maxx, 1);

		$chart = new OFC_Chart();
		$chart->set_title($title);
		$chart->set_bg_colour('#FAFAFA');
		$chart->add_element($bar);
		$chart->add_y_axis($y_axis);
		$chart->x_axis = $x_axis;

		//get json data
		$fstream = $chart->toString();

		return $fstream;
	}
	
}