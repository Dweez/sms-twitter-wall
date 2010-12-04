<?php

require_once('../smswall.inc.php');

// Modification des paramètres de config
$id = $_POST['id'];
$visible = $_POST['visible'];

// @todo control
$visible = ($visible == '1') ? '0' : '1';
$sql = "UPDATE items SET visible = ? WHERE id = ?;";
$q = $db->prepare($sql);
$q->execute(array($visible,$id));

// Refresh vers l'index
header( 'refresh: 0; url=index.php' );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="generator" content="Bug" />
<meta name="viewport" content="initial-scale=1,user-scalable=yes" />
<title>TwitWall Factory</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="index.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
</body>
</html>

