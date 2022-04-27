#!/usr/bin/php

<?php

/* EggBot HPGL - Parser
 
 by Heiko Kretschmer (usenet@wurst-wasser.net)
 
 This software is published under GPL.
  
 The original can be found here: https://www.evilmadscientist.com/
  
 What is HPGL? Read this: https://docs.fileformat.com/cad/hpgl/
 
 
ToDo
----
* Clear out the sign-mess. I totally screwed up the directions. For the next time: origin (0/0) is in the upper left corner! It is not! Frick. Inkscape (and others?) define 0/0 at bottom left corner!
* Take over the world, pinky!
* Refactoring - Big time!
* Translate everything in python
* Make it independent from usual 1016x1016 (dpi) resolution--DONE (it's stupid anyway since you loose precision in the fractions since the resolution of the stepper is so much worse....)
* Make it return to middle after drawing--DONE
*  
*/

echo("Attention - Before running, make sure the pen is in the middle of the egg!!!111oneeleven\n");
echo("\n");
echo("Warning - this is a very basic HPGL parser, it might won't cope with all path/pen-commands!\n");

#define( 'STDIN', fopen( 'php://stdin', 'r' ));

/* Globals */
$gDryRun=0;
//$gDryRun=1;
$gWaitForKey=0;
$gYOffset=10; // make it move a bit "upwards"


/* Get Arguments or use hardecoded path to HPGL-file */
if ($argc==2)
{
	echo("Reading HPGL-file \"$argv[1]\"...\n");
	$gHpglString=file_get_contents($argv[1]);
}
else
{
	#$gHpglString=file_get_contents("_Drawings/Inkscape/Mm 125x25Path.hpgl"); # Great!
	#$gHpglString=file_get_contents("_Drawings/Inkscape/Strandbar 125x25.hpgl"); # :-D
	$gHpglString=file_get_contents("_Drawings/Inkscape/Frohe Ostern 125x25.hpgl"); # :-D
}
//exit(1);

/* check read HPGL */
if (strlen($gHpglString)<42) 
{
	echo("Oh my. Infile is not okay. Exiting...\n");
	return(1);
}

$gHomingDone=0;
$gOneRotationSteps=500;
$gEggDiameter=40; // mm
$gEggCircumference=2*3.1415*$gEggDiameter/2; //mm
//$gEggCanvasHeight=15; // mm
#$gEggCanvasHeight=30; // mm ZU GROSS
$gEggCanvasHeight=25; // mm 
$gEggCanvasHeightDegrees=360/$gEggCircumference*$gEggCanvasHeight; 
$gEggCanvasHeightSteps=$gOneRotationSteps/360*$gEggCanvasHeightDegrees; // The area between the poles that will be drawn at. Depends very much on clamp-design, egg size and whatnot.
$gEggCanvasDPI=intval($gEggCanvasHeightSteps/$gEggCanvasHeight*25.4);
echo("IMPORTANT - Canvas width (X) of your drawing should match circumference: $gEggCircumference mm\n");
echo("IMPORTANT - Canvas height (Y) of your drawing should match printable area: $gEggCanvasHeight mm\n");
echo("IMPORTANT - Canvas resolution (dpi) of your drawing should match: $gEggCanvasDPI dpi\n");
if (0)
{
	$gHpglDpiX=1016;
	$gHpglDpiY=1016;
}
else
{
	$gHpglDpiX=$gEggCanvasDPI;
	$gHpglDpiY=$gEggCanvasDPI;
}
$gCurrentPositionX=0;
$gCurrentPositionY=0;
$gMinStepsPerMove=3; // only used with custom DPI
//$gStepsPer
#exit();

/* Explode commands */
$hpglCommandsArray=explode(";", $gHpglString);
print_r($hpglCommandsArray);

echo("\n");
$hpglLastCommand="";
$hpglCommandID=0;
foreach($hpglCommandsArray as $hpglCommand)
{
	echo("Command $hpglCommandID: " . substr($hpglCommand, 0, 7) . "...\n");
	
	switch(substr($hpglCommand, 0, 2))
	{
		case "PU":
					penUp(substr($hpglCommand, 2));
		break;

		case "PD":
					penDown(substr($hpglCommand, 2), $hpglLastCommand);
		break;
		
		case "SP":
					selectPen(substr($hpglCommand, 2));
		break;
		
		default:
					echo("Ignoring command: \"$hpglCommand\"\n");
		break;
	} // switch
	echo("--\n");
	
	$hpglCommandID++;
	$hpglLastCommand=$hpglCommand;
}


