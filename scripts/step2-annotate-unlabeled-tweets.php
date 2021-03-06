<?php

$data_scores=file_get_contents("../result/neg.txt");
$data_scores=explode("\n", $data_scores);

$data_text=[];
foreach ($data_scores as $key => $value) {
	$tmp=explode("\t", $value);
	$data_text[$tmp[0]]=intval($tmp[1]);
}

// Get cleaned tweets
$tweets=getCleanedTweets("../dataset/step1-clean-unlabeled-data.csv");

// Annotate tweets
$annoted_tweets_array = annotateTweets($tweets,$data_text);

//  Get min number of tweets
$min_number_of_tweets=getMinCountArrays($annoted_tweets_array);

// Get random tweets by polarity
$annoted_tweets_text=getRandomAnnotedTweets($annoted_tweets_array,0/*$min_number_of_tweets*/,1);

// Get Annotated tweets as text
//$annoted_tweets_text = getAnnotatedTweetsAsText($annoted_tweets_array);

// Create annoted tweets file
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/clean/dataset/step2-auto-annoted-data.csv",$annoted_tweets_text);



////////////////////// FUNCTIONS /////////////////////////////


// Get Tweets
 function getCleanedTweets($filename)
{
	$full_data=file_get_contents($filename);
	$tweets_array=explode("\n", $full_data);
	return $tweets_array;
}


// Annotate Tweets
function annotateTweets($tweets,$data_text)
{
	$data=[
		"mixte"=>[],
		"negatif"=>[],
		"positif"=>[],
		"autre"=>[],
	];
	foreach ($tweets as $key => $value) {
		if ($polarity=getPolarityByTweet($value,$data_text)) {
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

// Get random tweets
function getRandomAnnotedTweets($tweets,$nboftts=1,$counter_start=1)
{
	if ($nboftts==0) {
		$mixte=$tweets['mixte'];
		$negatif=$tweets['negatif'];
		$positif=$tweets['positif'];
		$autre=$tweets['autre'];
	}else{

	// Get random first N Tweets
	shuffle($tweets['mixte']);
	$mixte = array_slice($tweets['mixte'], 0,$nboftts);
	
	shuffle($tweets['negatif']);
	$negatif = array_slice($tweets['negatif'], 0,$nboftts);
	
	shuffle($tweets['positif']);
	$positif = array_slice($tweets['positif'], 0,$nboftts);
	
	shuffle($tweets['autre']);
	$autre = array_slice($tweets['autre'], 0,$nboftts);

	}

	// Merge tweets by polarity
	$tweets_merged=array_merge($mixte,$negatif,$positif,$autre);

	// Randomize tweets
	shuffle($tweets_merged);
	$text_data=implode("\n", $tweets_merged);
	return $text_data;
}


function checkExistanceBySimilarity($string,$word){
	$exploded_string=explode(" ", $string);
	$percent=0;
	foreach ($exploded_string as $key => $value) {
		similar_text($value, $word,$percent);
		if($percent>77)return true;
	}
	return false;
}

// Check if one words is on string (array) and check similarity
function strposa($string, $words=array(), $offset=0) {
        $chr = array();
        //check by simlarity
        foreach($words as $word) {
                $res = checkExistanceBySimilarity($string,$word);
                if ($res !== false) $chr[$word] = $res;
        }
        // check exist
        /*foreach($words as $word) {
                $res = strpos($string, $word, $offset);
                if ($res !== false) $chr[$word] = $res;
        }*/
        if(empty($chr)) return false;
        return min($chr);
}


// Get polarity by Tweet
function getPolarityByTweet($string,$data_text)
{
	$positif_words=[
"😂",
"💪", 
"💜",
"💖",
"👏",
"👍",
"🎺",
"😍",
"bravo",
"courage",
"positif",
"amour",
"espoir",
"chance",
"belle",
"beau",
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
"attaqu",
"résume",
"changement",
"hauteur",
"confiance",
"aimer",
"joie",
"merci",
"calme",
"adore",
"felicitations",
"intelligence",
"credible",
"prouve",
"responsable",
"respecter",
];

	$negatif_words=[
"🚮",
"😠",
"😳",
"🚫", 
"🔥",
"😢", 
"😱",
"😹",
"😠",
"😨",
"😫",
"😭",
"malade",
"bloodysusu",
"honte",
"suicide",
"autosuicide",
"suicidaire",
"suicidemediatique",
"suicidepolitique",
"assume",
"jevoteelledegage",
"haine",
"ToutSaufMacron",
"hontemarine",
"null",
"con",
"ivre",
"échec",
"frapp",
"clash",
"invectiv",
"racist",
"cougar",
"couille",
"schlag",
"shlag",
"mensonge",
"bat",
"médiocre",
"poudre",
"cour",
"cons",
"nul",
"diocre",
"flavienneuvy",
"archi",
"damidotvalerie",
"hop",
"grosse",
"rigol",
"idio",
"humilier",
"pute",
"putain",
"encule",
"connard",
"salope",
"merde",
"cul",
"batard",
"connasse",
"enfoire",
"abruti",
"caca",
"bordel",
"peur",
"mort",
"salir",
"voile",
"menace",
"triste",
"contre",
"folle",
"indigne",
"parasite",
"gueule",
"pathetique",
"crise",
"insulter",
"menteuse",
"ridiculisee",
"lecon",
"abstentionnistes",
"debile",
"pleurer",
"couilles",
"ptdr",
"marre",
"connerie",
"vulgarite",
"agressivite",
"genante",
"ment",
"betises",
"sdementhon",
"reconciliee",
"menteur",
"danger",
"fhollande",
"fhaine",
"shutthefnckup",
];

$mixte_words=[
"solere",
"ecologie",
"franceinsoumise",
"neant",
"casse",
"mdrrrr",
"malaise",
"vulgaire",
"ecu",
"blonde",
"mdr",
"ridiculisation",
"clown",
"taire",
"bizarre",
"perd",
"EnMarche",
"honteux",
"daesh",
"démotivé",
"guerre",
"dupontaignan",
"francoisfillon",
"fake",
"ridiculis",
"pas",
"mauvais",
"insupportable",
"immigration",
"interrompre",
"fini",
"regret",
"macron",
"lepen",
"pen",
"islamis",
"djihadis"
];

//echo $string."     ". getStringScore($string,$data_text); die;
$score=getStringScore($string,$data_text);
	if ($score==0) {
			if ((!strposa($string, $mixte_words, 1))&&(!strposa($string, $negatif_words, 1))&&(!strposa($string, $positif_words, 1))){
	    return 'autre';
	}else{
	    return 'negatif';
	}

	}
	if ($score<-1){
	    return 'negatif';
	} else if ($score>=-1 && $score<1){
	    return 'autre';
	} else if ($score>=1&& $score<5){
	    return 'mixte';
	} else if ($score>=5){
	    return 'positif';
	}

}

function getMinCountArrays($arrays)
{
	$counts = array_map('count', $arrays);
	return min($counts);
}


function getStringScore($string,$data_text)
{
	$words=explode(" ", $string);
	$scores_words=array_keys($data_text);
	$somme=0;
	//$tmp_score=0;
	foreach ($words as $key => $word) {
		if (array_key_exists($word,$data_text)) {
			$tmp_score=$data_text[$word];
			$somme+=$tmp_score;
		}

		/*if ($tmp_score==0) {
			foreach ($scores_words as $key => $value) {
					similar_text($value, $word,$percent);
					if($percent>60){
						$tmp_score=$data_text[$scores_words[$key]];
						$somme+=$tmp_score;
						break;
					}
			}
	}*/

	}

	return $somme;
}