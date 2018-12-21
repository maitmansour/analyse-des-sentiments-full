<?php


// Get TWeets by file
$tweets=getTweetsByFile("data/unlabeled.xml");

//Get Annoted tweets array
$annoted_tweets = annoterTweetsOld($tweets);

$min_number_of_tweets=getMinCountArrays($annoted_tweets);
// Get random tweets by polarity
$data=getRandomAnnotedTweets($annoted_tweets,$min_number_of_tweets,1);

// Create annoted files
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/prediction/data/task1-train.csv",$data);



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
	$positif_words=["😂","💪", "💜","💖","👏","👍","bravo","courage","positif","future","amour","espoir","chance","belle","🎺","😍","top","super","magnifique","ya",
"excellent",
"heureusement",
"expliquer",
"arrogante",
"ok",
"arrêter",
"plat",
"cœur",
"saint",
"show",
"caricature",
"félicitations",
"intellectuelle",
"perd",
"élections",
"fhaine",
"incroyable",
"grâce",
"connait",
"côté",
"venir",
"nuit",
"ns",
"petit",
"part",
"évidemment",
"assume",
"cesse",
"attaqué",
"résume",
"bloodysusu",
"représente",
"suffit",
"analyse",
"gouvernement",
"choisir",
"françois",
"2002",
"taire",
"changement",
"bizarre"];

	$negatif_words=["🚮","ridiculisation","😠","😳","🚫", "🔥","😢", "😱","😹","😠","😨","clown","honte","jevoteelledegage","FHaine","ToutSaufMacron","hontemarine","EnMarche","null","honteux","con","ivre","bu","😫","😭","échec","frapper","clash","invective","daesh","raciste","cougar","travail",
"couilles",
"schlag",
"totalement",
"convaincre",
"macronpresident",
"tt",
"guerre",
"justice",
"puisse",
"shlag",
"mensonge",
"discours",
"dupontaignan",
"vrmt",
"bat",
"échec",
"loin",
"paris",
"appelle",
"électorat",
"pouvoir",
"totale",
"système",
"20",
"preuve",
"connais",
"vois",
"sort",
"médiocre",
"débats",
"matin",
"genre",
"sauf",
"fut",
"fidèle",
"invite",
"mai",
"unis",
"esprit",
"retrouver"];
	$string=$tweet['message'];

	/*if ((strposa($string, $negatif_words, 1))&&(strposa($string, $positif_words, 1))) {
	    return 'mixte';
	} else */if (strposa($string, $negatif_words, 1)){
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
			array_push($data[$polarity], "\t".stringFormatter($value['message'])."\t".$polarity);
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

	$text_data="";
	foreach ($tweets_merged as $key => $value) {
		if ($counter_start==1) {
			$text_data.=$counter_start.$value;		
		}else{
			$text_data.="\n". $counter_start.$value;
		}
		$counter_start++;
	}

	return $text_data;
}



// Check if one words is on string (array)
function strposa($haystack, $needles=array(), $offset=0) {
        $chr = array();
        foreach($needles as $needle) {
                $res = strpos($haystack, $needle, $offset);
                if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
}

function getTweetIdByUrl($url)
{
	$arr = explode("/", $url);
	return end($arr);
}

function getMinCountArrays($arrays)
{
	$counts = array_map('count', $arrays);
	return min($counts);
}





function stringFormatter($string){
	//  STR TO LOWER
	$string= strtolower($string);

	// delete ponctuation
	$string = preg_replace('/[[:punct:]]/', ' ', $string);

	// Delete URL
	$string = preg_replace('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', ' ', $string);

	return $string;
}
