CFLAGS=-Wall -g

pwm-control: pwm-control.c
	gcc $(CFLAGS) -o pwm-control pwm-control.c
clean:
	rm pwm-control
