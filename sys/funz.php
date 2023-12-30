<?php
function mail_av($to,$msg,$ogg,$from=EMAIL_SERVER,$from_name=TITOLO){//funzione chiamata alla fine
  $headers = "From: $from_name <$from>\r\n";
  $headers .= "Reply-To:$from\r\n";
  $headers .= "MIME-Version:1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
  $message='<!DOCTYPE html>
  <html>
  <head>
  <meta charset=utf-8>
  <title>Email</title>
  </head>
  <body>
  '.$msg;
  $message.='<div style=display:none>';
  return mail($to,$ogg,$message,$headers);
}

function json($url){
       // create curl resource
       $ch = curl_init();
       // set url
       curl_setopt($ch, CURLOPT_URL, $url);
       //return the transfer as a string
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       // $output contains the output string
       $output = curl_exec($ch);
       // close curl resource to free up system resources
       curl_close($ch);
 return json_decode($output,true);
}
function curl($url,$ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36'){

  $options = array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_USERAGENT => $ua
  );
  $curl_handle = curl_init($url);
  curl_setopt_array( $curl_handle, $options );
  $content = curl_exec($curl_handle);
  curl_close($curl_handle);
  //return json_decode($content,true);
  return $content;
}

function ip(){
  return isset($_SERVER['HTTP_CLIENT_IP'])
    ? $_SERVER['HTTP_CLIENT_IP']
    : (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
      ? $_SERVER['HTTP_X_FORWARDED_FOR']
      : $_SERVER['REMOTE_ADDR']);
}

function user_nation(){
  $ip = ip(); // Get the user's IP address
  $json_url = "https://ipinfo.io/$ip/json";   // Send a request to ipinfo.io to get geolocation information
  $response = file_get_contents($json_url);
  $geolocation = json_decode($response, true);
  $nation = isset($geolocation['country']) ? $geolocation['country'] : '';
  $city = isset($geolocation['city']) ? $geolocation['city'] : '';
  $region = isset($geolocation['region']) ? $geolocation['region'] : '';
  if (empty("$nation$city$region")) {
    $response = null;
  }else{
    $response = "$nation, $city, $region";
  }

  return $response;
}

