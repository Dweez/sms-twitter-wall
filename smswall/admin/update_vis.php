<?php

require_once('../smswall.inc.php');

$oldvis = $_POST['oldvis'];
$id = $_POST['id'];

$visible = ($oldvis == '1') ? '0' : '1';
$sql = "UPDATE items SET visible = ? WHERE id = ?;";
$q = $db->prepare($sql);
$q->execute(array($visible,$id));

//if($query){
	$response = array();
	$response[] = $visible;
	
	echo json_encode($response);
//}