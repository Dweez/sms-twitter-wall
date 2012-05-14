<?php
header( "Content-type: application/json; charset=utf-8" );
require_once('smswall.inc.php');
include('twitdate.php');

$qconfig = $db->query("SELECT * FROM config_wall");
$config = $qconfig->fetch(PDO::FETCH_ASSOC);
$ary_config = array('avatar'=>$config['avatar']);
$ary_old = (array) json_decode(stripslashes($_POST['aryOld']));
$spe_old = array();
foreach($ary_old as $old){
	$spe_old[$old->id] = $old->etat;
}

//if(!empty($ary_old)){
	
	$result = $db->query("SELECT * FROM items ORDER BY timestamp DESC LIMIT 0,30");
	$rowarray = $result->fetchall(PDO::FETCH_ASSOC);
	
	$ary_twit = array();

	foreach($rowarray as $item){
		
		// reformattage à revoir
		
		$isUrl = filter_var($item['link'], FILTER_VALIDATE_URL);
		if($isUrl){
			$ary_url = explode("/", $isUrl);
			if($ary_url[2]=="twitter.com"){
				$item['pseudo'] = $ary_url[3];
				//$item['twitdate'] = Timesince($item['timestamp'],'');
				$item['twitdate'] = $item['timestamp'];
			}else{
				$item['pseudo'] = "RSS";
				$item['title'] .= " - " . strip_tags(substr($item['description'],0,250));
			}
		}else{
			if($item['link'] == "SMS"){
				$item['pseudo'] = "SMS";
				$item['title'] = strip_tags($item['description']);
				$item['avatarbig'] = "";
			}else if($item['link'] == "WEB"){
				$item['pseudo'] = $item['title'];
				$item['title'] = strip_tags($item['description']);
				$item['avatarbig'] = "";
			}
		}
		
		$item['twitdate'] = Timesince($item['timestamp'],'');
		//$item['twitdate'] = $item['timestamp'];
		
		// Type d'affichage des messages 
		
		if(array_key_exists($item['id'], $spe_old)){
			// Ancien : message déjà présent sur le mur
			if($item['visible'] == 0 && $spe_old[$item['id']] == 0){
				$item['etat'] = "staticClose";
				// Efface le contenu du message avant de fermer le message
				//$item['title'] = "";
			}elseif($item['visible'] == 0 && $spe_old[$item['id']] == 1){
				$item['etat'] = "animClose";
				// Efface le contenu du message avant de fermer le message
				//$item['title'] = "";
			}elseif($item['visible'] == 1 && $spe_old[$item['id']] == 0){
				$item['etat'] = "animOpen";
			}elseif($item['visible'] == 1 && $spe_old[$item['id']] == 1){
				$item['etat'] = "staticOpen";
			}
		}else{
			// Nouveau : message non présent sur le mur précédement
			if($item['visible'] == 0){
				$item['etat'] = "staticClose";
				//$item->title = "";
			}elseif($item['visible'] == 1){
				$item['etat'] = "animOpen";
			}
		}
		$ary_twit[] = $item;
	}
	echo json_encode(array('twits'=>$ary_twit,'config'=>$ary_config));
//}
