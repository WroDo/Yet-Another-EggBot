#!/usr/bin/python
#
# https://github.com/WroDo/Yet-Another-EggBot
#
# https://tutorials-raspberrypi.de/raspberry-pi-servo-motor-steuerung/  -> 50Hz, 5-15% Tastrate
# https://draeger-it.blog/raspberry-pi-tutorial-7-servomotor-steuern/ -> 50 Hz, 0 Grad am Servo 2.5 % Tastverhältnis (2.5% = 0.5/20*100), 90 Grad am Servo 7.5% Tastverhältnis (7.5% = 1.5/20*100), 180 Grad am Servo 12.5% Tastverhältnis (12.5% = 2.5/20*100)
#
# Sollte der Servo Motor etwas zittern, während er sich nicht bewegt, kannst du den Puls mit p.ChangeDutyCycle(0) ruhig stellen.

# Servo Anschluß:
#  GND: 39 oder 9, 25, 6, 14, 20, 30,34
#  Signal: 37 (GPI26) zweiter von unten links
#  5V: 2 oder 4
# ATTENTION: USE A 1K-OHM RESISTOR ON SIGNAL LINE!!!!!!!!!!!!oneoneeleven

# Includes
import RPi.GPIO as GPIO
import time
import sys

# Globals
gServoGPIO = 26 # GPIO 26 ist Pin 37 (links unten)
# Duty Cycle solle zwischen -10 und +10% Abweichung von 50% liegen
# 40 > 50 < 60
gPWMDutyCycleUp = 3.5; # 40-2 #40
gPWMDutyCycleDown = 8.0; # 58 #60
gDelay = 0.1
gWait = 0.5
gOpen = False
gPosition = 'undefined'
gPWM = None

def penUp():
	global gDelay, gOpen, gPWMDutyCycleUp, gPWMDutyCycleDown, gPWM, gPosition
	if gPosition != 'up':
		gPWM.ChangeDutyCycle(gPWMDutyCycleUp)
		time.sleep(gWait) # Wait for servo moving up
		gPosition = 'up'
		print("Moved up.")

def pwmClose():
	global gDelay, gOpen, gPWMDutyCycleUp, gPWMDutyCycleDown, gPWM, gPosition
	if gPosition != 'up':
		penUp()
	gPWM.stop()
	GPIO.cleanup()
	print("PWM shut down.")
	gOpen=False

# Read stdin
def readInput():
	global gDelay, gOpen, gPWMDutyCycleUp, gPWMDutyCycleDown, gPWM, gPosition
	for lLine in sys.stdin:
		if 'quit' == lLine.rstrip():
			if gOpen == True:
				pwmClose()
			break
		if 'open' == lLine.rstrip():
			if gOpen == False:
				#gServoGPIO = 17 # GPIO 17 ist Pin 11 (links 6 von oben)
				GPIO.setmode(GPIO.BCM)
				GPIO.setup(gServoGPIO, GPIO.OUT)
				#gPWM = GPIO.PWM(gServoGPIO, 500) # GPIO als PWM mit 500Hz
				gPWM = GPIO.PWM(gServoGPIO, 50) # GPIO als PWM mit 50Hz
				gPWM.start(gPWMDutyCycleUp) # Initialisierung
				print("PWM initiated in 'up' position.")
				gOpen=True
		if gOpen == True:
			if 'close' == lLine.rstrip():
				pwmClose()
			if 'up' == lLine.rstrip():
				penUp();
			if 'down' == lLine.rstrip():
				if gPosition != 'down':
					for i in range(int(gPWMDutyCycleUp*10), int(gPWMDutyCycleDown*10), 1):
						gPWM.ChangeDutyCycle(i/10)
						time.sleep(gDelay)
					gPosition = 'down'
					print("Moved down.")
	print("Bye!")
  
  
# Main
print("Use 'open', 'close', 'up', 'down', 'quit'.")
readInput()




# EOF
