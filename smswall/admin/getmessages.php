<?php

require_once('../smswall.inc.php');
include('init_db.php');
include('../twitdate.php');

$ary_old = (array) json_decode(stripslashes($_GET['aryOld']));
$spe_old = array();
foreach($ary_old as $old){
	$spe_old[$old->id] = $old->etat;
}

/**
 * Mise à jour du mur
 */

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
				//$item['pseudo'] = $ary_url[2];
				$item['title'] .= " - " . strip_tags(substr($item['description'],0,255));
			}
		}else{
			if($item['link'] == "SMS"){
				$item['pseudo'] = "SMS";
				$item['title'] = strip_tags($item['description']);
			}else if($item['link'] == "WEB"){
				$item['pseudo'] = $item['title'];
				$item['title'] = strip_tags($item['description']);
			}
		}
		
		$item['twitdate'] = Timesince($item['timestamp'],'');
		//$item['twitdate'] = $item['timestamp'];
		
		// Type d'affichage des messages 
		
		if(array_key_exists($item['id'], $spe_old)){
			// Ancien : message déjà présent sur le mur
			if($item['visible'] == 0 && $spe_old[$item['id']] == 0){
				$item['etat'] = "msgNO";
				// Efface le contenu du message avant de fermer le message
				//$item['title'] = "";
			}elseif($item['visible'] == 0 && $spe_old[$item['id']] == 1){
				$item['etat'] = "msgNO";
				// Efface le contenu du message avant de fermer le message
				//$item['title'] = "";
			}elseif($item['visible'] == 1 && $spe_old[$item['id']] == 0){
				$item['etat'] = "msgOK";
			}elseif($item['visible'] == 1 && $spe_old[$item['id']] == 1){
				$item['etat'] = "msgOK";
			}
		}else{
			// Nouveau : message non présent sur le mur précédement
			if($item['visible'] == 0){
				$item['etat'] = "msgNO";
				//$item->title = "";
			}elseif($item['visible'] == 1){
				$item['etat'] = "msgOK";
			}
		}
		$ary_twit[] = $item;
	}
	echo '{"twits":' . json_encode($ary_twit) . '}';
//}
