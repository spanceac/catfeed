<?php 
@session_start();
$ses = $_SESSION['login_activ'];
if ( isset($ses) && $ses == 1 ){
	echo "<a href='../intel/logic.php?act=logout'>[ Deconectare ]</a>";
?>
<html>
<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title>Cat Feeder | Panou de control</title>
<link rel="stylesheet" type="text/css" href="grafic.css" />
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">
	
	function activare(){
		var cant = document.getElementById('cantitate').value;
		var ora = document.getElementById('timeInfo').innerHTML;
		
		$('#info2').load('../intel/logic.php?act=scrieSetari&cantitate='+cant+'&timp='+ora);
		$("#butonActivare").attr("disabled", "disabled");
		$('#info2').html('<p>* Butonul de activare este dezactivat pt 10 secunde</p>');
		window.setTimeout("$('#butonActivare').removeAttr('disabled');",10000);						
		window.setTimeout($('#info2').empty(),10500);
		return true;
	}
	function elapsed(){
		/* Apelam la interval de 10 sec */
		setInterval(function(){ $('#elapsed').load('../intel/logic.php?act=elapsedTime'); }, 5000);
	}
	function cantitateCutie(){
		setInterval(function(){ $('#cutie').load('../intel/logic.php?act=cutieGoala'); }, 1000);
	}
	function videoStream(videoFlag){
	var stream = 0;
		if ( videoFlag == 0 ){			
			//porneste subshell cu comanda fswebcam
			$('#streamingVideo').load('../intel/logic.php?act=streamVideo');		
			//stream = window.setInterval(function(){ $('#streamingVideo').fadeIn('fast'); },1200);
			stream = window.setInterval(function(){ $('#streamingVideo').html('<img src="../pic/webcam.jpg" width="360" />'); },1100);
			$("#butonVideo").attr("disabled", "disabled");
			
			return true;
		}
		if ( videoFlag == 1 ){
			stream=window.clearInterval(stream);
			$('#streamingVideo').load('../intel/logic.php?act=stopStreamVideo');			
			$('#streamingVideo').html('<img src="../design/img/no.jpg" width="360" />');
			//$('#butonVideo').removeAttr('disabled');			
			window.location.reload(true);
			$("#butonDeactivareVideo").attr("disabled", "disabled");
			return false;
		}		
	}	
function activareLumina(){
	$('#infoLumina').load('../intel/logic.php?act=iluminare&on=1');	
	/* Dupa cateva secunde stingem lumina */
	window.setTimeout(function(){$('#infoLumina').load('../intel/logic.php?act=iluminare&on=0')},10000);

}
	function ora(){					
/*		setInterval(function(){
			var time=new Date();
			var o= time.getHours();
			var m= time.getMinutes();
			var oracurenta = o+':'+m;
			$('#timeInfo').html(oracurenta); },1000);
			*/
			setInterval(function(){ $('#timeInfo').load('../intel/logic.php?act=oraCurenta'); },1000);
			return true;
	}
</script>
</head>

<body onload="ora(); elapsed(); cantitateCutie(); Response.Cache.SetNoStore();">
<div id="tot">
<table width="600" align="center" border="0" cellspacing="2">
	<tr>
		<td>
			<div> Ora curenta: 
				<div id="timeInfo"></div>
			</div>
		</td>
		<td>
			<div id="elapsed"></div>
		</td>		
	</tr>
	<tr>
		<td><h2>Da drumu' la mancare</h2></td>
		<td><h2>Cam cat vrei sa dai ?</h2></td>
	</tr>
	<tr>
			<td colspan="2"> <hr /> </td>
	</tr>
	<tr>
		<td>
			<div id="cutie"></div>
			<input type="button" name="activeaza" id="butonActivare" value="Activare" onclick="activare()" />
			<div id="info2"></div>
		</td>
		<td>
			<select id="cantitate">
				<option value="100"> -- 100 gr -- </option>
				<option value="200" selected="selected"> -- 200 gr -- </option>
				<option value="300"> -- 300 gr -- </option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="button" name="lumina" id="butonLumina" value="Activare lumina" onclick="activareLumina()" />
			<div id="infoLumina"></div>
		</td>		
	</tr>

	<tr>
		<td>
			<h2>Video streaming</h2>
			<input type="button" name="video" id="butonVideo" value="Streaming Video" onclick="videoStream(0)" />
			<input type="button" name="stopVideo" id="butonDeactivareVideo" value="Dezactivare Streaming Video" onclick="videoStream(1)" />
		</td>
		<td>			
			<div id="streamingVideo" style="width:355px; height:350px;">				
				<img src="../design/img/no.jpg" width="360" />
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p>* Selecteaza (daca vrei) cantitatea si da drumu' la mancare!</p>
		</td>
	</tr>

</table>
</div>
<center>
	&copy; Web enabled cat feeder Raspberry Pi, hackathon
</center>
</body>
</html>
<?php
} else {
	echo "NU ai acces la aceasta pagina! Trebuie sa te conectezi!\n";
}
?>
