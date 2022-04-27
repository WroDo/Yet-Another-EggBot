# Taken from https://joy-it.net/files/files/Produkte/RB-Moto2/RB-Moto2%20Anleitung-20200323.pdf


import RPi.GPIO as GPIO
import time

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

coil_A_1_pin = 24 # pink
coil_A_2_pin = 4 # orange
coil_B_1_pin = 23 # blau
coil_B_2_pin = 25 # gelb
coil2_A_1_pin = 18 # pink
coil2_A_2_pin = 22 # orange
coil2_B_1_pin = 17 # blau
coil2_B_2_pin = 27 # gelb

GPIO.setup(coil_A_1_pin, GPIO.OUT)
GPIO.setup(coil_A_2_pin, GPIO.OUT)
GPIO.setup(coil_B_1_pin, GPIO.OUT)
GPIO.setup(coil_B_2_pin, GPIO.OUT)

GPIO.setup(coil2_A_1_pin, GPIO.OUT)
GPIO.setup(coil2_A_2_pin, GPIO.OUT)
GPIO.setup(coil2_B_1_pin, GPIO.OUT)
GPIO.setup(coil2_B_2_pin, GPIO.OUT)

def disable():
    GPIO.output(coil_A_1_pin, 0)
    GPIO.output(coil2_A_1_pin, 0)
    GPIO.output(coil_A_2_pin, 0)
    GPIO.output(coil2_A_2_pin, 0)
    GPIO.output(coil_B_1_pin, 0)
    GPIO.output(coil2_B_1_pin, 0)
    GPIO.output(coil_B_2_pin, 0)
    GPIO.output(coil2_B_2_pin, 0)
    
disable()



# EOF
