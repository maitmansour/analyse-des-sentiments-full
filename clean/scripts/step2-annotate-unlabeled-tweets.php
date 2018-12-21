<?php


// Get cleaned tweets
$tweets=getCleanedTweets("../dataset/clean-data.csv");

// Annotate tweets
$annoted_tweets_array = annotateTweets($tweets);

// Get Annotated tweets as text
$annoted_tweets_text = getAnnotatedTweetsAsText($annoted_tweets_array);

// Create annoted tweets file
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/clean/dataset/auto-annoted-data.csv",$annoted_tweets_text);



////////////////////// FUNCTIONS /////////////////////////////


// Get Tweets
 function getCleanedTweets($filename)
{
	$full_data=file_get_contents($filename);
	$tweets_array=explode("\n", $full_data);
	return $tweets_array;
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
		$mixte=$tweets['mixte'];
		$negatif=$tweets['negatif'];
		$positif=$tweets['positif'];
		$autre=$tweets['autre'];

		// Merge tweets by polarity
		$tweets_merged=array_merge($mixte,$negatif,$positif,$autre);

		// Randomize tweets
		shuffle($tweets_merged);

		// Convert array to text
		$text_data=implode("\n", $tweets_merged);

	return $text_data;

}



function checkExistanceBySimilarity($string,$word){
	$exploded_string=explode(" ", $string);
	$percent=0;
	foreach ($exploded_string as $key => $value) {
		similar_text($value, $word,$percent);
		if($percent>75)return true;
	}
	return false;
}

// Check if one words is on string (array) and check similarity
function strposa($string, $words=array(), $offset=0) {
        $chr = array();
        foreach($words as $word) {
                $res = checkExistanceBySimilarity($string,$word);
                if ($res !== false) $chr[$word] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
}


// Get polarity by Tweet
function getPolarityByTweet($string)
{
	$positif_words=[
"😂",
"💪", "💜",
"💖",
"👏",
"👍",
"bravo",
"courage",
"positif",
"amour",
"espoir",
"chance",
"belle",
"beau",
"🎺",
"😍",
"top",
"super",
"magnifique",
"excellent",
"heureusement",
"explique",
"arrogant",
"arrête",
"plat",
"cœur",
"caricatur",
"félicitation",
"intellectuelle",
"élection",
"incroyable",
"grâce",
"connait",
"côté",
"cesse",
"attaqué",
"résume",
"changement"];

	$negatif_words=["🚮",
"ridiculisation",
"😠",
"😳",
"🚫", 
"🔥",
"😢", 
"😱",
"😹",
"😠",
"😨",
"malade",
"bloodysusu",
"clown",
"honte",
"assume",
"taire",
"bizarre",
"jevoteelledegage",
"haine",
"perd",
"ToutSaufMacron",
"hontemarine",
"EnMarche",
"null",
"honteux",
"con",
"ivre",
"bu",
"😫",
"😭",
"échec",
"frapp",
"clash",
"invectiv",
"daesh",
"racist",
"cougar",
"travail",
"couille",
"démotivé",
"schlag",
"guerre",
"shlag",
"mensonge",
"discours",
"dupontaignan",
"bat",
"échec",
"médiocre"
];
	if ((strposa($string, $negatif_words, 1))&&(strposa($string, $positif_words, 1))) {
	    return 'mixte';
	} else if (strposa($string, $negatif_words, 1)){
	    return 'negatif';
	} else if (strposa($string, $positif_words, 1)){
	    return 'positif';
	}

	return 'autre';
}