<?php


// Get TWeets by file
$tweets=getTweetsByFile("../dataset/step0-labeled.csv");

//Get only messages
$messages_array=getMessagesByTweets($tweets);


//clean messages
$clean_messages_text=cleanMessages($messages_array);


// Create annoted files
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/clean/dataset/step3-clean-labeled-data.csv",$clean_messages_text);



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
		$message=explode("\t", $value);
			array_push($messages_array, $message[1]);
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
	// remove null values
	$clean_messages= array_filter($clean_messages); 

	// delete tweets with less than two words
	$clean_messages= array_filter($clean_messages, 
		function($v, $k) {
    return str_word_count($v)>2;
			}, 
	ARRAY_FILTER_USE_BOTH);

	// delete duplicates
	$clean_messages=array_unique($clean_messages);
	
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

	// Delete accents
	$string=normalize($string);
	// Delet articles
	$articles=[
" aux ",
" cet ",
" cette ",
" ces ",
" des ",
" les ",
" leur ",
" leurs ",
" mes ",
" mon ",
" nos ",
" notre ",
" quel ",
" quelle ",
" quelles ",
" quelles. ",
" quels ",
" ses ",
" son ",
" tes ",
" ton ",
" une ",
" vos ",
" votre ",
" elle ",
" nous ",
" vous ",
" ils ",
" elles ",
" est "];

// Remove one charcter
$string = preg_replace("@\b[a-z]{1,2}\b@m", " ", $string);

// delete multiple whitespaces
$string = preg_replace('/[\s]+/mu', ' ', $string);

#$string=spellCheck($string);


// remove articles
$string=str_replace($articles,"", $string);

// Remove bgining whitespaces
$string = ltrim($string);

	return $string;
}


function normalize ($string) {
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj', 'd'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'C'=>'C', 'c'=>'c', 'C'=>'C', 'c'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'R'=>'R', 'r'=>'r',
    );
   
    return strtr($string, $table);
}

function spellCheck($string)
{
	$words=explode(" ", $string);
	$pspell_link = pspell_new ("fr");
	pspell_add_to_session($pspell_link,"Macron");
	pspell_add_to_session($pspell_link,"jevoteelledegage");
	pspell_add_to_session($pspell_link,"ToutSaufMacron");
	pspell_add_to_session($pspell_link,"hontemarine");
	pspell_add_to_session($pspell_link,"EnMarche");
	pspell_add_to_session($pspell_link,"pen");
	pspell_add_to_session($pspell_link,"daech");

	foreach ($words as $key => $word) {
		if (!pspell_check($pspell_link, $word)) {
		     unset($words[$key]);
		}
	}

	return implode(" ", $words);

}