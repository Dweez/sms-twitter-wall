<?php

require_once('../smswall.inc.php');

// Modification des paramètres de config
$up_hashtag = $_POST['hashtag'];
$up_modo_type = $_POST['modo_type'];
$up_purge = $_POST['cbox'];
$up_avatar = $_POST['avatar'];
$up_theme = $_POST['theme'];

if(!empty($up_hashtag) || !empty($up_modo_type) || !empty($up_purge) || !empty($up_avatar) || !empty($up_theme)){
	// @todo Il y a y a moyen de factoriser toutes ces mises à jour
	// Update de la chaine hashtag
	if(!empty($up_hashtag)){
		$sql = "UPDATE config_wall SET hashtag = ? WHERE id = 1;";
		$q = $db->prepare($sql);
		$q->execute(array($up_hashtag));
	}
	// Changement de modération
	if(!empty($up_modo_type)){
		$toggleModo = ($up_modo_type == 'A posteriori') ? '1' : '0';
		$sql = "UPDATE config_wall SET modo_type = ? WHERE id = 1;";
		$q = $db->prepare($sql);
		$q->execute(array($toggleModo));
	}
	// Affichage des avatars
	if(!empty($up_avatar)){
		$toggleAvatar = ($up_avatar == 'Afficher les avatars') ? '1' : '0';
		$sql = "UPDATE config_wall SET avatar = ? WHERE id = 1;";
		$q = $db->prepare($sql);
		$q->execute(array($toggleAvatar));
	}
	// Purge des tweets (pas des sms pour l'instant)
	if($_POST['cbox'] == "OK"){
		// Suppression de tous les tweets :
		$sql = "DELETE FROM items";
		//$nbrItems = $db->exec($sql);
		$q = $db->prepare($sql);
		$q->execute();
		
		// Mise à jour de la config : mtime = timestamp serveur
		$sql = "UPDATE config_wall SET mtime = ? WHERE id = 1;";
		$q = $db->prepare($sql);
		$q->execute(array(time()));
	}
	// Changement de thème
	if(!empty($up_theme)){
		$sql = "UPDATE config_wall SET theme = ? WHERE id = 1;";
		$q = $db->prepare($sql);
		$q->execute(array($up_theme));
	}
	// On reload la config toute fraiche. 
	// Ne passe pas en deuxieme requete du update, dommage
	$qconfig = $db->query("SELECT * FROM config_wall");
	$config = $qconfig->fetch(PDO::FETCH_ASSOC);
	//var_dump($_POST['cbox']); exit;
	
	// Refresh vers l'index
	header( 'refresh: 0; url=index.php' );
	echo "Tag : " . $config['hashtag'] . " - Modo : " . $config['modo_type'] . "<br/>";
	echo "<i>refresh ...</i>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="generator" content="Bug" />
<meta name="viewport" content="initial-scale=1,user-scalable=yes" />
<title>TwitWall Factory</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="main.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
</body>
</html>
