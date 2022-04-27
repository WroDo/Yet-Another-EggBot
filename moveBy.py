#!/usr/bin/python

# ToDo:
# -----
# * refactoring!


import RPi.GPIO as GPIO
import time
import sys

# Check argv
if (len(sys.argv)!=3):
        print("Usage: ", sys.argv[0], " XSTEPS YSTEPS")
        exit()

#print ('Argument List:', str(sys.argv))
stepsX=int(sys.argv[1])
stepsY=int(sys.argv[2])
#stepsY=0 # debug / testing
print ('Steps to do:', str(stepsX), '/', str(stepsY))
#delay = 5
delay = 5

# Init GPIO
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

coil_A_1_pin = 24 # pink
coil_A_2_pin = 4 # orange
coil_B_1_pin = 23 # blau
coil_B_2_pin = 25 # gelb

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

StepCount = 8
Seq = list(range(0, StepCount))
Seq[0] = [0,1,0,0]
Seq[1] = [0,1,0,1]
Seq[2] = [0,0,0,1]
Seq[3] = [1,0,0,1]
Seq[4] = [1,0,0,0]
Seq[5] = [1,0,1,0]
Seq[6] = [0,0,1,0]
Seq[7] = [0,1,1,0]

GPIO.setup(coil_A_1_pin, GPIO.OUT)
GPIO.setup(coil_A_2_pin, GPIO.OUT)
GPIO.setup(coil_B_1_pin, GPIO.OUT)
GPIO.setup(coil_B_2_pin, GPIO.OUT)

GPIO.setup(coil2_A_1_pin, GPIO.OUT)
GPIO.setup(coil2_A_2_pin, GPIO.OUT)
GPIO.setup(coil2_B_1_pin, GPIO.OUT)
GPIO.setup(coil2_B_2_pin, GPIO.OUT)

def setStepX(w1, w2, w3, w4):
    GPIO.output(coil_A_1_pin, w1)
    GPIO.output(coil_A_2_pin, w2)
    GPIO.output(coil_B_1_pin, w3)
    GPIO.output(coil_B_2_pin, w4)
    
def setStepY(w1, w2, w3, w4):
    GPIO.output(coil2_A_1_pin, w1)
    GPIO.output(coil2_A_2_pin, w2)
    GPIO.output(coil2_B_1_pin, w3)
    GPIO.output(coil2_B_2_pin, w4)
    
def forwardX(delay, steps):
    for i in range(steps):
        for j in range(StepCount):
            setStepX(Seq[j][0], Seq[j][1], Seq[j][2], Seq[j][3])
            time.sleep(delay)
    disable()
            
def backwardsX(delay, steps):
    for i in range(steps):
        for j in reversed(range(StepCount)):
            setStepX(Seq[j][0], Seq[j][1], Seq[j][2], Seq[j][3])
            time.sleep(delay)
    disable()

def forwardY(delay, steps):
    for i in range(steps):
        for j in range(StepCount):
            setStepY(Seq[j][0], Seq[j][1], Seq[j][2], Seq[j][3])
            time.sleep(delay)
    disable()
            
def backwardsY(delay, steps):
    for i in range(steps):
        for j in reversed(range(StepCount)):
            setStepY(Seq[j][0], Seq[j][1], Seq[j][2], Seq[j][3])
            time.sleep(delay)
    disable()


def disable():
     GPIO.output(coil_A_1_pin, 0)
     GPIO.output(coil2_A_1_pin, 0)
     GPIO.output(coil_A_2_pin, 0)
     GPIO.output(coil2_A_2_pin, 0)
     GPIO.output(coil_B_1_pin, 0)
     GPIO.output(coil2_B_1_pin, 0)
     GPIO.output(coil_B_2_pin, 0)
     GPIO.output(coil2_B_2_pin, 0)
 
# Reset coils, disable (stepper might be turned by hand)
disable()

#
# Simple Faelle zuerst: Nur X-Steps oder nur Y-Steps zu machen
#
if (stepsX != 0 and stepsY == 0):
    if stepsX > 0:
        print("Stepping X backwards")
        #forwardX(int(delay) / 1000.0, abs(int(stepsX)))
        backwardsX(int(delay) / 1000.0, abs(int(stepsX)))
    elif stepsX < 0:
        print("Stepping X forward")
        #backwardsX(int(delay) / 1000.0, abs(int(stepsX)))
        forwardX(int(delay) / 1000.0, abs(int(stepsX)))
elif (stepsY != 0 and stepsX == 0):
    if stepsY > 0:
        print("Stepping Y backwards")
        backwardsY(int(delay) / 1000.0, abs(int(stepsY)))
        #forwardY(int(delay) / 1000.0, abs(int(stepsY)))
    elif stepsY < 0:
        print("Stepping Y forward")
        forwardY(int(delay) / 1000.0, abs(int(stepsY)))
        #backwardsY(int(delay) / 1000.0, abs(int(stepsY)))
else:
        #
        # Now the tricky part: Move both axis simulteanously
        # I don't know if this is a good way to do it, but I guess for having an idea around 4 a.m. it's pretty good...
        print("Diagonal stepping...")
        ultraSteps=abs(int(stepsX)) * abs(int(stepsY))
        stepCountX=0
        stepCountY=0
        for ultraStep in range(0, ultraSteps):
            # First X-Axis
            if (ultraStep % stepsY) == 0:
                # Get 1 or -1...(awkward method, aye?)
                stepCountX=stepCountX+1
                thisStep=int(stepsX/abs(stepsX))
                if (thisStep > 0):
                    #print("Stepping X forward")
                    #forwardX(int(delay) / 1000.0, 1)
                    backwardsX(int(delay) / 1000.0, 1)
                elif (thisStep < 0):
                    #print("Stepping X backwards")
                    forwardX(int(delay) / 1000.0, 1)
                    #backwardsX(int(delay) / 1000.0, 1)
            # Second Y-Axis
            if (ultraStep % stepsX) == 0:
                # Get 1 or -1...(awkward method, aye?)
                stepCountY=stepCountY+1
                thisStep=int(stepsY/abs(stepsY))
                if (thisStep < 0):
                    #print("Stepping Y forward")
                    forwardY(int(delay) / 1000.0, 1)
                    #backwardsY(int(delay) / 1000.0, 1)
                elif (thisStep > 0):
                    #print("Stepping Y backwards")
                    backwardsY(int(delay) / 1000.0, 1)
                    #forwardY(int(delay) / 1000.0, 1)
        print("stepCountX: ", stepCountX, ", stepCountY: ", stepCountY)
            
    



# 20 langsame Schritte vorwaerts
if 1 == 0:
    delay = 20
    steps = 20
    forward(int(delay) / 1000.0, int(steps))

# 200 schnelle Schritte Ruueckwaerts
if 1 == 0:
    delay = 1
    steps = 200
    backwards(int(delay) / 1000.0, int(steps))

# 1 complete rotation
if 1 == 0:
    delay = 2
    #steps = 512
    steps = 500
    forward(int(delay) / 1000.0, int(steps))


# Reset coils, disable (stepper might be turned by hand)
disable()


# EOF
