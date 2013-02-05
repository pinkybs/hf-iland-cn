<?php

define('WORD_WIDTH',10);
define('WORD_HIGHT',10);
define('OFFSET_X',0);
define('OFFSET_Y',0);
define('WORD_SPACING',4);

include_once 'Hapyfish2/Bms/Bll/bmp.php';
 
class Hapyfish2_Bms_Bll_ValidateCode
{
	  public function setImage($Image)
	  {
	    $this->ImagePath = $Image;
	  }
	  
	  public function getData()
	  {
	    return $data;
	  }
	  
	  public function getResult()
	  {
	    return $DataArray;
	  }
	  
	  public function getHec($imagedata = null)
	  {
	    //$res = imagecreatefromjpeg($this->ImagePath);
	    if (!$imagedata) {
	    	$res = imagecreatefrombmp($this->ImagePath);
	    } else {
	    	$res = imagecreatefrombmpstring($imagedata);
	    }
	    $size = getimagesize($this->ImagePath);
	    $data = array();
	    for($i=0; $i < $size[1]; ++$i)
	    {
	      for($j=0; $j < $size[0]; ++$j)
	      {
	        $rgb = imagecolorat($res,$j,$i);
	        $rgbarray = imagecolorsforindex($res, $rgb);
	        if($rgbarray['red'] < 125 || $rgbarray['green']<125
	        || $rgbarray['blue'] < 125)
	        {
	          $data[$i][$j]=1;
	        }else{
	          $data[$i][$j]=0;
	        }
	      }
	    }
	    $this->DataArray = $data;
	    $this->ImageSize = $size;
	  }
	  
	  public function run()
	  {
	    $result="";
	    // 查找4个数字
	    $data = array("","","","");
	    for($i=0;$i<4;++$i)
	    {
	      /* 
	      $x = ($i*(WORD_WIDTH+WORD_SPACING))+OFFSET_X;
	      $y = OFFSET_Y;
	      for($h = $y; $h < (OFFSET_Y+WORD_HIGHT); ++ $h)
	      {
	        for($w = $x; $w < ($x+WORD_WIDTH); ++$w)
	        {
	          $data[$i].=$this->DataArray[$h][$w];
	        }
	      }
	      */
	    	$x = $i*10;
	    	for($j=0; $j < 10; $j++) {
	    		for($k=$x; $k < $x + 10; $k++) {
	    			$data[$i].=$this->DataArray[$j][$k];
	    		}
	    	}
	    }
	 
	    /*
	    // 进行关键字匹配
	    foreach($data as $numKey => $numString)
	    {
	      $max=0.0;
	      $num = 0;
	      foreach($this->Keys as $key => $value)
	      {
	        $percent=0.0;
	        similar_text($value, $numString,$percent);
	        if(intval($percent) > $max)
	        {
	          $max = $percent;
	          $num = $key;
	          if(intval($percent) > 95)
	            break;
	        }
	      }
	      $result.=$num;
	    }*/
	    foreach($data as $numKey => $numString) {
	    	foreach($this->Keys as $key => $value) {
	    		if ($numString === $value) {
	    			//echo $numString . '<br/>' . $value . '<br/><br/>';
	    			$result .= $key;
	    			break;
	    		}
	    	}
	    }
	    
	    $this->data = $result;
	    // 查找最佳匹配数字
	    return $result;
	  }
	 
	  public function Draw()
	  {
	    for($i=0; $i<$this->ImageSize[1]; ++$i)
	    {
	        for($j=0; $j<$this->ImageSize[0]; ++$j)
	        {
	            if ($j%10 == 0) {
	            	echo '&nbsp;&nbsp;';
	            }
	            if ($this->DataArray[$i][$j] == '1') {
	            	echo '<font style="color:red">' . $this->DataArray[$i][$j] . '</font>';
	            } else {
	        		echo $this->DataArray[$i][$j];
	            }
	        }
	        echo "<br/>";
	    }
	  }
	  
	  public function __construct()
	  {
	  	$this->Keys = array(
		    '0'=>'0111100000100001000010000100001000010000100001000010000100001000010000100001000010000100000111100000',
		    '1'=>'0010000000111000000000100000000010000000001000000000100000000010000000001000000000100000001111100000',
		    '2'=>'0111100000100001000000000100000000010000000010000000010000000010000000010000000010000000001111110000',
		    '3'=>'0111100000100001000000000100000000010000001110000000000100000000010000000001000010000100000111100000',
		    '4'=>'0000100000000110000000101000000010100000010010000010001000001111110000000010000000001000000001110000',
		    '5'=>'1111110000100000000010000000001000000000111110000000000100000000010000000001000010000100000111100000',
		    '6'=>'0011100000010000000010000000001000000000101110000011000100001000010000100001000010000100000111100000',
		    '7'=>'1111110000100001000000001000000000100000000100000000010000000010000000001000000001000000000100000000',
		    '8'=>'0111100000100001000010000100001000010000011110000010000100001000010000100001000010000100000111100000',
		    '9'=>'0111100000100001000010000100001000010000100011000001110100000000010000000001000000001000000111000000'
	  	);
	  	
	  }
	  
	  protected $ImagePath;
	  protected $DataArray;
	  protected $ImageSize;
	  protected $data;
	  protected $Keys;
	  protected $NumStringArray;
 
}