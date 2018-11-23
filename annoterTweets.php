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

// Get polarity by Tweet
function getPolarityByTweet($tweet)
{
	$negatif_words=["🎺", "🔥","😢", "😱","😹","😠","😨","clown","#honte","#jevoteelledegage","#FHaine","#ToutSaufMacron","#hontemarine","#EnMarche","null","honteux","con","ivre","bu","😫","😭","échec","frapper","clash","invective"];
	$positif_words=["😂","💪", "💜","💖","👏","👍","bravo","courage","positif","future","amour","espoir","chance","belle"];
	$string=$tweet['message'];

	if ((strposa($string, $negatif_words, 1))&&(strposa($string, $positif_words, 1))) {
	    return 'mixte';
	} else if (strposa($string, $negatif_words, 1)){
	    return 'negatif';
	} else if (strposa($string, $positif_words, 1)){
	    return 'positif';
	}

	return 'autre';
}


// Annoter les tweets 1
function annoterTweetsOld($tweets)
{
	$data=[
		"mixte"=>[],
		"negatif"=>[],
		"positif"=>[],
		"autre"=>[],
	];
	foreach ($tweets as $key => $value) {
		if ($polarity=getPolarityByTweet($value)) {
			array_push($data[$polarity], "	".$value['message']."	".$polarity);
		}
	}
	// supprimer la redandance des infos
	return array_map("unserialize", array_unique(array_map("serialize", $data)));
}

// Annoter les tweets 2
function annoterTweetsNew($tweets)
{
	$data=[
		"mixte"=>[],
		"negatif"=>[],
		"positif"=>[],
		"autre"=>[],
	];
	foreach ($tweets as $key => $value) {
		if ($polarity=getPolarityByTweet($value)) {
			array_push($data[$polarity], getTweetIdByUrl($value['url'])." ".$polarity);
		}
	}
	// supprimer la redandance des infos
	return array_map("unserialize", array_unique(array_map("serialize", $data)));
}