/* Return to Middle */
if (42==23)
{
	$returnMoveY= ( ($gEggCanvasHeight/2)/25.4 * $gHpglDpiY ) -
					$gCurrentPositionY;
	penUp("0,$returnMoveY");
	#movePen(0, $returnMoveY);
}

/* Disable Steppers */
if (!$gDryRun) system("python Disable.py");


/* HERE BE DRAGONS */
function penDown($aHpglCommand, $aHpglLastCommand)
{
	global $gDryRun;

	echo("Pen down, move to: $aHpglCommand\n");
	$lCoordinates=explode(",", $aHpglCommand);

	# Skip Wiping
        # PU0,0;
        # PD0,90;	
	if ($aHpglCommand=="0,90" && $aHpglLastCommand=="PU0,0" || 
		$aHpglCommand=="0,9" && $aHpglLastCommand=="PU0,0")
	{ 
		echo("Skipping wipe.\n");
		return;
	}

	# PEN DOWN
	if (!$gDryRun) system("python penDown.py");
	
	/* Now, move the pen */
	echo("Coordinate-pairs: " . count($lCoordinates) . "\n");
	for ($lCoordID=0;$lCoordID<count($lCoordinates);$lCoordID=$lCoordID+2)
	{
		movePen($lCoordinates[$lCoordID], $lCoordinates[$lCoordID+1]);
	}
}

function penUp($aHpglCommand)
{
	global $gDryRun;
	
	# Pen Up
	if (!$gDryRun) system("python penUp.py");

	if (strlen($aHpglCommand))
	{
		echo("Pen up, move to: $aHpglCommand\n");
		$lCoordinates=explode(",", $aHpglCommand);
	
		/* Now, move the pen */
		movePen($lCoordinates[0], $lCoordinates[1]);
	}
	else
	{
		echo("Pen up, no move.\n");
	}
}

function selectPen($aHpglCommand)
{
	echo("Select pen: $aHpglCommand\n");
	echo("Fortunately, we only have one pen. :)\n");
}

function movePen($aX, $aY)
{
	global $gCurrentPositionX, $gCurrentPositionY, $gHpglDpiX, $gHpglDpiY, $gOneRotationSteps, $gEggCircumference, $gDryRun, $gEggCanvasHeightSteps, $gMinStepsPerMove, $gWaitForKey, $gHomingDone, $gYOffset;
	
	echo("--\n");
	echo("Current position: ($gCurrentPositionX/$gCurrentPositionY)pt\n");
	echo("Moving pen to: ($aX/$aY)pt\n");
	$lStepsX=0;
	$lStepsY=0;
	
	/* Move to the origin */
	if ($aX==0 && $aY==0 && $gHomingDone==0) // at launch
	{
		/* Move to the origin - ASSUMING THE PEN IS IN THE MIDDLE RIGHT NOW! <------------- IMPORTANT */
		/* Also assuming the egg is a perfect sphere, and the circumference is identical on the X- and Y-axis. (Yeah, yeah, this will be remedied...) 
		We ALSO assume, that you put Y in the middle postion before launch! */
		
		/* Set Y-Position */
		echo("Moving to origin (bottom left), assuming the pen was in the middle of Y.\n");
		$lStepsX=0; /* We don't have to move on X-Axis…wherever we are is the origin */
		$lStepsY=intval(-$gEggCanvasHeightSteps/2+$gYOffset); // half the height, offset
#		$lStepsY=$gEggCanvasHeightSteps/2; // half the height

		$gHomingDone=1; // so the homing will only be manipulated on launch. Not at End.
	}
	else if ($aX==0 && $aY==0 && $gHomingDone==1) // at finish
	{ 	// don't return to origin, but to middle
		echo("Moving to the middle of Y.\n");
		movePen(0, intval($gEggCanvasHeightSteps/2+$gYOffset));
		return;
		//$lStepsX=$lStepsY=0;
	}
	else /* Relative move from last position */
	{
		/* Calculate Difference */
		$lStepsX=$aX-$gCurrentPositionX;
		$lStepsY=$aY-$gCurrentPositionY;
		echo("Moving pen this much relative to current position: ($lStepsX/$lStepsY)pt\n");
				
	}
	$lStepsX=intval($lStepsX);
	$lStepsY=intval($lStepsY);
	echo("Moving by steps: ($lStepsX/$lStepsY)steps\n");
	
	/* MOVE PEN! (by steps) */
	if (!$gDryRun)
	{
		/* Wait for key */
		if ($gWaitForKey==1)
		{
			$key = prompt( 'Please press return to continue...' );
		}
		
		/* Now actually move */
		system("python moveBy.py $lStepsX $lStepsY");
	}

	/* Save current position */
	$gCurrentPositionX=$aX;
	$gCurrentPositionY=$aY;

} // movePen


