<html>
<head>
<title>Cat Feeder | Index</title>
<link rel="stylesheet" type="text/css" href="design/grafic.css" />
<script type="text/javascript">
	function verifica(){		
		if ( document.getElementById('pass').value == '' ){
			alert('Introdu parola!');
			return false;
		} else {
			return true;
		}
	}
	function focusCursor(){
		document.getElementById('pass').focus();
	}
</script>
</head>

<body onload="focusCursor()">

<div id="cont">
<table width="500" align="center" border="0" cellspacing="0">
<form method="post" action="./intel/logic.php?act=login">
	<tr>
		<td><h2>Cat Feeder | Login </h2></td>
	</tr>
	<tr>
		<td><h3>Introdu parola:</h3></td>
		<td rowspan="3"><div style="margin-left:30px;"><img src="design/img/rpi.jpg" width="200" /></div></td>
	</tr>

	<tr>
		<td><input type="password" name="pass" id="pass" /></td>
	</tr>
	<tr>
		<td><input type="submit" name="login" value="Conectare" onclick="return verifica()"></td>
	</tr>
</form>
</table>
</div>
<center>
	&copy; Web enabled cat feeder Raspberry Pi, hackathon
</center>

</body>
</html>
