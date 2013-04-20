CC=gcc
CFLAGS=-Wall
LIBS=-lwiringPi

pwm-control: pwm-control.o
	$(CC) $(CFLAGS) $(LIBS) -o pwm-control pwm-control.c

clean:
	rm -f *o pwm-control
