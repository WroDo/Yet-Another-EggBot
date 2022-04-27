#!/usr/bin/php

<?php

/* EggBot SVG - Parser
 
 by Heiko Kretschmer (usenet@wurst-wasser.net)
 
 This software is published under GPL.
  
  
 What is SVG? Read this: https://www.w3.org/TR/SVG2/struct.html
 
 
ToDo
----
* recurse thru groups (<g>)
* implement all primitives (cubes, circles…)
 
*/

echo("Warning - this is a very basic SVG parser, it won't cope with all path-commands (https://developer.mozilla.org/en-US/docs/Web/SVG/Attribute/d#path_commands)!\n");


$xmlString=file_get_contents("_Drawings/Omnigraffle/Rectangles/Rectangles_Human_Readable.svg");

$xml=simplexml_load_string($xmlString) or die("Error: Cannot create object");
echo("---->8-----\n");
print_r($xml);
echo("----8<-----\n");


echo("Getting viewbox of canvas…\n");
#print_r($xml->attributes()['viewBox']);
$viewBoxElements=explode(" ", $xml->attributes()['viewBox']);
$gViewBoxX1=$viewBoxElements[0];
$gViewBoxY1=$viewBoxElements[1];
$gViewBoxX2=$viewBoxElements[2];
$gViewBoxY2=$viewBoxElements[3];
echo("ViewBox: ($gViewBoxX1/$gViewBoxY1) - ($gViewBoxX2/$gViewBoxY2)\n");
echo("\n");

#echo("Get metadata…\n");
#print_r($xml->metadata);
#echo("\n");

echo("Getting title of top group…\n"); // https://developer.mozilla.org/en-US/docs/Web/SVG/Element/g
#print_r($xml->g->title);
print($xml->g->title);
echo("\n");

echo("Getting next layer…\n");
print($xml->g->g->title);
echo("\n");

echo("Getting paths…\n"); // https://developer.mozilla.org/en-US/docs/Web/SVG/Element/path
$pathCount=count($xml->g->g->path);
for ($pathID=0;$pathID<$pathCount;$pathID++)
//foreach($xml->g->g->path as $onePath);
{
    /*echo("Path #$pathID:\n");
    print_r($xml->g->g->path[$pathID]); /**/
    
    /* Now examine this path */
    $onePathD=$xml->g->g->path[$pathID]['d'];   // https://developer.mozilla.org/en-US/docs/Web/SVG/Attribute/d
    /* https://developer.mozilla.org/en-US/docs/Web/SVG/Attribute/d#path_commands :
MoveTo: M, m
LineTo: L, l, H, h, V, v
Cubic Bézier Curve: C, c, S, s
Quadratic Bézier Curve: Q, q, T, t
Elliptical Arc Curve: A, a
ClosePath: Z, z
    */
    echo("Path #$pathID" . '[d]: ' . "$onePathD\n");
    
    /* Walk thru path-commands */
    #TODO at leasT M, L, C, Z
    
}
echo("\n");



if (0)
{
    echo("--Sandbox:\n");
    print(count($xml->g->g->path) . "\n");
    print_r($xml->g->g->path[2]);
    echo("--Sandbox\n");
}


/* HERE BE DRAGONS */

/*$xmlArray=xmlObjToArr($xml);
echo("---->8-----\n");
print_r($xmlArray);
echo("----8<-----\n"); */


// from https://www.php.net/manual/en/book.simplexml.php
function xmlObjToArr($obj)
{ 
        $namespace = $obj->getDocNamespaces(true); 
        $namespace[NULL] = NULL; 
        
        $children = array(); 
        $attributes = array(); 
        $name = strtolower((string)$obj->getName()); 
        
        $text = trim((string)$obj); 
        if( strlen($text) <= 0 ) { 
            $text = NULL; 
        } 
        
        // get info for all namespaces 
        if(is_object($obj)) { 
            foreach( $namespace as $ns=>$nsUrl ) { 
                // atributes 
                $objAttributes = $obj->attributes($ns, true); 
                foreach( $objAttributes as $attributeName => $attributeValue ) { 
                    $attribName = strtolower(trim((string)$attributeName)); 
                    $attribVal = trim((string)$attributeValue); 
                    if (!empty($ns)) { 
                        $attribName = $ns . ':' . $attribName; 
                    } 
                    $attributes[$attribName] = $attribVal; 
                } 
                
                // children 
                $objChildren = $obj->children($ns, true); 
                foreach( $objChildren as $childName=>$child ) { 
                    $childName = strtolower((string)$childName); 
                    if( !empty($ns) ) { 
                        $childName = $ns.':'.$childName; 
                    } 
                    $children[$childName][] = xmlObjToArr($child); 
                } 
            } 
        } 
        
        return array( 
            'name'=>$name, 
            'text'=>$text, 
            'attributes'=>$attributes, 
            'children'=>$children 
        ); 
    } 


#EOF
?>
