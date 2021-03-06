<?php


// Get TWeets by file
$tweets=getTweetsByFile("../dataset/step6-unformated-test-data.csv");

//Get only messages
$messages_array=getMessagesByTweets($tweets);


//clean messages
$clean_messages_text=cleanMessages($messages_array);


// Create annoted files tests
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/clean/dataset/step7-clean-test-data.csv",$clean_messages_text);



////////////////////// FUNCTIONS /////////////////////////////


// Get TWeets By file
 function getTweetsByFile($filename)
{
	$full_data=file_get_contents($filename);
	$tweets_array=explode("\n", $full_data);
	return $tweets_array;
}

// Get messages only by tweets
function getMessagesByTweets($tweets)
{
	$messages_array=[];
	foreach ($tweets as $key => $value) {
		array_push($messages_array, $value);	
		}	
	
	
	return $messages_array;
}


// Get messages only by tweets
function cleanMessages($messages_array)
{
	$clean_messages=[];
	foreach ($messages_array as $key => $value) {
			$one_clean_message=stringCleaner($value);
			array_push($clean_messages, $one_clean_message);
	}
	$clean_messages_text=implode("\n", $clean_messages);
	return $clean_messages_text;
}


// clean text
function stringCleaner($string){

	// Delete URLs
	$string = preg_replace('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', ' ', $string);

	// Delete ponctuation
	$string = preg_replace('/[[:punct:]]/', ' ', $string);
	$string=str_replace("…", "", $string);

	//delete numbers
	$string = preg_replace('/[0-9]+/', '', $string);


	//  String to lower
	$string= strtolower($string);

	// Delet articles
	$articles=[
" aux ",
" ce ",
" cet ",
" cette ",
" ces ",
" des ",
" du ",
" l ",
" la ",
" le ",
" les ",
" leur ",
" leurs ",
" là ",
" ma ",
" mes ",
" mon ",
" nos ",
" notre ",
" quel ",
" quelle ",
" quelles ",
" quelles. ",
" quels ",
" sa ",
" ses ",
" son ",
" ta ",
" tes ",
" ton ",
" un ",
" une ",
" vos ",
" votre ",
" je ",
" tu ",
" il ",
" elle ",
" nous ",
" vous ",
" ils ",
" elles ",
" et ",
" en ",
" es ",
" on ",
" se ",
" est ",
" à ",
" ç ",
" ç",
" é "];

// Remove one charcter
$string = preg_replace("@\b[a-z]{1}\b@m", " ", $string);
// Remove one charcter
$string = preg_replace("@\b[a-z][a-z]{1}\b@m", " ", $string);

// remove articles
$string=str_replace($articles," ", $string);

// delete multiple whitespaces
$string = preg_replace('/[\s]+/mu', ' ', $string);


// Remove bgining whitespaces
$string = ltrim($string);

	return $string;
}
