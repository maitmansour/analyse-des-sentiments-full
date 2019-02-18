<?php

// Read Files
$old_tweets = file_get_contents("../dataset/step4-already-annoted-data.csv", 'r');
$new_tweets = file_get_contents("../dataset/step2-auto-annoted-data.csv", 'r');

// Merge Tweets
$full_train_data_with_number=mergeTweetsAndAddNumbers($old_tweets,$new_tweets);

// Write merged File
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/clean/dataset/step5-final-train-data.csv",$full_train_data_with_number);





function mergeTweetsAndAddNumbers($old_tweets,$new_tweets)
{
	$full_data=[];

	$old_tweets_array=explode("\n", $old_tweets);
	$new_tweets_array=explode("\n", $new_tweets);
	$merged_arrays=array_merge($old_tweets_array,$new_tweets_array);

	$tmp_contents="";
	foreach ($merged_arrays as $keyFD => $valueFD) {
		$tmp_contents.=($keyFD+1)."\t".$valueFD."\n";
	}

	return $tmp_contents;
}