#include <stdio.h>
#include <stdlib.h>
#include <wiringPi.h>
#include <string.h>
#include <unistd.h>
#define PWM_CLOCK 27000
#define PWM_DIVISOR 10000000/PWM_CLOCK

int activ, cant, ilum;

void aprinde_lumina(void)
{
}

void data_get(char *data)
{
    char *temp;
    int init_addr;
    temp = malloc(50);
    init_addr = (int) temp;
    temp = strchr(data, '=');
    temp++;
    activ = atoi(temp);
    temp = strchr(temp, '=');
    temp++;
    cant = atoi(temp);
    temp = strchr(temp,'=');
    temp++;
    ilum = atoi(temp);
    temp = (char *) init_addr;
    free(temp);
}
void init_PWM(void)
{
    int i;
    i = wiringPiSetup();
    if(i < 0)
    {
	printf("Necazuri la initializare PWM\n");
	exit(1);
    }
    pinMode(1, PWM_OUTPUT);
    pwmSetMode(PWM_MODE_MS);
    pwmSetClock(PWM_DIVISOR);
    pwmWrite(1, 0); // do nothing for the moment
}
void release_door(void)
{
	pwmWrite(1, 75); //servo goto open position
	usleep(600000); //wait until servo reaches open position
	pwmWrite(1, 0); //stop servo motor
	sleep(2); //wait with tray opened for 1 seconds
	pwmWrite(1, 30); //servo goto closed position
	usleep(600000); //wait until servo reaches closed position
	pwmWrite(1, 0); //stop servo motor
}

int main(void)
{
    sleep(5);
    init_PWM();
    FILE *fd;
    char *data;

    while(1)
    {
	data = malloc(50);
	fd = fopen("/dev/shm/setariweb", "r");
	if(fd < 0)
	{
	    perror("Settings file open error");
	    exit(1);
	}
	fread(data, 1, 50, fd);
	data_get(data);
	if(activ)
	{
	    release_door();
	    sleep(5); //at least 5 seconds between feedings
	}
	else
	{
	    usleep(100000); //sleep to avoid massive cpu usage
	}
	fclose(fd);
	free(data);
    }
    return 0;
}
