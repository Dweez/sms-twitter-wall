<?php

// THIS IS ABSOLUTELY ESSENTIAL - DO NOT FORGET TO SET THIS
@date_default_timezone_set("GMT");

require_once('../smswall.inc.php');
$qconfig = $db->query("SELECT * FROM config_wall");
$config = $qconfig->fetch(PDO::FETCH_ASSOC);

$result = $db->query("SELECT * FROM items ORDER BY timestamp DESC");
$rowarray = $result->fetchall(PDO::FETCH_ASSOC);

$writer = new XMLWriter();
// Output directly to the user

$writer->openURI('php://output');
$writer->startDocument('1.0');

$writer->setIndent(4);

// declare it as an rss document
$writer->startElement('rss');
$writer->writeAttribute('version', '2.0');
$writer->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');


$writer->startElement("channel");
//----------------------------------------------------
//$writer->writeElement('ttl', '0');
$writer->writeElement('title', 'Sms Wall #'.$config['hashtag']);
$writer->writeElement('description', 'Flux RSS des contributions au Sms/Twitter Wall. Tag en cours : #'.$config['hashtag']);
$writer->writeElement('link', 'http://www.sms-wall.org');
$writer->writeElement('pubDate', date("D, d M Y H:i:s e"));
    $writer->startElement('image');
        $writer->writeElement('title', 'La Cantine Numérique Rennaise');
        $writer->writeElement('link', 'http://www.lacantine-rennes.net/');
        $writer->writeElement('url', 'http://www.dweez.com/smswall/media/logo.png');
        $writer->writeElement('width', '188');
        $writer->writeElement('height', '116');
    $writer->endElement();
//----------------------------------------------------

    
foreach($rowarray as $row){
	$ary_url = explode("/", $row['link']);
	// Traitement de certains champs 
	if($ary_url[2]=="twitter.com"){
		$row['pseudo'] = $ary_url[3];
	}else{
		if($row['link'] == "SMS"){
			$row['link'] = "http://www.sms-wall.org";
			$row['pseudo'] = "SMS";
			$row['title'] = strip_tags($row['description']);
		}else if($row['link'] == "WEB"){
			$row['link'] = "http://www.sms-wall.org";
			$row['pseudo'] = $row['title'];
			$row['title'] = strip_tags($row['description']);
		}
	}
	//----------------------------------------------------
	$writer->startElement("item");
	$writer->writeElement('title', $row['pseudo']);
	$writer->writeElement('link', $row['link']);
	$writer->writeElement('description', $row['description']);
	$writer->writeElement('guid', 'http://www.sms-wall.org');
	
	$writer->writeElement('pubDate', date("D, d M Y H:i:s e",$row['timestamp']));
	
	$writer->startElement('category');
	    $writer->writeAttribute('domain', 'http://www.sms-wall.org');
	    $writer->text(date("M Y"));
	$writer->endElement(); // Category
	
	// End Item
	$writer->endElement();
	//----------------------------------------------------
}


// End channel
$writer->endElement();

// End rss
$writer->endElement();

$writer->endDocument();

$writer->flush();
?> 