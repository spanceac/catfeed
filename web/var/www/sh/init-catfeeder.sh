#!/bin/sh

setariweb='/dev/shm/setariweb'
log='/var/log/log_catfeeder'
doConfig(){
	touch $setariweb
	chmod 777 $setariweb
	echo "Activ=0\nCantitate=0\n" > $setariweb
	return 0
}
doLog(){
	if [ -f $log ]; then
		rm $log
		touch $log
		return 0
	else
		touch $log
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