function movePenCustomDPI($aX, $aY)
{
	global $gCurrentPositionX, $gCurrentPositionY, $gHpglDpiX, $gHpglDpiY, $gOneRotationSteps, $gEggCircumference, $gDryRun, $gEggCanvasHeightSteps, $gMinStepsPerMove;
	
	echo("--\n");
	echo("Current position: ($gCurrentPositionX/$gCurrentPositionY)pt\n");
	echo("Moving pen to: ($aX/$aY)pt\n");
	$lStepsX=0;
	$lStepsY=0;
	
	/* Move to the origin */
	if ($aX==0 && $aY==0)
	{
		/* Move to the upper left corner - ASSUMING THE PEN IS IN THE MIDDLE RIGHT NOW! <------------- IMPORTANT */
		/* Also assuming the egg is a perfect sphere, and the circumference is identical on the X- and Y-axis. (Yeah, yeah, this will be remedied...) */
		
		/* Set Y-Position */
		$lStepsX=0; /* We don't have to move on X-Axis…wherever we are is the origin */
		$lStepsY=-$gEggCanvasHeightSteps/2; // half the height
	}
	else /* Relative move */
	{
		/* Calculate Difference */
		$lRelativeMoveX=$aX-$gCurrentPositionX;
		$lRelativeMoveY=$aY-$gCurrentPositionY;
		echo("Moving pen this much relative to current position: ($lRelativeMoveX/$lRelativeMoveY)pt\n");
		
		/* Calculate X-Steps (circumference of the egg) */
		if ($lRelativeMoveX)
		{
			$lRelativeMoveXMm=$lRelativeMoveX/$gHpglDpiX*2.54*10; /* Calculate movement in mm */
			$lStepsX=intval($gOneRotationSteps / $gEggCircumference * $lRelativeMoveXMm);
		}
		
		/* Calculate Y-Steps */
		if ($lRelativeMoveY)
		{
			$lRelativeMoveYMm=$lRelativeMoveY/$gHpglDpiX*2.54*10; /* Calculate movement in mm */
			$lStepsY=intval($gOneRotationSteps / $gEggCircumference * $lRelativeMoveYMm);
		}
		
	}
	$lStepsX=intval($lStepsX);
	$lStepsY=intval($lStepsY);
	echo("Moving by steps: ($lStepsX/$lStepsY)steps\n");
	
	/* MOVE PEN! (by steps) */
		if ($lStepsX<$gMinStepsPerMove && $lStepsY<$gMinStepsPerMove)
		{
			echo("Skipping null-steps...\n");
		}
		else
		{
			if (!$gDryRun)
			{
				system("python moveBy.py $lStepsX $lStepsY");
			}

			/* Save current position */
			$gCurrentPositionX=$aX;
			$gCurrentPositionY=$aY;

			/* Wait for key */
			$key = prompt( 'Please press a return to continue' );
		}
	
	/* Save current position */
#	$gCurrentPositionX=$aX;
#	$gCurrentPositionY=$aY;
	
	
}

function prompt( $msg )
{
	echo $msg . "\n";
	#ob_flush();
	$in = trim( fgets( STDIN ));
	return $in;
}



#EOF
?>
