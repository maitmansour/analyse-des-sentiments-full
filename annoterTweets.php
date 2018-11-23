<?php


// Get TWeets by file
$tweets=getTweetsByFile("data/unlabeled.xml");



////////////////////// FUNCTIONS /////////////////////////////


// Get TWeets
 function getTweetsByFile($filename)
{
	$xml=file_get_contents($filename);
	$xml_string=simplexml_load_string($xml);
	$json = json_encode((array)$xml_string);
	$array = json_decode($json,TRUE);
	$tweets=$array['tweet'];
	return $tweets;
}
