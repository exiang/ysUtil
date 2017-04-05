<?php namespace Exiang\YsUtil;
 
class YsUtil 
{
    public function hasCheese($bool=true)
    {
        return $bool;
    }

    public function html2text($html)
	{
		$h2t = new \Html2Text\Html2Text($html);
		$buffer = $h2t->get_text();
		
		return $buffer;
	}

    public function splitFullName2FirstLast($name)
	{
		$parts = explode(" ", $name);
		$lastName = array_pop($parts);
		$firstName = implode(" ", $parts);

		return array('firstName'=>$firstName, 'lastName'=>$lastName);
	}

	public function ipInRange($ip, $range) 
	{
		if (strpos($range, '/') == false)
			$range .= '/32';

		// $range is in IP/CIDR format eg 127.0.0.1/24
		list($range, $netmask) = explode('/', $range, 2);
		$range_decimal = ip2long($range);
		$ip_decimal = ip2long($ip);
		$wildcard_decimal = pow(2, (32 - $netmask)) - 1;
		$netmask_decimal = ~ $wildcard_decimal;
		return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
	}

	public function isCloudflare() 
	{
		$ipCheck        = self::cloudflareCheckIP($_SERVER['REMOTE_ADDR']);
		$requestCheck   = self::cloudflareRequestsCheck();
		return ($ipCheck && $requestCheck);
	}

