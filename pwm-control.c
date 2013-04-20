#include <stdio.h>
//#include <wiringPi.h>
#include <stdlib.h>
#include <string.h>

int cant = 0;
int activ = 0;
int ilum = 0;

void aprinde_lumina(void)
{
	
}
void data_get(char *data)
{
	int init_addr;
	char *temp = malloc(50);
	init_addr = (int) temp;
	temp = strchr(data,'=');
	temp++;
	activ = atoi(temp);
	temp = strchr(temp,'=');
	temp++;
	cant = atoi(temp);
	temp = strchr(temp,'=');
	temp++;
	ilum = atoi(temp);
	temp = (char *) init_addr;
	free(temp);
	printf("activ %d, cant %d, ilum %d\n",activ, cant, ilum);
}

int main()
{
	FILE *f1;
	char *data;
	while(1)
	{
		data = malloc(100);
		f1 = fopen("/dev/shm/setariweb","r");
		if(f1 == NULL)
		{
			perror("Error opening settings file");
			exit(1);
		}
		fread(data, 1, 100, f1);
		printf("%s",data);
		data_get(data);
		free(data);
		fclose(f1);
		if(ilum)
			aprinde_lumina();
	}
	exit(0);
}

