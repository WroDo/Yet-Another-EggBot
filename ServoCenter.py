# https://tutorials-raspberrypi.de/raspberry-pi-servo-motor-steuerung/
# Sollte der Servo Motor etwas zittern, während er sich nicht bewegt, kannst du den Puls mit p.ChangeDutyCycle(0) ruhig stellen.

# Servo Anschluß:
#  GND: 39 oder 9, 25, 6, 14, 20, 30,34
#  Signal: 37 (GPI26) zweiter von unten links
#  5V: 2 oder 4


import RPi.GPIO as GPIO
import time

servoGPIO = 26 # GPIO 26 ist Pin 37 (links unten)
#servoGPIO = 17 # GPIO 17 ist Pin 11 (links 6 von oben)
GPIO.setmode(GPIO.BCM)
GPIO.setup(servoGPIO, GPIO.OUT)

p = GPIO.PWM(servoGPIO, 500) # GPIO als PWM mit 500Hz

p.start(2.5) # Initialisierung

# 50% ist also 50:50 genau "mitte"
p.ChangeDutyCycle(50) 

time.sleep(0.5)

p.stop()

GPIO.cleanup()
