<?php

require_once('../smswall.inc.php');

$switchBul = $_POST['switchBul'];
$id = $_POST['id'];

$etatBul = ($switchBul == '1') ? '1' : '0';
//$sql = "UPDATE items SET bulle = 0 WHERE bulle = 1;";
$sql = "UPDATE items SET bulle = ? WHERE id = ?;";
$q = $db->prepare($sql);
$q->execute(array($etatBul,$id));

//if($query){
	$response = array();
	$response[] = $etatBul;
	
	echo json_encode($response);
//}