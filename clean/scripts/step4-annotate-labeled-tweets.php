<?php


// Get cleaned tweets
$tweets=getCleanedTweets("../dataset/clean-labeled-data.csv");

// Get cleaned tweets
$annotations=getCleanedTweetsAnnotation("../dataset/labeled.csv");

// Annotate tweets
$annoted_tweets_array = mergeTweetsAndAnnotation($tweets,$annotations);

// Get Annotated tweets as text
$annoted_tweets_text = getAnnotatedTweetsAsText($annoted_tweets_array);

// Create annoted tweets file
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/clean/dataset/already-annoted-data.csv",$annoted_tweets_text);



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
		$tmp_merge=$value."\t".$annotations[$key];
			array_push($merged_data, $tmp_merge);
	}
	return $merged_data;
}

// Annotate Tweets
function annotateTweets($tweets)
{
	$data=[
		"mixte"=>[],
		"negatif"=>[],
		"positif"=>[],
		"autre"=>[],
	];
	foreach ($tweets as $key => $value) {
		if ($polarity=getPolarityByTweet($value)) {
			array_push($data[$polarity], $value."\t".$polarity);
		}
	}
	// delete redendant infos
	return array_map("unserialize", array_unique(array_map("serialize", $data)));
}

// Get Annotated tweets as text
function getAnnotatedTweetsAsText($tweets)
{
		// Convert array to text
		$text_data=implode("\n", $tweets);

	return $text_data;

}