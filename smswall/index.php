<?php 
require_once('smswall.inc.php');

try {
	$qconfig = $db->query("SELECT * FROM config_wall");
	$config = $qconfig->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
	echo $e->getMessage();
}

$theme = (!empty($config)) ? $config['theme'] : 'default';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="generator" content="Bug" />
<title>TwitWall</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="themes/<?php echo $theme; ?>/main.css" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="js/json2.min.js"></script>
</head>
<body>

	<div id="overlay" style="display: none;">
		<div id="bulleMsg" class="bulle"></div>
	</div>
	
	
	
	<div id="menu">
		<span id="infos"></span>
		<span id="logo"></span>
	</div>
	
	<div id="wrapper">
		<div id="hiddenForm">
			<input id="last_ts" name="last_ts" type="text" style="width: 80px;" value="" />
			<input id="aryOld" name="aryOld" type="text" style="width: 150px;" value=""/>
		</div>
		<div id="background">
			
		</div>
		<div id="menuOmbre">
	
		</div>
		
		<ul id="containerMsg">
			<?php
	
			/**
			 * Construction du wall
			 * Lecture des derniers tweets en base pour la création et alimentation du tableau de référence $ary_old
			 */ 
			
			$result = $db->query("SELECT * FROM items ORDER BY timestamp DESC LIMIT 0,30");
			$rowarray = $result->fetchall(PDO::FETCH_ASSOC);
	
			$ary_old = array();
			foreach($rowarray as $row){
				$ary_old[] = array("id"=>$row['id'], "etat"=>$row['visible']);
			}
	
			?>
		</ul>
		
	</div>
	<div id="footer">
		<div id="ftContainer">
			<div id="ftContent">
			</div>
		</div>
	</div>
<script>
$(document).ready(function() {
	function stripslashes(str) {
		str=str.replace(/\\'/g,'\'');
		str=str.replace(/\\"/g,'"');
		str=str.replace(/\\0/g,'\0');
		str=str.replace(/\\\\/g,'\\');
		return str;
	}

	// Init
	var timer;
	$('#aryOld').val('<?php echo json_encode($ary_old); ?>');

	refreshTwit = function(){
		// Mise à jour du mur : 
		// @param (array)aryOld : état actuel du mur
		// return JSon data.twits
		
		// Va falloir ranger ce bazar !
		var alertPseudo;
		var alertTitle;
		var alertTwitdate;
		var etatBulle;
		var idBulle;
		
		$.post('getmessages.php', { aryOld: $('#aryOld').val() }, function(data) {

			// Parsing du JSon
			$('#containerMsg').empty();
			
			var config = data.config;
			
			var i = 0;
			$.each(data.twits, function(i,twit){
				/*if(i==0 && twit.etat == "animOpen"){
					alertPseudo = twit.pseudo;
					alertTitle = twit.title;
					alertTwitdate = twit.twitdate;
					i++;
				}*/
	
				if(config.avatar == 1){
					switch(twit.link){
						case 'SMS':
							avatar = '<img src="media/default_sms.png" class="avatar" />';
							break;
						case 'WEB':
							avatar = '<img src="media/default_www.png" class="avatar" />';
							break;
						default:
							avatar = (twit.avatar) ? '<img src="' + twit.avatar + '" class="avatar" />' : '';
							break;
					}
				}else{
					avatar = '';
					twit.avatarbig = '';
				}
				
				if(twit.bulle == 1){
					alertPseudo = twit.pseudo;
					alertTitle = twit.title;
					alertTwitdate = twit.twitdate;
					alertAvatar = twit.avatarbig ? '<img src="' + twit.avatarbig + '" class="avatarbig" />' : avatar;
					idBulle = twit.id;
				}

				
				
				// Construction du li pour chaque tweets
				$('#containerMsg').append(
					'<li class="' + twit.etat + '" id="' + twit.id + '" name="' + twit.etat + '" visibility="' + twit.visible + '">'
						+ avatar
						+ '<span class="author">' + twit.pseudo + '</span>'
						+ '<span class="textMsg"> ' + stripslashes(twit.title) + ' - </span>'
						+ '<span class="time">' + twit.twitdate + '</span>'
						+ '<div style="clear: both;"></div>'
					+ '</li>');
				
			});

			// Récupération de l'état d'affichage des twits pour la mise en cache
			var myT = new Array();
			$('li').each(function(index) {
			    var tw = {"id": $(this).attr("id"), "etat": $(this).attr("visibility") };
			    myT.push(tw);
			  });
			
			// Mise en cache de l'état des twits pour comparaison à la prochaine lecture
			$('#aryOld').val(JSON.stringify(myT));

			// @Todo : voir si il n'y a pas moyen de gérer le masquage seulement en css. 
			// Merger staticClose et animClose qui st tous les deux .msgNO 
			$("li[name=staticClose]").hide();
			$("li[name=animOpen]").hide();
			$("li[name=animClose]").show();

			// Gestion d'affichage des tweets
			// Handler pour la création de la queue
			var para = $("ul");

			// Alimentation de la queue 1
			// Fermeture des tweets refusés
			para.queue(
				"testQueue",
				function( next ){
					var Closed = $("li[name=animClose]").show();
					var currentClose = 0;
					function nextClose()
					{
						Closed.eq(currentClose).slideUp(300, nextClose);
						currentClose++;
						if(currentClose == $("li[name=animClose]").length + 1){
							next();
						}
					}
					nextClose();
				}
			);

			// Alimentation de la queue 2 - petite pause
			para.delay( 300, "testQueue" );

			// Ouverture du Twit Bulle 
			para.queue(
				"testQueue",
				function( next ){
					
					// test à l'arrache : plus élégant de bosser avec data.twits[0].pseudo
					if(alertPseudo){
						$.post('admin/update_bul.php', { id: idBulle, switchBul: '0'}, function(data) {
							$("#bulleMsg").html(
								'<div id="splash" class="animOpen" style="display: none;" >'
									+ alertAvatar
									+ '<span class="author">' + alertPseudo + '</span>'
									+ '<span class="textMsg"> ' + stripslashes(alertTitle) + ' - </span>'
									+ '<span class="time">' + alertTwitdate + '</span>'
									+ '<div style="clear: both;"></div>'
								+ '</div>'
							);
							
							$("#overlay").fadeIn(500,function(){
								$("#splash").slideDown(400);
							}).delay(4000).fadeOut(500, next);
						});
						
					}else{
						
						next();
					}
				}
			);


			/*para.queue(
				"testQueue",
				function( next ){
					
				}
			);*/


			
			
			// Alimentation de la queue 3
			// Ouverture des nouveaux tweets
			para.queue(
				"testQueue",
				function( next ){
					var Opened = $("li[name=animOpen]").hide();
					var currentOpen = 0;
					function nextOpen()
					{
						Opened.eq(currentOpen).slideDown(200, nextOpen);
						currentOpen++;
						if(currentOpen == $("li[name=animOpen]").length + 1){
							next();
						}
					}
					nextOpen();
				}
			);

			// On vide la queue
			para.dequeue( "testQueue" );
			
			// Masquage des twits déjà refusés
			$("li[name=staticClose]").hide();
			$("li[name=animOpen]").hide();
		});
		
		// Rafraichissement toutes les 10 secondes
		timer = setTimeout("refreshTwit()", 10000);
	}
	refreshTwit();
});
</script>
</body>
</html>