function vdp($data, $label = '', $return = false)
{
	$debug = debug_backtrace();
	$callingFile = $debug[0]['file'];
	$callingFileLine = $debug[0]['line'];
	ob_start();
	var_dump($data);
	$c = ob_get_contents();
	ob_end_clean();
	$c = preg_replace("/\r\n|\r/", "\n", $c);
	$c = str_replace("]=>\n", '] = ', $c);
	$c = preg_replace('/= {2,}/', '= ', $c);
	$c = preg_replace("/\[\"(.*?)\"\] = /i", "[$1] = ", $c);
	$c = preg_replace('/  /', "    ", $c);
	$c = preg_replace("/\"\"(.*?)\"/i", "\"$1\"", $c);
	$c = preg_replace("/(int|float)\(([0-9\.]+)\)/i", "$1() <span class=\"number\">$2</span>", $c);
	$c = preg_replace("/(\[[\w ]+\] = string\([0-9]+\) )\"(.*?)/sim", "$1<span class=\"string\">\"", $c);
	$c = preg_replace("/(\"\n{1,})( {0,}\})/sim", "$1</span>$2", $c);
	$c = preg_replace("/(\"\n{1,})( {0,}\[)/sim", "$1</span>$2", $c);
	$c = preg_replace("/(string\([0-9]+\) )\"(.*?)\"\n/sim", "$1<span class=\"string\">\"$2\"</span>\n", $c);
	$regex = array(
		'numbers' => array(
			'/(^|] = )(array|float|int|string|resource|object\(.*\)|\&amp;object\(.*\))\(([0-9\.]+)\)/i',
			'$1$2(<span class="number">$3</span>)'
		), 'null' => array('/(^|] = )(null)/i', '$1<span class="keyword">$2</span>'), 'bool' => array('/(bool)\((true|false)\)/i', '$1(<span class="keyword">$2</span>)'),
		'types' => array('/(of type )\((.*)\)/i', '$1(<span class="type">$2</span>)'), 'object' => array('/(object|\&amp;object)\(([\w]+)\)/i', '$1(<span class="object">$2</span>)'),
		'function' => array('/(^|] = )(array|string|int|float|bool|resource|object|\&amp;object)\(/i', '$1<span class="function">$2</span>('),
	);
	foreach ($regex as $x) {
		$c = preg_replace($x[0], $x[1], $c);
	}
	$style = '.string::selection{color:#000;background:#fff}.dumpr{border-radius:0.3rem;display:flex;color:#fff;padding:5px 9px;clear:both;background-color:#000;backdrop-filter: blur(1.5px);margin: auto;}.dumpr pre{color:white;font-size:25px;margin:0;padding:5px 9px 7px;white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word;}.dumpr div{background-color:#black;float:left;clear:both}.dumpr span.string{color:#e04d69}.dumpr span.number{color:#grey}.dumpr span.keyword{color:#44a282}.dumpr span.function{color:#bbd}.dumpr span.object{color:#ac00ac}.dumpr span.type{color:#0072c4}
    ';
	$style = preg_replace("/ {2,}/", "", $style);
	$style = preg_replace("/\t|\r\n|\r|\n/", "", $style);
	$style = preg_replace("/\/\*.*?\*\//i", '', $style);
	$style = str_replace('}', '} ', $style);
	$style = str_replace(' {', '{', $style);
	$style = trim($style);
	$c = trim($c);
	$c = preg_replace("/\n<\/span>/", "</span>\n", $c);
	if ($label == '') {
		$line1 = '';
	} else {
		$line1 = "<strong>$label</strong> \n";
	}
	$out = "\n<!-- Dumpr Begin -->\n" . "<style type=\"text/css\">" . $style . "</style>\n" . "<div class=\"dumpr\">
        <div><pre>$line1 $callingFile : $callingFileLine \n$c\n</pre></div></div><!--<div style=\"clear:both;\">&nbsp;</div>-->" . "\n<!-- Dumpr End -->\n";
	if ($return) {
		return $out;
	} else {
		echo $out;
	}
}

function sanitizeFileName($fileName) {
  // Remove any characters that are not letters, numbers, underscores, or hyphens
  $sanitizedFileName = preg_replace("/[^\w\-]/", "", $fileName);
  return strtolower($sanitizedFileName);
}

function cachejson($file_name, $jsonurl, $folder, $debug = false)
{
    //$folder = sanitizeFileName($folder);
    $dir = $folder;

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $cache_json = $dir . sanitizeFileName($file_name) . ".json";

    if (file_exists($cache_json)) {
        if ($debug || filesize($cache_json) <= 170) {
            unlink($cache_json);
        }
    }

    if (!is_file($cache_json)) {
        $json = curl($jsonurl);
        if (!file_exists($cache_json) || filesize($cache_json) > 170) {
            file_put_contents($cache_json, $json);
        }
    } else {
        $json = file_get_contents($cache_json);
    }

    return json_decode($json, true);
}


function makeFileSafe($inputString) {
  $fileSafeString = preg_replace("/[^a-zA-Z0-9_-]+/", "_", $inputString);

  $fileSafeString = trim($fileSafeString, '_-');

  $fileSafeString = preg_replace("/[_-]+/", "_", $fileSafeString);

  $fileSafeString = strtolower($fileSafeString);

  if (empty($fileSafeString)) {
      $fileSafeString = "default_filename";
  }

  $fileSafeString = substr($fileSafeString, 0, 255);

  return $fileSafeString;
}
function minify_html($html){
   $search = array(
    '/(\n|^)(\x20+|\t)/',
    '/(\n|^)\/\/(.*?)(\n|$)/',
    '/\n/',
    '/\<\!--.*?-->/',
    '/(\x20+|\t)/', # Delete multispace (Without \n)
    '/\>\s+\</', # strip whitespaces between tags
    '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
    '/=\s+(\"|\')/'); # strip whitespaces between = "'

   $replace = array(
    "\n",
    "\n",
    " ",
    "",
    " ",
    "><",
    "$1>",
    "=$1");

    $html = preg_replace($search,$replace,$html);
    return $html;
}