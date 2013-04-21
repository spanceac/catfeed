#include <stdio.h>
#include <stdlib.h>
#include <wiringPi.h>
#include <string.h>
#include <unistd.h>
#define PWM_CLOCK 27000
#define PWM_DIVISOR 10000000/PWM_CLOCK

int activ, cant, ilum;

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
    i = wiringPiSetupGpio();
    if(i < 0)
    {
	printf("Necazuri la initializare PWM\n");
	exit(1);
    }
    pinMode(18, PWM_OUTPUT);
    pwmSetMode(PWM_MODE_MS);
    pwmSetClock(PWM_DIVISOR);
    pwmWrite(18, 0); // do nothing for the moment
}
void release_door(void)
{
    int i;
    for(i = 0; i < cant/100; i++)
    {
	pwmWrite(18, 40); //servo goto open position
	usleep(600000); //wait until servo reaches open position
//	sleep(2); //wait with tray opened for 1 seconds
	pwmWrite(18, 30); //servo goto closed position
	usleep(600000); //wait until servo reaches closed position
    }
}
int cutie_plina(void)
{
    int state;
    digitalWrite(1, HIGH); //aprinde led test
    usleep(100000);
    state = digitalRead(17); //read LDR
    digitalWrite(1, LOW); //stinge led test
    if(state)
	return 1; //cutie plina
    else
	return 0; //cutie goala --> action
}
void softPWM(int val)
{
    digitalWrite(14, HIGH);
    usleep(10000);
    digitalWrite(14, LOW);
    usleep(10000);
}

int main(void)
{
    sleep(5);
    init_PWM();
    pinMode(17, INPUT); //LDR detectie cutie goala
    pinMode(1, OUTPUT); //LED detectie cutie goala
    pinMode(14, OUTPUT); //soft PWM
    pinMode(15, OUTPUT); //PIN light
    digitalWrite(15, LOW); //lights off
    FILE *fd, *fd2;
    char *data;
/*
    while(1)
    {
	if(cutie_plina())
	    printf("Cutia e plina\n");
	else
	    printf("Cutia e goala\n");
	usleep(500000);
    }
*/
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
	fd2 = fopen("/dev/shm/cutie","w");
	if(fd2 < 0)
	{
	    perror("Cutie status file open error");
	    exit(1);
	}
	if(cutie_plina())
	    fputs("Cutia=1",fd2);
	else
	    fputs("Cutia=0",fd2);
	fclose(fd2);

	data_get(data);
	if(ilum)
	    digitalWrite(15, HIGH);
	else
	    digitalWrite(15, LOW);
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
