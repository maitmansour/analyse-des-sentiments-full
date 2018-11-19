<?php

// Get TWeets
$tweets=getTweetsByFile("data/unlabeled.xml");

//Get Json By Tweets
$json_data=getJsonByTweets($tweets);

//Write JSON FILE
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/data/unlabeled.json",$json_data);




////////////////////// FUNCTIONS /////////////////////////////


// Get TWeets
 function getTweetsByFile($filename)
{
	$xml=file_get_contents($filename);
	//$json=utf8_encode($xml);
	$xml_string=simplexml_load_string($xml);
	$json = json_encode((array)$xml_string);
	$array = json_decode($json,TRUE);
	$tweets=$array['tweet'];
	return $tweets;
}

 function getJsonByTweets($tweets)
{
	$data="";
	foreach ($tweets as $key => $value) {

	$index_table=array (
	  'index' => 
	  array (
	    '_index' => 'analyse-des-sentiments',
	    '_type' => 'tweet',
	    '_id' => ($key+1),
	  ),
	);

	$tweet_table=array (
	  'date' => $value['date'],
	  'favoris' => $value['favoris'],
	  'message' => $value['message'],
	  'retweet' => $value['retweet'],
	  'username' => $value['username'],
	  'hashtags' => getHashTags( $value['message']),
	);

	/*if ($key!=0) {
		$data.="\n".json_encode($index_table)."\n";
	}else{
		$data.=json_encode($index_table)."\n";
	}*/
		$data.=json_encode($index_table)."\n";

	$data.= json_encode($tweet_table)."\n";

	}

	return $data;
}

// Get Hashtags on String
function getHashTags($str)
{
	preg_match_all('/#([^\s]+)/', $str, $matches);
	$matches=$matches[1];
	$result=[];
	if (isset($matches)) {
	foreach ($matches as $key => $matche) {
	    $result[$key]="#".$matches[$key];
	   // $result[$key]=$tmp;
		//unset(var)
	}
	}

	return $result;
}
