<?php

require_once('../smswall.inc.php');

header('refresh: 15; url=index.php');

include('init_db.php');
include('../twitdate.php');

// Include des libs SimplePie pour le parsing des flux
// et une autre pour l'encodage/decodage des noms de domaines internationaux (idn)
require_once('../simplepie/simplepie.inc');
require_once('../simplepie/idn/idna_convert.class.php');

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
		$feedAry[] = 'http://search.twitter.com/search.rss?q=%23'.trim($hash);
	}
	$feed->set_feed_url($feedAry);

	// Init du feed unique
	$feed->init();
	
	// SimplePie : Make sure the page is being served with the UTF-8 headers.
	$feed->handle_content_type();

// Enregistrement des derniers items du feed unique
foreach($feed->get_items() as $item){
	$feed = $item->get_feed();
    $feedlink = $feed->get_link();
    
    // A retester avec un feed Blogspot
    $isBlogpost = preg_match("/blogspot\.com/",$feedlink);
    $isTwitter = preg_match("/twitter\.com/",$feedlink);
    $title = $isBlogpost ? utf8_decode($item->get_title()) : $item->get_title();
    $content = $isBlogpost ? utf8_decode($item->get_content()) : $item->get_content();
    
    $timestamp = $item->get_date('U');
    $link = $item->get_permalink();
    
    $modo_type = $config['modo_type'];
    
    // Insert si le timestamp de l'item est plus récent que $lastItemRow['timestamp']
    if($timestamp > $oldTimestamp){
    	$db->exec('INSERT INTO "items" VALUES(NULL,'.$db->quote($title).','.$db->quote($link).','.$db->quote($content).','.$timestamp.','.$modo_type.');');
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="generator" content="Bug" />
<meta name="viewport" content="initial-scale=1,user-scalable=yes" />
<title>TwitWall Factory</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="main.css" media="screen" rel="stylesheet" type="text/css" />
<!-- <script type="text/javascript" src="js/jquery-1.4.3-min.js"></script> -->
<!-- <script type="text/javascript" src="js/json2.min.js"></script> -->
</head>
<body>
<div id="menu">
	<form style="float: right; margin-right: 25px;">
		<input type="button" value="Refresh" onclick="window.location.href='index.php'" />
	</form>
	<form action="purge_items.php" method="post" style="float: right;  margin-right: 20px;">
		<input type="checkbox" name="cbox" value="1" /> 
		<input type="submit" value="Purger" />
	</form>
	
	<form action="update_config.php" method="post" style="float: left; margin-right: 20px;">
		<input type="text" name="hashtag" value="<?php echo $hashtag; ?>" style="width: 100px;"/>
		<input type="submit" value="Filtrer" />
	</form> 
	<form action="update_config.php" method="post" style="display: block;">
		<?php 
		if($modo_type == 1){
			$aprioriState = '';
			$aposterioriState = 'selected="selected" ';
		}else{
			$aprioriState = 'selected="selected" ';
			$aposterioriState = '';
		}
		?>
		<select name="modo_type">
			<option <?php echo $aprioriState; ?>>A priori</option>
			<option <?php echo $aposterioriState; ?>>A posteriori</option>
		</select>
		<input type="submit" value="Ok" />
	</form> 
	
</div>
<?php 
/**
 * Construction du wall
 * Lecture des derniers tweets en base
 */ 
$result = $db->query("SELECT * FROM items ORDER BY timestamp DESC LIMIT 0,100");
$rowarray = $result->fetchall(PDO::FETCH_ASSOC);
?>
<ul id="containerMsg">
<?php 
foreach($rowarray as $row){
	
	switch($row['visible']){
		case '0':
			$classcss = 'msgNO';
			break;
		#case "staticClose":
		#	$classcss = 'msgNO';
		#	break;
		#case "animClose":
		#	//classcss = 'msg';
		#	$classcss = 'msgNO';
		#	break;
		#case "animOpen":
		#	$classcss = 'msgNew';
		#	break;
		case "1":
			$classcss = 'msgOK';
			break;
		#case "staticOpen":
		#	$classcss = 'msgOK';
		#	break;
	}
	
	$ary_link = explode("/", $row['link']);
	if($ary_link[2]=="twitter.com"){
		$pseudo = $ary_link[3];
	}else{
		$pseudo = "SMS";
	}
	?>
	
	<li class="<?php echo $classcss; ?>">
		<form action="update_vis_static.php" method="post" style="float: right;">
			<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
			<input type="hidden" name="visible" value="<?php echo $row['visible']; ?>"/>
			<input type="submit" value="ok" style="padding: 0; height: 22px;" />
		</form>
		<span class="author">
			<?php echo $pseudo; ?>
		</span>
		<span class="textMsg">
			<?php echo $row['description']; ?>
		</span>
		<span class="time">
			<?php echo Timesince($row['timestamp'],''); ?>
		</span>
		<div style="clear: both;" ></div>
	</li>
	<?php 
}
?>
</ul>
</body>
</html>

