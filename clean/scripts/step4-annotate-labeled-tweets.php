<?php

 
// Get cleaned tweets
$tweets=getCleanedTweets("../dataset/step3-clean-labeled-data.csv");

// Get cleaned tweets
$annotations=getCleanedTweetsAnnotation("../dataset/step0-labeled.csv");

// Annotate tweets
$annoted_tweets_array = mergeTweetsAndAnnotation($tweets,$annotations);

// Get Annotated tweets as text
$annoted_tweets_text = getAnnotatedTweetsAsText($annoted_tweets_array);

// Create annoted tweets file
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/clean/dataset/step4-already-annoted-data.csv",$annoted_tweets_text);



////////////////////// FUNCTIONS /////////////////////////////


// Get Tweets
 function getCleanedTweets($filename)
{
	$full_data=file_get_contents($filename);
	$tweets_array=explode("\n", $full_data);
	return $tweets_array;
}


// Get Tweets
 function getCleanedTweetsAnnotation($filename)
{
	$full_data=file_get_contents($filename);
	$tweets_array=explode("\n", $full_data);
	
	$annotation_array=[];
	foreach ($tweets_array as $key => $value) {
		$annotation=explode("\t", $value);
			array_push($annotation_array, $annotation[2]);
	}
	return $annotation_array;
}

function mergeTweetsAndAnnotation($tweets,$annotations)
{
	$merged_data=[];
	foreach ($tweets as $key => $value) {
		$tmp_merge=$value."\t".getCorrectAnnotation($annotations[$key]);
			array_push($merged_data, $tmp_merge);
	}
	return $merged_data;
}

// Get Annotated tweets as text
function getAnnotatedTweetsAsText($tweets)
{
		// Convert array to text
		$text_data=implode("\n", $tweets);

	return $text_data;

}

function getCorrectAnnotation($annotation){
	switch ($annotation) {
		case 'positive':
			return "positif";
			break;
		
		case 'negative':
			return "negatif";
			break;
		
		case 'mixed':
			return "mixte";
			break;
	}
			return "autre";
}