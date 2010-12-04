<?php

require_once('../smswall.inc.php');
include('init_db.php');
include('../twitdate.php');

?>

<!DOCTYPE html> 
<html>
<head>
<meta name="generator" content="Bug" />
<meta name="viewport" content="initial-scale=1,user-scalable=yes" />
<title>TwitWall Factory</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="main.css" media="screen" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0a2/jquery.mobile-1.0a2.min.css" />
<script src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
<script src="http://code.jquery.com/mobile/1.0a2/jquery.mobile-1.0a2.min.js"></script>
</head>
<body>
<div data-role="page"> 

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
				<select name="modo_type" onchange="submit();">
					<option <?php echo $aprioriState; ?>>A priori</option>
					<option <?php echo $aposterioriState; ?>>A posteriori</option>
				</select>
			</form>
			
			<div class="title">Hashtag :</div>
			<div class="subTitle">
				Tags multiples : séparés par une virgule.<br/>
				Le <strong>#</strong> est ajouté automatiquement.
			</div>
			<form action="update_config.php" method="post" style="">
				<input type="text" name="hashtag" value="<?php echo $config['hashtag']; ?>"/>
				<input type="submit" value="OK" />
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
		
		$.getJSON('register.php', { aryOld: $('#aryOld').val() }, function(data) {

			// Parsing du JSon
			$('#containerMsg').empty();
			$.each(data.twits, function(i,twit){
				
				switch(twit.visible){
					case '0':
						classcss = 'msgNO';
						classtog = 'togon';
						break;
					case "1":
						classcss = 'msgOK';
						classtog = 'togoff';
						break;
				}
				
				// Construction du li pour chaque tweets
				$('#containerMsg').append(
					'<li class="' + twit.etat + '" id="' + twit.id + '" name="' + twit.etat + '" visibility="' + twit.visible + '">'
						+ '<a href="javascript://" onclick="update(' + twit.id + ',' + twit.visible + ');" class="' + classtog + '" style="float: right;">&nbsp;</a>'
						+ '<span class="author">' + twit.pseudo + ' : </span>'
						+ '<span class="textMsg">' + stripslashes(twit.title) + ' - </span>'
						+ '<span class="time">' + twit.twitdate + '</span>'
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
	// N'importe quoi, repenser le problème avec un listener sur la classe du bouton
	update = function(id,visibility){
		$.post('update_vis.php', { id: id, oldvis: visibility }, function(data) {
			
			var state = data[2];
			twit = $("#"+id);
			
			if((state == '1')){
				var btnString = '<a href="javascript://" onclick="update(' + id + ',1);" class="togoff" style="float: right;">&nbsp;</a>';
				twit.removeClass('msgNO').addClass('msgOK');
			}else{
				var btnString = '<a href="javascript://" onclick="update(' + id + ',0);" class="togon" style="float: right;">&nbsp;</a>';
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
			//archiveAry();
		});
	}


	affichOptions = function(){
		$("#overlay").height($("#containerMsg").height());
		$("#overlay").toggle("fast");
		
	}
	
});
</script>
</body>
</html>

