<?php

require_once('../smswall.inc.php');

// Création de la base de données
$db->exec("BEGIN;
CREATE TABLE IF NOT EXISTS 'config_wall' (
	'id' INTEGER PRIMARY KEY  NOT NULL,
	'modo_type' INTEGER DEFAULT 0,
	'hashtag' VARCHAR,
	'phone_number' VARCHAR,
	'bulle' BOOL,
	'avatar' BOOL,
	'ctime' INTEGER,
	'mtime' INTEGER
);

CREATE TABLE IF NOT EXISTS 'items' (
	'id' INTEGER PRIMARY KEY  NOT NULL, 
	'title' VARCHAR, 
	'link' VARCHAR, 
	'description' VARCHAR, 
	'timestamp' INTEGER, 
	'visible' INTEGER,
	'bulle' INTEGER DEFAULT 0,
	'avatar' VARCHAR
);
COMMIT;");

// Lecture de la config
$qconfig = $db->query("SELECT * FROM config_wall");
$config = $qconfig->fetch(PDO::FETCH_ASSOC);

// Si la config est vide, création de la config par défaut
if(empty($config)){
	echo "Création de la config...";
	// @todo : Changer le numéro de téléphone
	$db->exec("INSERT INTO config_wall VALUES(NULL,0,'Android','0606060606',0,0,'".time()."','".time()."');");
}

// Mise à jour de la config
$qconfig = $db->query("SELECT * FROM config_wall");
$config = $qconfig->fetch(PDO::FETCH_ASSOC);
