<?
header("Content-Type: image/png");

$username='LASTFM_USERNAME'; //enter username
$api_key='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; //generate your own api key here: http://www.last.fm/api/account
$cache = 86400; //24 hour cache
$localfile = 'stats_cache.txt'; //make sure to create this file in local directory and give php write access

//if older than 24 hours, download fresh stats
if ((!file_exists($localfile)) || (time()-filemtime($localfile)>$cache)) {
	$user_getinfo_xml = file_get_contents('http://ws.audioscrobbler.com/2.0/?method=user.getinfo&user=$username&api_key=$api_key');
	$fp = fopen($localfile, "w");
	fwrite($fp, $user_getinfo_xml);
	fclose($fp);
}

$user_getinfo=new SimpleXMLElement(file_get_contents($localfile));
$playcount = $user_getinfo->xpath('user/playcount');
$joindate = $user_getinfo->xpath('user/registered/@unixtime');
$playcount=$playcount[0];
$duration_unix=time()-$joindate[0];

$months = $duration_unix / (60*60*24*30);
$weeks  = $duration_unix / (60*60*24*7);
$days   = $duration_unix / (60*60*24);

$permonth  = floor($playcount / $months)." plays per month";
$perweek   = floor($playcount / $weeks)." plays per week";
$perday    = floor($playcount / $days)." plays per day";

$a=strlen($permonth);
$b=strlen($perweek);
$c=strlen($perday);

//printing image
$font = 3;
$width = ImageFontWidth($font)* $a;
$height = 65;
$im = ImageCreate($width,$height);

$background_colour = imagecolorallocate ($im, 242, 242, 242); //white background
$text_colour = imagecolorallocate ($im, 0, 0,0);//black text
$trans_colour = $background_colour;//transparent colour
imagecolortransparent($im, $trans_colour);
imagestring ($im, $font, 0, 0, $perday, $text_colour);
imagestring ($im, $font, 0, 26, $perweek, $text_colour);
imagestring ($im, $font, 0, 52, $permonth, $text_colour);


imagepng($im);

ImageDestroy($im);

?>