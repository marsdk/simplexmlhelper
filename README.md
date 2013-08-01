SimpleXMLHelper class for PHP
=============================

SimpleXMLHelper

A helper class for converting SimpleXMLElement to an array structure. This also traverses
the namespace given and adds them to the array.

Usage
=====

// Require the helper file.
require_once("SimpleXMLHelper.class.php");

// Load the XML. 
$xml = simplexml_load_file('somefile.xml');

// Read the namespace from the XML file.
$namespaces = $xml->getNamespaces(TRUE);

// Convert XML to array.
$array_xml = SimpleXMLHelper::xmlarray($xml, $namespaces);