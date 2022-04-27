#!/usr/bin/python
#
# https://tutorials-raspberrypi.de/raspberry-pi-servo-motor-steuerung/
# Sollte der Servo Motor etwas zittern, während er sich nicht bewegt, kannst du den Puls mit p.ChangeDutyCycle(0) ruhig stellen.

# Servo Anschluß:
#  GND: 39 oder 9, 25, 6, 14, 20, 30,34
#  Signal: 37 (GPI26) zweiter von unten links
#  5V: 2 oder 4
# ATTENTION: USE A 1K-OHM RESISTOR ON SIGNAL LINE!!!!!!!!!!!!oneoneeleven


import RPi.GPIO as GPIO
import time

servoGPIO = 26 # GPIO 26 ist Pin 37 (links unten)
#servoGPIO = 17 # GPIO 17 ist Pin 11 (links 6 von oben)
GPIO.setmode(GPIO.BCM)
GPIO.setup(servoGPIO, GPIO.OUT)

#p = GPIO.PWM(servoGPIO, 50) # GPIO als PWM mit 50Hz
p = GPIO.PWM(servoGPIO, 500) # GPIO als PWM mit 500Hz


#p.start(2.5) # Initialisierung

# Duty Cycle solle zwischen -10 und +10% Abweichug von 50% liegen
# 40 > 50 < 60

if 1 == 1:
	p.start(40) # Initialisierung
#	for i in range(70, 40, -1):
#		p.ChangeDutyCycle(i)
#		time.sleep(0.1)
	time.sleep(0.5)

if 1 == 0:
	p.ChangeDutyCycle(40)
	# Give time to move
	time.sleep(0.5)

p.stop()
GPIO.cleanup()
