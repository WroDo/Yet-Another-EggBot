# Yet-Another-EggBot
## The why of Fry
I always wanted to try this and I had long feared controlling a servo or stepper would be really hard - but actually it wasn't. :-D

I wanted KISS. I wanted to make my own approach using a Raspberry Pi. It's definitely not as sophisticated as existing solutions out there, but it's my own, pretty simple and I love it. I hope you enjoy (and modify) it!

# Print
All parts are printable without support. I used PETG, but I think any other material will do just fine.

# Additional parts
* a Raspberry Pi (I used a v3, but I guess any Pi will do)
* two steppers and drivers (I used Joy-IT article RB-Moto2, EAN #4250236810195 )
* one 9g servo (I used Tower Pro SG90, but anyone that size will do)
* some M3 screws (incl. setscrews), washers and nuts
* one M5 screw, washer and locknut
* a spring that will go over the M5 screw
* some rubber foam
* the software (see below)

# Assembly
## General
Nothing special, just assemble it as you see fit.
## Servo connection
I used this to connect the servo:
* Pin 02 (5V) for servo +
* Pin 37 (GPIO26) for servo S
* Pin 39 (GND) for servo ground

# Design your plot
I think InkScape is one of the best (and free) solutions for the job. I proceed as follows:
* create a new document
* set canvas size to 125x25mm (you can make it bigger, depending on your eggs and Y-range)
* draw something
* convert text to paths with Extensions->Text->Hershey Text...
* save a copy as HPGL. Make sure the resolution is around 100dpi (if unsure, make some dry runs of the script before actually drawing).

# Printing
## Preparation
* make sure the arm is centered, if not so, use f.e ./moveBy.py 0 5 to move the arm 5 points to the left
* make sure the arm is raised, if in doubt, use ./penUp.py
* insert your pen into the arm
* use something like ./parseHPGL.php _Drawings/Inkscape/Happy\ Easter\!\ 125x25.hpgl
* hit CTRL-C if anything goes wrong (and start over) :-)

# Known issues
* SVG-parser is far from usable
* HPGL-parser handles simple lines, but no text- or circle-commands. Nevertheless, it works just fine will all my designs. Also the code needs refactoring. :)
* the codemix of Python and PHP is annoying (and very slow)

# Resources
* Video showing it in action: https://youtu.be/9CK9rJAHFhk
* Download of the printable design: https://www.thingiverse.com/thing:5369927
* Download of the software: https://github.com/WroDo/Yet-Another-EggBot

# Thanks
* Thanks to Imko for providing the Y-arm (from his design "Okmi EggBot", Thingiverse-ID 3512980) that I remixed! 
* Thanks to Evil Mad Scientist for the "template": https://wiki.evilmadscientist.com/The_Original_Egg-Bot_Kit!
* Thanks to Evil Mad Scientist for the Extension: https://wiki.evilmadscientist.com/Hershey_Text!

#EOF