	// Use when handling ip's
	public function getRequestIP() 
	{
		$check = self::isCloudflare();

		if($check) {
			return $_SERVER['HTTP_CF_CONNECTING_IP'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	// convert base unit integer back to decimal
	public function bui2Decimal($bui)
	{
		return sprintf('%.2f', substr_replace($bui, '.', -2, 0));
	}
	
	public function decimal2Bui($decimal)
	{
		return str_replace('.', '', $decimal);
	}

	// meter to kilometer
	public function m2km($meter, $showUnit=false)
	{
		$buffer = sprintf('%.2f', $meter/1000);
		if($showUnit) $buffer .= ' KM';
		return $buffer;
	}
	
	public function timezone2offset($name)
	{
		$dateTime = new \DateTime(); 
		$dateTime->setTimeZone(new DateTimeZone($name)); 
		return $dateTime->format('P'); 
	}
	
	// take a timestamp from timezone A (preset to GMT) and convert to timezone B
	// not sure is this useful or not	
	public function convertTimezone($timestamp, $toTimezone, $fromTimezone='GMT')
	{
		// convert timestamp fromTimezone to gmt first
		$date = new \DateTime(null, new \DateTimeZone($fromTimezone));
		$date->setTimestamp($timestamp);
		
		// convert gmt timestamp to toTimezone
		$date->setTimezone(new \DateTimeZone($toTimezone));
		return strtotime($date->format('Y-M-d H:i:s'));
	}
	
	public function slugify($text)
	{ 
		// replace non letter or digits by -
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);

		// trim
		$text = trim($text, '-');

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// lowercase
		$text = strtolower($text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		if (empty($text))
		{
			return '';
		}

		return $text;
	}
	
	public function string2Array($str, $separator=',')
	{
		return array_map('trim', explode($separator, $str));
	}

	public function yskrsort($array) 
	{
		krsort($array);
		return $array;
	}
	
	public function truncate($string='', $limit=100, $pad='...')
	{
		// return with no change if string is shorter than $limit
		if(mb_strlen($string) <= $limit) return $string;

		$string = mb_substr($string, 0, $limit);

		return $string . $pad;
    }
	
	
	public function formatByte($bytes, $unit = "MB", $decimals = 2) 
	{
		$units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 
		'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);

		$value = 0;
		if ($bytes > 0) 
		{
			// Generate automatic prefix by bytes 
			// If wrong prefix given
			if (!array_key_exists($unit, $units)) 
			{
				$pow = floor(log($bytes)/log(1024));
				$unit = array_search($pow, $units);
			}

			// Calculate byte value by prefix
			$value = ($bytes/pow(1024,floor($units[$unit])));
		}

		// If decimals is not numeric or decimals is less than 0 
		// then set default value
		if (!is_numeric($decimals) || $decimals < 0) 
		{
			$decimals = 2;
		}

		// Format output
		return sprintf('%.' . $decimals . 'f '.$unit, $value);
	}
	
	
	public function generateRandomPassword($max='8', $min='8', $lowerCase=false)
	{
		$limit = rand($min, $max);
		$buffer = '';
		for($i=0; $i<$limit; $i++)
		{
			$switch = rand(1, 4);
			if($switch%2 == 0)
			{
				$buffer .= chr(rand(97, 122));
			}
			else
			{
				$buffer .= chr(rand(48,57));
			}
		}

		if($lowerCase) $buffer = strtolower($buffer);
		
		return $buffer;
	}
	
	// check is a string is md5
	public function isMd5($string)
	{
		return preg_match('/^[A-Fa-f0-9]{32}$/',$string);
	}

	// check is a string is sha1
	public function isSha1($string)
	{
		return preg_match('/^[A-Fa-f0-9]{40}$/',$string);
	}
	
	public function isAlphaNumeric($input)
	{
		return (preg_match("/^[A-Z,a-z,0-9]+$/i", $input));
	}
	
	public function isEmailAddress($input)
	{
		if (preg_match("/[[:alnum:]]+@[[:alnum:]]+\.[[:alnum:]]+/i", $input))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function getMimeType($filename)
	{
		$mimetype = false;
		if(function_exists('finfo_fopen')) 
		{
			// open with FileInfo
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
		}
        elseif (function_exists('exif_imagetype'))
        {
            $mimetype = image_type_to_mime_type(exif_imagetype($filename));
        }
		elseif(function_exists('mime_content_type'))
		{
		   $mimetype = mime_content_type($filename);
		}
		return $mimetype;
	}
	
	public function listDir($dir, $recur = true)
	{
		$file_list = '';

		if($recur)
		{
			$stack[] = $dir;
			while($stack)
			{
				$current_dir = array_pop($stack);
				if($dh = @opendir($current_dir))
				{
					while(($file = readdir($dh)) !== false)
					{
						// exclude any filename begin with ".", could be a up one directory, a .svn, a .htaccess
						if($file{0} !== '.' && $file != 'Thumbs.db')
						{
							$current_file = sprintf("{$current_dir}%s{$file}", DIRECTORY_SEPARATOR);
							if(is_file($current_file))
							{
								$file_list[] = sprintf("{$current_dir}%s{$file}", DIRECTORY_SEPARATOR);
							}
							elseif(is_dir($current_file))
							{
								$stack[] = $current_file;
							}
						}
					}
				}
			}
		}
		else
		{
			$handle = opendir($dir);
			while(false !== ($file = readdir($handle)))
			{
				if( $file{0} !== '.' && $file != 'Thumbs.db')
				{
					$file_list[] = $file;
				}
			}

			if(is_array($file_list))
			{
				sort($file_list);
			}
		}

		return $file_list;
	}
	
	public function fileGetBinary($file)
	{
		$filesize = filesize($file);
		if($filesize == 0)
		{
			$filesize = 1024;
		}

		$handle = fopen($file, "rb");
		$content = fread($handle, $filesize);
		fclose($handle);
		return $content;
	}
	
	
	public function getFileName($filename)
	{
		if($filename)
		{
			return basename($filename);
		}
		return '';
	}
	
	public function getFileExtension($filename)
	{
		if($filename)
		{
			return substr($filename, strrpos($filename, ".")+1);
		}
		return '';
	}
	
	// spreadsheet usage
	public function ssNum2Alpha($n)
	{
		for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
			$r = chr($n%26 + 0x41) . $r;
		return $r;
	}
	
	// time elapsed to human readable format
	public function timeElapsed($timestamp, $precision=5)
	{
		$timestamp = time()-$timestamp;
		$bit = array(
			' year'        => $timestamp / 31556926 % 12,
			' week'        => $timestamp / 604800 % 52,
			' day'        => $timestamp / 86400 % 7,
			' hour'        => $timestamp / 3600 % 24,
			' min'    => $timestamp / 60 % 60,
			' sec'    => $timestamp % 60
		);
			
		foreach($bit as $k => $v)
		{
			if($v > 1)$result[] = $v . $k . 's';
			if($v == 1)$result[] = $v . $k;
		}
		
		$result = array_slice($result, 0, $precision);
		//array_splice($result, count($result)-1, 0, '&amp;');
		array_splice($result, count($result)-1, 0, '');
		$result[] = 'ago.';
		
		
		$tmp = join(' ', $result);
		return $tmp;
	}
	
	public function toChinaseNum($num)
	 {
		$char = array("零","一","二","三","四","五","六","七","八","九");
		$dw = array("","十","百","千","万","亿","兆");
		$retval = "";
		$proZero = false;
		for($i = 0;$i < strlen($num);$i++)
		{
			if($i > 0)    $temp = (int)(($num % pow (10,$i+1)) / pow (10,$i));
			else $temp = (int)($num % pow (10,1));
			
			if($proZero == true && $temp == 0) continue;
			
			if($temp == 0) $proZero = true;
			else $proZero = false;
			
			if($proZero)
			{
				if($retval == "") continue;
				$retval = $char[$temp].$retval;
			}
			else $retval = $char[$temp].$dw[$i].$retval;
		}
		if($retval == "一十") $retval = "十";
		return $retval;
	 }
	
	public function widerKeyword($keyword)
	{
		$buffer = '';
		$length = mb_strlen($keyword);
		for($i=0; $i<$length; $i++)
		{
			$buffer .= mb_substr($keyword, $i, 1)."%";
		}
		return mb_substr($buffer, 0, -1);
	}
	
	// pass in date in yyyy-mm-dd format
	public function getWeekInMonth($timestamp, $rollover='sunday')
    {
		$date = date('Y-m-d', $timestamp);
        
		$cut = substr($date, 0, 8);
        $daylen = 86400;
        //$timestamp = strtotime($date);
		
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $i = 1;
        $weeks = 1;

        for($i; $i<=$elapsed; $i++)
        {
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if($day == strtolower($rollover))  $weeks ++;
        }

        return $weeks;
    }
	
	public function dateDiff($interval, $datefrom, $dateto, $using_timestamps = false) 
	{
		/*
		$interval can be:
		yyyy - Number of full years
		q - Number of full quarters
		m - Number of full months
		y - Difference between day numbers
			(eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
		d - Number of full days
		w - Number of full weekdays
		ww - Number of full weeks
		h - Number of full hours
		n - Number of full minutes
		s - Number of full seconds (default)
		*/
		
		if (!$using_timestamps) {
			$datefrom = strtotime($datefrom, 0);
			$dateto = strtotime($dateto, 0);
		}
		$difference = $dateto - $datefrom; // Difference in seconds
		 
		switch($interval) {
		 
		case 'yyyy': // Number of full years
			$years_difference = floor($difference / 31536000);
			if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
				$years_difference--;
			}
			if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
				$years_difference++;
			}
			$datediff = $years_difference;
			break;
		case "q": // Number of full quarters
			$quarters_difference = floor($difference / 8035200);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$quarters_difference--;
			$datediff = $quarters_difference;
			break;
		case "m": // Number of full months
			$months_difference = floor($difference / 2678400);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$months_difference--;
			$datediff = $months_difference;
			break;
		case 'y': // Difference between day numbers
			$datediff = date("z", $dateto) - date("z", $datefrom);
			break;
		case "d": // Number of full days
			$datediff = floor($difference / 86400);
			break;
		case "w": // Number of full weekdays
			$days_difference = floor($difference / 86400);
			$weeks_difference = floor($days_difference / 7); // Complete weeks
			$first_day = date("w", $datefrom);
			$days_remainder = floor($days_difference % 7);
			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
			if ($odd_days > 7) { // Sunday
				$days_remainder--;
			}
			if ($odd_days > 6) { // Saturday
				$days_remainder--;
			}
			$datediff = ($weeks_difference * 5) + $days_remainder;
			break;
		case "ww": // Number of full weeks
			$datediff = floor($difference / 604800);
			break;
		case "h": // Number of full hours
			$datediff = floor($difference / 3600);
			break;
		case "n": // Number of full minutes
			$datediff = floor($difference / 60);
			break;
		default: // Number of full seconds (default)
			$datediff = $difference;
			break;
		}    
		return $datediff;
	}

	public function nl2space($string)
	{
		return trim(preg_replace('/\s+/', ' ', $string));
	}
	
	public function calculateAge($timestamp = 0, $now = 0) 
	{
		# default to current time when $now not given
		if ($now == 0)
			$now = time();
	 
		# calculate differences between timestamp and current Y/m/d
		$yearDiff   = date("Y", $now) - date("Y", $timestamp);
		$monthDiff  = date("m", $now) - date("m", $timestamp);
		$dayDiff    = date("d", $now) - date("d", $timestamp);
	 
		# check if we already had our birthday
		if ($monthDiff < 0)
			$yearDiff--;
		elseif (($monthDiff == 0) && ($dayDiff < 0))
			$yearDiff--;
	 
		# set the result: age in years
		$result = intval($yearDiff);
	 
		# deliver the result
		return $result;
	}
	
	public function null2empty($string)
	{
		if($string == null) return '';
		return $string;
	}
	
	public function mbWordWrap($string, $width=75, $break="\n", $cut=false) 
	{
		$words = explode("\n", trim($string));
		foreach($words as &$word) {
		  if (!$cut) {
			  $regexp = '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){'.$width.',}\b#U';
		  } else {
			  $regexp = '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){'.$width.'}#';
		  }
		  $string_length = mb_strlen($word,'UTF-8');
		  $cut_length = ceil($string_length / $width);
		  $i = 1;
		  $new_word = '';
		  while ($i < $cut_length) {
			  preg_match($regexp, $word,$matches);
			  $new_string = $matches[0];
			  $new_word .= $new_string.$break;
			  $word = substr($word, strlen($new_string));
			  $i++;
		  }
		  $word = $new_word.$word;
		}
		return join(' ',$words);
	}

	public function generateArrayRange($start, $stop, $mode='1d')
	{
		$result = null;
		// desc
		if($start > $stop)
		{
			for($i=$start; $i>=$stop; $i--)
			{
				$result[] = $i;
			}
		}
		// asc
		else
		{
			for($i=$start; $i<=$stop; $i++)
			{
				$result[] = $i;
			}
		}
		
		if($mode == '2d')
		{
			$result = self::convert1dTo2dArray($result);
		}
		return $result;
	}

	public function convert1dTo2dArray($array)
	{
		$result = null;
		foreach($array as $k=>$v)
		{
			$result[$v] = $v;
		}
		return $result;
	}
	
	// number start from 1 to 12
	public function monthNumber2Name($number, $format='F')
	{
		$dateObj = \DateTime::createFromFormat('!m', $number);
		return $dateObj->format($format);
	}
	
	public function convertToKeyValueArray($array, $code='key', $title='title')
	{
		$result = '';
		foreach($array as $a)
		{
			$result[$a[$code]] = $a[$title];
		}
		return $result;
	}
	
	public function isJson($string) 
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	private function cloudflareCheckIP($ip) 
	{
		$cf_ips = array(
			'199.27.128.0/21',
			'173.245.48.0/20',
			'103.21.244.0/22',
			'103.22.200.0/22',
			'103.31.4.0/22',
			'141.101.64.0/18',
			'108.162.192.0/18',
			'190.93.240.0/20',
			'188.114.96.0/20',
			'197.234.240.0/22',
			'198.41.128.0/17',
			'162.158.0.0/15',
			'104.16.0.0/12',
		);
		$is_cf_ip = false;
		foreach ($cf_ips as $cf_ip) {
			if (self::ipInRange($ip, $cf_ip)) {
				$is_cf_ip = true;
				break;
			}
		} return $is_cf_ip;
	}

	private function cloudflareRequestsCheck() 
	{
		$flag = true;

		if(!isset($_SERVER['HTTP_CF_CONNECTING_IP']))   $flag = false;
		if(!isset($_SERVER['HTTP_CF_IPCOUNTRY']))       $flag = false;
		if(!isset($_SERVER['HTTP_CF_RAY']))             $flag = false;
		if(!isset($_SERVER['HTTP_CF_VISITOR']))         $flag = false;
		return $flag;
	}
 
}