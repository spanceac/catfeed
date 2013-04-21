#!/bin/sh

setariweb='/dev/shm/setariweb'
log='/var/www/log/log_catfeeder'
doConfig(){
	touch $setariweb
	chmod 777 $setariweb
	echo "Activ=0\nCantitate=0\nIluminare=0" > $setariweb
	return 0
}
doLog(){
	if [ -f $log ]; then
		rm $log
		touch $log
		chmod 777 $log
		return 0
	else
		touch $log
		chmod 777 $log
		return 0
	fi
}

if [ -f $setariweb ]; then
	rm $setariweb
	doConfig
	doLog
	exit 0
else
	doConfig
	doLog
	exit 0
fi
