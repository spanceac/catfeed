<?php
$act = $_GET['act'];

function logging($text, $tip){
	if ( $tip == "err" ){
		echo "<p style='color:red';>$text</p>";
		return;
	} elseif ( $tip == "info" ){
		echo "<p style='color:green'>$text</p>";
		return;
	} elseif ( $tip == "local" ){
		$f = '/var/www/log/log_catfeeder';
		$fl = fopen($f, 'a+') or die("Nu pot deschide fisier log.");	
		fwrite($fl, $text) or die("Nu pot scrie in fisier log.");	
		fclose($f);
		return;
	}
}

function CitesteFisier($fisierul){
	$fp = fopen($fisierul, 'rb') or die("Nu pot deschide fisier pass.");	
	$continut = fread($fp, filesize($fisierul));
	fclose($fisierul);	
return $continut;
}

function SetariDefault($ora){
	$fc = '/dev/shm/setariweb';
	
	if (!file_exists($fc)){
		logging("Fisierul de configurare NU exista pe disc!\n","err");
		return;
	}	
	$fp = fopen($fc, "w") or die("Nu pot deschide fisier conf.");
	
	if ( fwrite($fp, "Activ=0\nCantitate=0\nIluminare=0\nOra=".$ora) )
		logging("[".date('h:i:s')."] INFO: Fisier configurare scris cu succes cu valorile DEFAULT!\n","local");
	else
		logging("Eroare la scrierea fisierului de conf!","err");
	fclose($f);
	return;
}

switch ($act){

case "login":
	echo "LOGIN\n";
	$pass = $_POST['pass'];
	//$pass = md5($pass);
	$pass = (string)$pass;
	$pass_local = CitesteFisier('/etc/pass-catfeeder');
	$pass_local = (string)$pass_local;
	
	echo strcmp($pass_local, $pass);
	
	if ( strcmp($pass_local, $pass) == 0 ){		
		logging("Parola introdusa NU este corecta!\n","err");
		echo "\n".$pass. " | ". $pass_local;
		//return;	
	} else {
		@session_start();
		$_SESSION['login_activ'] = 1;
		@header('Location: ../design/main.php');
	}	
	
	break;
	
case "logout":
	@session_start();
	@session_destroy();
	@header('Location: ../index.php');
	break;

case "scrieSetari":		
	$cantitate = $_GET['cantitate'];
	$ora = $_GET['timp'];
	$fc = '/dev/shm/setariweb';
	
	if (!file_exists($fc)){
		logging("Fisierul de configurare NU exista pe disc!\n","err");
		return;
	}	
	$fp = fopen($fc, "w") or die("Nu pot deschide fisier conf.");
	
	if ( fwrite($fp, "Activ=1\nCantitate=".$cantitate."\nIluminare=1\nOra=".$ora) )
		logging("[".date('h:i:s')."] INFO: Fisier configurare scris cu succes!\n","local");
	else
		logging("Eroare la scrierea fisierului de conf!","err");
	fclose($f);
	
	/* Asteptam 3 secunde dupa care scriem valorile default */
	sleep(3);
	SetariDefault($ora);
	
	break;

case "streamVideo":	
	//system('fswebcam -l 1 -d /dev/video0 /var/www/pic/webcam.jpg --background', $retId);
	system('sudo systemctl start video_catfeeder.service', $retId);
	//print_r($retId);
	if ( $retId != 0 )
		logging("NU pot porni serviciul video_catfeeder.service!\n","err");
	if ( $retId == 0 )
		logging("[".date('h:i:s')."] INFO: Start video streaming!\n","local");		
	
	break;
	
case "stopStreamVideo":		
	system('sudo systemctl stop video_catfeeder.service', $retId);
	
	if ( $retId != 0 )
		logging("NU pot opri serviciul video_catfeeder.service!\n","err");
	if ( $retId == 0 )
		logging("[".date('h:i:s')."] INFO: Video streaming oprit: OK!\n","local");
		
	break;
	
case "iluminare":
$fc = '/dev/shm/setariweb';
	$on_off = $_GET['on'];
	
	
	if (!file_exists($fc)){
		logging("Fisierul de configurare NU exista pe disc!\n","err");
		return;
	}	
	$fp = fopen($fc, "r+") or die("Nu pot deschide fisier conf.");
	$continut = fread($fp, filesize($fc));
	
	if ( $on_off == 1 ){
		$rez_activare = str_replace("Iluminare=0","Iluminare=1",$continut);
		logging("Iluminare activata\n","info");	
		/* golim continutul fisierului */
		if ( file_put_contents($fc, $rez_activare)  == FALSE ){
			logging("Nu pot aprinde lumina!\n","err");
			return;
		} else {
			logging("[".date('h:i:s')."] INFO: Lumina activata: OK!\n","local");
		}
		
		
	} else if ( $on_off == 0 ) {
		$rez_dezactivare = str_replace("Iluminare=1","Iluminare=0",$continut);
		logging("Iluminare dezactivata\n","info");
		if ( file_put_contents($fc, $rez_dezactivare) == FALSE ){
				logging("Nu pot stinge lumina!\n","err");
			return;
		} else {
			logging("[".date('h:i:s')."] INFO: Lumina dezactivata: OK!\n","local");
		}
		
	}
	fclose($f);	
	break;

case "cutieGoala":
$f = '/dev/shm/cutie';
	$continut = file_get_contents($f);
	
	if ( $continut == 1 )
			logging("E bine! Mai este mancare in cutie!\n","info");
	else if ( $continut == 0 )
			logging("Nu-i bine! Nu mai este mancare in cutie!\n","err");
	
	break;

case "elapsedTime":
	$continut = CitesteFisier('/dev/shm/setariweb');
	$timp = strstr($continut, 'Ora=');
	if ( $timp == FALSE ){
		echo "NU este disponibil timpul de cand nu a mai fost hranita pisica!";
		return;
	}
	$timp = strstr($timp, '=');
	$timp = trim($timp, '=');
	
	$ora = strstr($timp, ':', true); $ora = trim($ora, ':');
	$min = strstr($timp, ':'); 
	$min = trim($min, ':');
	
	$t_o = (int)date('h') - $ora;
	$t_m = (int)date('i') - $min;
		
	if ( $t_o <= 0 ){
		if ( $t_m > 0 )
			logging("Au trecut: <b>" . $t_m . " min</b> de cand nu ti-ai mai hranit pisica","info");
	} else if ( $t_o == 1 && $t_m < 0 ){
		$dif = 60+$t_m;
		logging("Au trecut: <b>" . $dif . " min</b> de cand nu ti-ai mai hranit pisica","info");
		
	} else if ( $t_o > 1 ){
		if ( $t_m >= 0 )
			logging("Au trecut: <b>" . $t_o. " ore</b> si <b>" . $t_m . " min</b> de cand nu ti-ai mai hranit pisica","info");
		else{
			$dif_minute = 60+$t_m;
			logging("Au trecut: <b>" . $t_o. " ore</b> si <b>" . $dif_minute . " min</b> de cand nu ti-ai mai hranit pisica","info");
		}
	} else if ( $t_o > 4 ){
		logging("!!! Nu ai hranit pisica de peste 4 ore !!!","err");
		/* TBD: send_mail(); */
	}
	
	break;
	
case "oraCurenta":
	echo date('h:i');
	break;
}
?>
