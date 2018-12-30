import RPi.GPIO as GPIO
import time
 
GPIO.setmode(GPIO.BCM)
 
coil_A_1_pin = 25
coil_A_2_pin = 24
coil_B_1_pin = 7
coil_B_2_pin = 8
 
GPIO.setup(coil_A_1_pin, GPIO.OUT)
GPIO.setup(coil_A_2_pin, GPIO.OUT)
GPIO.setup(coil_B_1_pin, GPIO.OUT)
GPIO.setup(coil_B_2_pin, GPIO.OUT)

forward_seq = ['1000', '0100', '0010', '0001']
reverse_seq = list(forward_seq) # to copy the list
reverse_seq.reverse()
 
def forward(delay, steps):    
    for i in range(steps):
        for step in forward_seq:
            set_step(step)
            time.sleep(delay)
    # time.sleep(3)
 
def backwards(delay, steps):    
    for i in range(steps):
        for step in reverse_seq:
            set_step(step)
            time.sleep(delay)
    # time.sleep(3)
 
    
def set_step(step):
    GPIO.output(coil_A_1_pin, step[0] == '1')
    GPIO.output(coil_A_2_pin, step[1] == '1')
    GPIO.output(coil_B_1_pin, step[2] == '1')
    GPIO.output(coil_B_2_pin, step[3] == '1')


if __name__ == '__main__':
    while True:
        set_step('0000')
        delay = input("Delay between steps (milliseconds)?")
        steps = input("How many steps forward? ")
        forward(int(delay) / 1000.0, int(steps))
        set_step('0000')
        steps = input("How many steps backwards? ")
        backwards(int(delay) / 1000.0, int(steps))

# 3 50一圈