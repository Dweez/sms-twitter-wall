<?php
header("refresh:10;url=register.php");

require_once('../smswall.inc.php');
include('init_db.php');
include('../twitdate.php');

// Include des libs SimplePie pour le parsing des flux
// et une autre pour l'encodage/decodage des noms de domaines internationaux (idn)
require_once('../simplepie/simplepie.inc');
require_once('../simplepie/idn/idna_convert.class.php');

/**
 * Captation des tweets
 */

// Dernier twit enregistré. Sert de référent
$lastItem = $db->query("SELECT * FROM items ORDER BY timestamp DESC LIMIT 0, 1");
$lastItemRow = $lastItem->fetch(PDO::FETCH_ASSOC);
$oldTimestamp = (empty($lastItemRow['timestamp'])) ? $config['ctime'] : $lastItemRow['timestamp'];

// Initialisation du feed unique. Agrégat des différents flux RSS à parser
$feed = new SimplePie();
	// Pas de cache
	$feed->enable_cache(false);

	/**
	 * Pb Twitter : seulement 15 réponses dans le feed RSS :
	 * 		Si flot important sur un hashtage bavard, certains tweets peuvent passer à la trappe ... 
	 * 		ie : plus de 15 tweets durant l'intervale de rafraichissement
	 */ 
	
	// Récupération du hashtag en cours dans la config
	$hashtag = $config['hashtag'];
	$hashTagHash = explode(",", $hashtag);
	// Array des flux RSS à parser
	$feedAry = array();
	foreach($hashTagHash as $hash){
		//$feedAry[] = 'http://search.twitter.com/search.rss?q=%23'.trim($hash);
		$feedAry[] = 'http://search.twitter.com/search.rss?q=%23'.trim($hash).'&rpp=30';
	}
	$feed->set_feed_url($feedAry);

	// Init du feed unique
	$feed->init();
	
	// SimplePie : Make sure the page is being served with the UTF-8 headers.
	$feed->handle_content_type();

// Enregistrement des derniers items du feed unique
$aryNew = array();
foreach($feed->get_items() as $item){
	$feed = $item->get_feed();
    $feedlink = $feed->get_link();
    
    // A retester avec un feed Blogspot
    $isBlogpost = preg_match("/blogspot\.com/",$feedlink);
    $isTwitter = preg_match("/twitter\.com/",$feedlink);
    $title = $isBlogpost ? utf8_decode($item->get_title()) : $item->get_title();
    $content = $isBlogpost ? utf8_decode($item->get_content()) : $item->get_content();
    
	$enclosure = $item->get_enclosure();
	$avatarUrl = $enclosure->get_link();
    //$avatar = $isTwitter ? : '';
    
    
    $timestamp = $item->get_date('U');
    $link = $item->get_permalink();
    
    $modo_type = $config['modo_type'];
    $etat_bulle = $config['bulle'];
    
    // Insert si le timestamp de l'item est plus récent que $lastItemRow['timestamp']
    if($timestamp > $oldTimestamp){
    	$db->exec('INSERT INTO "items" VALUES(NULL,'.$db->quote($title).','.$db->quote($link).','.$db->quote($content).','.$timestamp.','.$modo_type.','.$etat_bulle.','.$db->quote($avatarUrl).');');
    	$ary_url = explode("/", $link);
    	$pseudo = $ary_url[3];
    	$aryNew[] = array($pseudo,$title);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="generator" content="Bug" />
<meta name="viewport" content="initial-scale=1,user-scalable=yes" />
<title><?php echo "New : " . count($aryNew); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="main.css" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="../js/json2.min.js"></script>
</head>
<body>
<div id="menu">
	<div id="hashtag">
		Nouveaux Tweets pour : <?php echo $hashtag; ?>
	</div>
</div>
<div id="subMenu">
	<?php 
	echo "Nouveaux Tweets : " . count($aryNew);
	?>
</div>

<ul id="containerMsg" style="top: 0;">
	<?php 
	if(!empty($aryNew)){
		foreach($aryNew as $twit){
			?>
		    <li class="msgOK">
		    	<strong><?php echo $twit[0]; ?> : </strong><?php echo $twit[1]; ?>
		    </li>
	    	<?php
		}
	}
	?>
</ul>
</body>
</html>
