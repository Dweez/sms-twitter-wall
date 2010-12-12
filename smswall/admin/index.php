<?php

require_once('../smswall.inc.php');
include('init_db.php');
include('../twitdate.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="generator" content="Bug" />
<meta name="viewport" content="initial-scale=1,user-scalable=yes" />
<title>TwitWall Factory</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="main.css" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="../js/json2.min.js"></script>
</head>
<body>
<div id="hiddenForm">
	<input id="last_ts" name="last_ts" type="text" style="width: 80px;" value="" />
	<input id="aryOld" name="aryOld" type="text" style="width: 150px;" value=""/>
</div>
<div id="menu">
	<div id="options" onclick="affichOptions();">
		options
	</div>
	
	<div id="hashtag">
		<?php echo '#' . $config['hashtag']; ?>
	</div>
	
	<div id="fresher" style="margin-top: 5px; display: none;">
		<img src="../media/preloader.gif" width="16" height="16" />
	</div>
</div>

<div id="overlay" style="display: none;">
	<div id="bulleMsg" class="bulle">
		<div id="closer" onclick="affichOptions();">
			<img src="../media/closer.png" width="48" height="48" />
		</div>
		
		<div class="title">Modération :</div>
		<div class="subTitle">
			Votre mur sera vide si le nombre de tweet modéré est > au nombre maximum de tweets affichés.
		</div>
		<form action="update_config.php" method="post" style="">
			<?php 
			if($config['modo_type'] == 1){
				$aprioriState = '';
				$aposterioriState = 'selected="selected" ';
			}else{
				$aprioriState = 'selected="selected" ';
				$aposterioriState = '';
			}
			?>
			<select name="modo_type" onchange="submit();" style="width: 100px;">
				<option <?php echo $aprioriState; ?>>A priori</option>
				<option <?php echo $aposterioriState; ?>>A posteriori</option>
			</select>
		</form>
		
		<div class="title">Hashtag / RSS :</div>
		<div class="subTitle">
			Tags multiples : séparés par une virgule.<br/>
			Le <strong>#</strong> est ajouté automatiquement.
		</div>
		<form action="update_config.php" method="post" style="">
			<input type="text" name="hashtag" value="<?php echo $config['hashtag']; ?>" style="width: 100px;"/>
			<input type="submit" value="OK" />
		</form> 
		
		<div class="title">Thèmes :</div>
		<div class="subTitle">
			Le thème doit exister dans /themes/
		</div>
		<form action="update_config.php" method="post" style="">
			<input type="text" name="theme" value="<?php echo $config['theme']; ?>" style="width: 100px;"/>
			<input type="submit" value="OK" />
		</form> 
		
		<div class="title">Avatar :</div>
		<div class="subTitle">
			Affichage des avatars
		</div>
		<form action="update_config.php" method="post" style="">
			<?php 
			if($config['avatar'] == 1){
				$afficherState = 'selected="selected" ';
				$masquerState = '';
			}else{
				$afficherState = '';
				$masquerState = 'selected="selected" ';
			}
			?>
			<select name="avatar" onchange="submit();" style="width: 100px;">
				<option <?php echo $afficherState; ?>>Afficher les avatars</option>
				<option <?php echo $masquerState; ?>>Masquer les avatars</option>
			</select>
		</form> 
		
		<div class="title">Purge du mur :</div>
		<div class="subTitle">
			Attention: en purgeant le mur vous allez vider la base de données !
		</div>
		<form action="update_config.php" method="post" style="">
			<label for="cbox">Je confirme : </label>
			<input type="checkbox" name="cbox" value="OK" /> 
			<input type="submit" value="OK" />
		</form> 
		
	</div>
</div>

<ul id="containerMsg">
	
	<?php

	$result = $db->query("SELECT * FROM items ORDER BY timestamp DESC LIMIT 0,30");
	$rowarray = $result->fetchall(PDO::FETCH_ASSOC);
	
	$ary_old = array();
	foreach($rowarray as $row){
		$ary_old[] = array("id"=>$row['id'], "etat"=>$row['visible']);
	}
	
	?>
</ul>

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
		
		$("#fresher").show();
		
		$.getJSON('getmessages.php', { aryOld: $('#aryOld').val() }, function(data) {

			// Parsing du JSon
			$('#containerMsg').empty();
			$.each(data.twits, function(i,twit){
				
				switch(twit.visible){
					case '0':
						classcss = 'msgNO';
						classTogVis = 'togon';
						break;
					case "1":
						classcss = 'msgOK';
						classTogVis = 'togoff';
						break;
				}
				switch(twit.bulle){
					case '0':
						classTogBul = 'bulOff';
						break;
					case "1":
						classTogBul = 'bulOn';
						break;
				}
				switch(twit.link){
					case 'SMS':
						pseudo =  twit.pseudo;
						avatar = '<img src="../media/default_sms.png" class="avatar" />';
						break;
					case 'WEB':
						pseudo = twit.pseudo;
						avatar = '<img src="../media/default_www.png" class="avatar" />';
						break;
					default:
						pseudo = '<a href="'+twit.link+'" target="_blank">' + twit.pseudo + '</a>';
						avatar = (twit.avatar) ? '<img src="' + twit.avatar + '" class="avatar" />' : '';
						break;
				}
				
				
				
				// Construction du li pour chaque tweets
				$('#containerMsg').append(
					'<li class="' + twit.etat + '" id="' + twit.id + '" name="' + twit.etat + '" visibility="' + twit.visible + '">'
						+ '<a href="javascript://" onclick="updateVis(' + twit.id + ',' + twit.visible + ');" class="' + classTogVis + '" style="float: right;">&nbsp;</a>'
						+ '<a href="javascript://" onclick="updateBul(' + twit.id + ',1);" class="' + classTogBul + '" style="float: right;">&nbsp;</a>'
						+ avatar
						+ '<span class="author">' + pseudo + ' : </span>'
						+ '<span class="textMsg">' + twit.title + ' - </span>'
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

			$("#fresher").hide();
		});
		
		// Rafraichissement toutes les 10 secondes
		timer = setTimeout("refreshTwit()", 10000);
	}
	refreshTwit();

	
	// Update de l'état d'un twit en asynchrone
	updateVis = function(id,visibility){
		$.post('update_vis.php', { id: id, oldvis: visibility }, function(data) {
			
			var state = data[2];
			twit = $("#"+id);
			
			if((state == '1')){
				var btnString = '<a href="javascript://" onclick="updateVis(' + id + ',1);" class="togoff" style="float: right;">&nbsp;</a>';
				twit.removeClass('msgNO').addClass('msgOK');
			}else{
				var btnString = '<a href="javascript://" onclick="updateVis(' + id + ',0);" class="togon" style="float: right;">&nbsp;</a>';
				twit.removeClass('msgOK').addClass('msgNO');
			}
			
			// Changer le content du a, sa class, etc c'est easy,
			// Par contre pas moyen de réécrire facilement l'attribut onclick...
			// D'ou cette soluce un peu trash ; on créé un nouveau bouton et on supprime l'ancien après :/
			twit.children().first().after(btnString);
			twit.children().first().remove();
			twit.attr('visibility',state);

			// Mise en cache
			// @todo : Attention penser à décommenter l'appel de la fonction lors du passage en Ajax
			archiveAry();
		});
	}

	updateBul = function(id,etatBul){
		$.post('update_bul.php', { id: id, switchBul: etatBul }, function(data) {
			
			var state = data[2];
			twit = $("#"+id);
			
			if((state == '1')){
				var btnString = '<a href="javascript://" onclick="updateBul(' + id + ',1);" class="bulOn" style="float: right;">&nbsp;</a>';
			}else{
				var btnString = '<a href="javascript://" onclick="updateBul(' + id + ',0);" class="bulOff" style="float: right;">&nbsp;</a>';
			}
			
			// Soluce un peu trash ; on créé un nouveau bouton et on supprime l'ancien après :/
			twit.children().eq(1).after(btnString);
			twit.children().eq(1).remove();
			twit.attr('visibility',state);

			// Mise en cache
			// @todo : Attention penser à décommenter l'appel de la fonction lors du passage en Ajax
			//archiveAry();
		});
	}

	// Mise en cache
	archiveAry = function(){
		// Récupération de l'état d'affichage des twits
		var myT = new Array();
		$('li').each(function(index) {
		    var tw = {"id": $(this).attr("id"), "etat": $(this).attr("visibility") };
		    myT.push(tw);
		  });
		
		// Mise en cache de l'état des twits pour comparaison à la prochaine lecture
		$('#aryOld').val(JSON.stringify(myT));
	}
	
	affichOptions = function(){
		$("#overlay").height($("#containerMsg").height());
		$("#overlay").toggle("fast");
		
	}
	
});
</script>
</body>
</html>

