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
		$f = '/var/log/log_catfeeder';
		$fl = fopen($f, 'a') or die("Nu pot deschide fisier log.");	
		$l = fwrite($fl, $text);
		fclose($f);
		return;
	}
}

function AflaPassLocal(){
$f = '/etc/pass-catfeeder';
	$fp = fopen($f, 'r') or die("Nu pot deschide fisier pass.");	
	$p = fread($fp, filesize($f));
	fclose($f);
echo $p;
return $p;
}

function SetariDefault(){
	$fc = '/dev/shm/setariweb';
	
	if (!file_exists($fc)){
		logging("Fisierul de configurare NU exista pe disc!\n","err");
		return;
	}	
	$fp = fopen($fc, "w") or die("Nu pot deschide fisier conf.");
	
	if ( fwrite($fp, "Activ=0\nCantitate=0\n") )
		logging("[".date(h:i:s)."] INFO: Fisier configurare scris cu succes cu valorile DEFAULT!\n","local");
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
	$pass_local = AflaPassLocal();
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
		logging("[".date(h:i:s)."] INFO: Fisier configurare scris cu succes!","local");
	else
		logging("Eroare la scrierea fisierului de conf!","err");
	fclose($f);
	
	/* Asteptam 3 secunde dupa care scriem valorile default */
	sleep(3);
	SetariDefault();
	
	break;

case "streamVideo":	
	//system('fswebcam -l 1 -d /dev/video0 /var/www/pic/webcam.jpg --background', $retId);
	system('sudo systemctl start video_catfeeder.service', $retId);
	//print_r($retId);
	if ( $retId != 0 )
		logging("NU pot porni serviciul video_catfeeder.service!","err");
	if ( $retId == 0 )
		logging("[".date(h:i:s)."] INFO: Start video streaming!","local");		
	
	break;
	
case "stopStreamVideo":		
	system('sudo systemctl stop video_catfeeder.service', $retId);
	//system('sudo rm /var/www/pic/webcam.jpg', $retIdPic);
	//print_r($retId);
	if ( $retId != 0 )
		logging("NU pot opri serviciul video_catfeeder.service!","err");
	if ( $retId == 0 )
		logging("[".date(h:i:s)."] INFO: Video streaming oprit: OK!","local");
		
	/*if ( $retIdPic != 0 )
		logging("NU pot sterge imaginea generata","err");
	if ( $retIdPic == 0 )
		logging("Poza stearsa: OK!","info");		
	*/
	break;
}
?>
