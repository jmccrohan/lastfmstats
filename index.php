<?
header("Content-Type: image/png");

$username='LASTFMUSERNAME'; //lastfm username
$joindate =112233445566; //unix timstamp of join date (see profile.xml file)
$cache = 86400; //24 hour cache
$localfile = 'stats_cache.txt';

//if older than 24 hours, download fresh stats
if ((!file_exists($localfile)) || (time()-filemtime($localfile)>$cache)) {

$c = curl_init();
curl_setopt($c, CURLOPT_URL, "http://ws.audioscrobbler.com/1.0/user/$username/profile.xml");
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$src = curl_exec($c);
curl_close($c);
preg_match('/<playcount>(.*)<\/playcount>/', $src, $m);
$contents = htmlentities($m[1]);

$fp = fopen($localfile, "w");
fwrite($fp, $contents);
fclose($fp);
}


$playcount=file($localfile);
$playcount=$playcount[0];

$duration_unix=time()-$joindate;

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

