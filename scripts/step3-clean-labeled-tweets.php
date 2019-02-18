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

	// Delet articles
	$articles=getArticles();
#$string=preg_replace($articles, ' ', $string);


	// Delete accents
	#$string=stemmer($string);



	// Delete accents
#	$string=normalize($string);

// Remove one charcter
$string = preg_replace("@\b[a-z]{1,2}\b@m", " ", $string);

// delete multiple whitespaces
$string = preg_replace('/[\s]+/mu', ' ', $string);

#$string=spellCheck($string);


// remove articles
#$string=str_replace($articles," ", $string);
// Remove bgining whitespaces
$string = ltrim($string);

#$string=substrSentence($string);
	return $string;
}


function normalize ($string) {
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Ð'=>'D', 'd'=>'d', 'Ž'=>'Z', 'ž'=>'z', 'C'=>'C', 'c'=>'c', 'C'=>'C', 'c'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'S',
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
		}/*else{
		$words[$key]=substr($word, 0,5);
		}*/
	}

	return implode(" ", $words);

}


function substrSentence($string){
	$words=explode(" ", $string);
	foreach ($words as $key => $word) {
		$words[$key]=substr($word, 0,5);
	}
	return implode(" ", $words);
}

function stemmer($string)
{
	$words=explode(" ", $string);
	foreach ($words as $key => $word) {
		 $words[$key] = PorterStemmer::Stem($word);
	}
	return implode(" ", $words);

}


function getArticles()
{
return [
"/\ba\b/i",
"/\babord\b/i",
"/\babsolument\b/i",
"/\bafin\b/i",
"/\bah\b/i",
"/\bai\b/i",
"/\baie\b/i",
"/\baient\b/i",
"/\baies\b/i",
"/\bailleurs\b/i",
"/\bainsi\b/i",
"/\bait\b/i",
"/\ballaient\b/i",
"/\ballo\b/i",
"/\ballons\b/i",
"/\ballô\b/i",
"/\balors\b/i",
"/\banterieur\b/i",
"/\banterieure\b/i",
"/\banterieures\b/i",
"/\bapres\b/i",
"/\baprès\b/i",
"/\bas\b/i",
"/\bassez\b/i",
"/\battendu\b/i",
"/\bau\b/i",
"/\baucun\b/i",
"/\baucune\b/i",
"/\baucuns\b/i",
"/\baujourd\b/i",
"/\baujourd'hui\b/i",
"/\baupres\b/i",
"/\bauquel\b/i",
"/\baura\b/i",
"/\baurai\b/i",
"/\bauraient\b/i",
"/\baurais\b/i",
"/\baurait\b/i",
"/\bauras\b/i",
"/\baurez\b/i",
"/\bauriez\b/i",
"/\baurions\b/i",
"/\baurons\b/i",
"/\bauront\b/i",
"/\baussi\b/i",
"/\bautre\b/i",
"/\bautrefois\b/i",
"/\bautrement\b/i",
"/\bautres\b/i",
"/\bautrui\b/i",
"/\baux\b/i",
"/\bauxquelles\b/i",
"/\bauxquels\b/i",
"/\bavaient\b/i",
"/\bavais\b/i",
"/\bavait\b/i",
"/\bavant\b/i",
"/\bavec\b/i",
"/\bavez\b/i",
"/\baviez\b/i",
"/\bavions\b/i",
"/\bavoir\b/i",
"/\bavons\b/i",
"/\bayant\b/i",
"/\bayez\b/i",
"/\bayons\b/i",
"/\bb\b/i",
"/\bbah\b/i",
"/\bbas\b/i",
"/\bbasee\b/i",
"/\bbat\b/i",
"/\bbeau\b/i",
"/\bbeaucoup\b/i",
"/\bbien\b/i",
"/\bbigre\b/i",
"/\bbon\b/i",
"/\bboum\b/i",
"/\bbravo\b/i",
"/\bbrrr\b/i",
"/\bc\b/i",
"/\bcar\b/i",
"/\bce\b/i",
"/\bceci\b/i",
"/\bcela\b/i",
"/\bcelle\b/i",
"/\bcelle-ci\b/i",
"/\bcelle-là\b/i",
"/\bcelles\b/i",
"/\bcelles-ci\b/i",
"/\bcelles-là\b/i",
"/\bcelui\b/i",
"/\bcelui-ci\b/i",
"/\bcelui-là\b/i",
"/\bcelà\b/i",
"/\bcent\b/i",
"/\bcependant\b/i",
"/\bcertain\b/i",
"/\bcertaine\b/i",
"/\bcertaines\b/i",
"/\bcertains\b/i",
"/\bcertes\b/i",
"/\bces\b/i",
"/\bcet\b/i",
"/\bcette\b/i",
"/\bceux\b/i",
"/\bceux-ci\b/i",
"/\bceux-là\b/i",
"/\bchacun\b/i",
"/\bchacune\b/i",
"/\bchaque\b/i",
"/\bcher\b/i",
"/\bchers\b/i",
"/\bchez\b/i",
"/\bchiche\b/i",
"/\bchut\b/i",
"/\bchère\b/i",
"/\bchères\b/i",
"/\bci\b/i",
"/\bcinq\b/i",
"/\bcinquantaine\b/i",
"/\bcinquante\b/i",
"/\bcinquantième\b/i",
"/\bcinquième\b/i",
"/\bclac\b/i",
"/\bclic\b/i",
"/\bcombien\b/i",
"/\bcomme\b/i",
"/\bcomment\b/i",
"/\bcomparable\b/i",
"/\bcomparables\b/i",
"/\bcompris\b/i",
"/\bconcernant\b/i",
"/\bcontre\b/i",
"/\bcouic\b/i",
"/\bcrac\b/i",
"/\bd\b/i",
"/\bda\b/i",
"/\bdans\b/i",
"/\bde\b/i",
"/\bdebout\b/i",
"/\bdedans\b/i",
"/\bdehors\b/i",
"/\bdeja\b/i",
"/\bdelà\b/i",
"/\bdepuis\b/i",
"/\bdernier\b/i",
"/\bderniere\b/i",
"/\bderriere\b/i",
"/\bderrière\b/i",
"/\bdes\b/i",
"/\bdesormais\b/i",
"/\bdesquelles\b/i",
"/\bdesquels\b/i",
"/\bdessous\b/i",
"/\bdessus\b/i",
"/\bdeux\b/i",
"/\bdeuxième\b/i",
"/\bdeuxièmement\b/i",
"/\bdevant\b/i",
"/\bdevers\b/i",
"/\bdevra\b/i",
"/\bdevrait\b/i",
"/\bdifferent\b/i",
"/\bdifferentes\b/i",
"/\bdifferents\b/i",
"/\bdifférent\b/i",
"/\bdifférente\b/i",
"/\bdifférentes\b/i",
"/\bdifférents\b/i",
"/\bdire\b/i",
"/\bdirecte\b/i",
"/\bdirectement\b/i",
"/\bdit\b/i",
"/\bdite\b/i",
"/\bdits\b/i",
"/\bdivers\b/i",
"/\bdiverse\b/i",
"/\bdiverses\b/i",
"/\bdix\b/i",
"/\bdix-huit\b/i",
"/\bdix-neuf\b/i",
"/\bdix-sept\b/i",
"/\bdixième\b/i",
"/\bdoit\b/i",
"/\bdoivent\b/i",
"/\bdonc\b/i",
"/\bdont\b/i",
"/\bdos\b/i",
"/\bdouze\b/i",
"/\bdouzième\b/i",
"/\bdring\b/i",
"/\bdroite\b/i",
"/\bdu\b/i",
"/\bduquel\b/i",
"/\bdurant\b/i",
"/\bdès\b/i",
"/\bdébut\b/i",
"/\bdésormais\b/i",
"/\be\b/i",
"/\beffet\b/i",
"/\begale\b/i",
"/\begalement\b/i",
"/\begales\b/i",
"/\beh\b/i",
"/\belle\b/i",
"/\belle-même\b/i",
"/\belles\b/i",
"/\belles-mêmes\b/i",
"/\ben\b/i",
"/\bencore\b/i",
"/\benfin\b/i",
"/\bentre\b/i",
"/\benvers\b/i",
"/\benviron\b/i",
"/\bes\b/i",
"/\bessai\b/i",
"/\best\b/i",
"/\bet\b/i",
"/\betant\b/i",
"/\betc\b/i",
"/\betre\b/i",
"/\beu\b/i",
"/\beue\b/i",
"/\beues\b/i",
"/\beuh\b/i",
"/\beurent\b/i",
"/\beus\b/i",
"/\beusse\b/i",
"/\beussent\b/i",
"/\beusses\b/i",
"/\beussiez\b/i",
"/\beussions\b/i",
"/\beut\b/i",
"/\beux\b/i",
"/\beux-mêmes\b/i",
"/\bexactement\b/i",
"/\bexcepté\b/i",
"/\bextenso\b/i",
"/\bexterieur\b/i",
"/\beûmes\b/i",
"/\beût\b/i",
"/\beûtes\b/i",
"/\bf\b/i",
"/\bfais\b/i",
"/\bfaisaient\b/i",
"/\bfaisant\b/i",
"/\bfait\b/i",
"/\bfaites\b/i",
"/\bfaçon\b/i",
"/\bferont\b/i",
"/\bfi\b/i",
"/\bflac\b/i",
"/\bfloc\b/i",
"/\bfois\b/i",
"/\bfont\b/i",
"/\bforce\b/i",
"/\bfurent\b/i",
"/\bfus\b/i",
"/\bfusse\b/i",
"/\bfussent\b/i",
"/\bfusses\b/i",
"/\bfussiez\b/i",
"/\bfussions\b/i",
"/\bfut\b/i",
"/\bfûmes\b/i",
"/\bfût\b/i",
"/\bfûtes\b/i",
"/\bg\b/i",
"/\bgens\b/i",
"/\bh\b/i",
"/\bha\b/i",
"/\bhaut\b/i",
"/\bhein\b/i",
"/\bhem\b/i",
"/\bhep\b/i",
"/\bhi\b/i",
"/\bho\b/i",
"/\bholà\b/i",
"/\bhop\b/i",
"/\bhormis\b/i",
"/\bhors\b/i",
"/\bhou\b/i",
"/\bhoup\b/i",
"/\bhue\b/i",
"/\bhui\b/i",
"/\bhuit\b/i",
"/\bhuitième\b/i",
"/\bhum\b/i",
"/\bhurrah\b/i",
"/\bhé\b/i",
"/\bhélas\b/i",
"/\bi\b/i",
"/\bici\b/i",
"/\bil\b/i",
"/\bils\b/i",
"/\bimporte\b/i",
"/\bj\b/i",
"/\bje\b/i",
"/\bjusqu\b/i",
"/\bjusque\b/i",
"/\bjuste\b/i",
"/\bk\b/i",
"/\bl\b/i",
"/\bla\b/i",
"/\blaisser\b/i",
"/\blaquelle\b/i",
"/\blas\b/i",
"/\ble\b/i",
"/\blequel\b/i",
"/\bles\b/i",
"/\blesquelles\b/i",
"/\blesquels\b/i",
"/\bleur\b/i",
"/\bleurs\b/i",
"/\blongtemps\b/i",
"/\blors\b/i",
"/\blorsque\b/i",
"/\blui\b/i",
"/\blui-meme\b/i",
"/\blui-même\b/i",
"/\blà\b/i",
"/\blès\b/i",
"/\bm\b/i",
"/\bma\b/i",
"/\bmaint\b/i",
"/\bmaintenant\b/i",
"/\bmais\b/i",
"/\bmalgre\b/i",
"/\bmalgré\b/i",
"/\bmaximale\b/i",
"/\bme\b/i",
"/\bmeme\b/i",
"/\bmemes\b/i",
"/\bmerci\b/i",
"/\bmes\b/i",
"/\bmien\b/i",
"/\bmienne\b/i",
"/\bmiennes\b/i",
"/\bmiens\b/i",
"/\bmille\b/i",
"/\bmince\b/i",
"/\bmine\b/i",
"/\bminimale\b/i",
"/\bmoi\b/i",
"/\bmoi-meme\b/i",
"/\bmoi-même\b/i",
"/\bmoindres\b/i",
"/\bmoins\b/i",
"/\bmon\b/i",
"/\bmot\b/i",
"/\bmoyennant\b/i",
"/\bmultiple\b/i",
"/\bmultiples\b/i",
"/\bmême\b/i",
"/\bmêmes\b/i",
"/\bn\b/i",
"/\bna\b/i",
"/\bnaturel\b/i",
"/\bnaturelle\b/i",
"/\bnaturelles\b/i",
"/\bne\b/i",
"/\bneanmoins\b/i",
"/\bnecessaire\b/i",
"/\bnecessairement\b/i",
"/\bneuf\b/i",
"/\bneuvième\b/i",
"/\bni\b/i",
"/\bnombreuses\b/i",
"/\bnombreux\b/i",
"/\bnommés\b/i",
"/\bnon\b/i",
"/\bnos\b/i",
"/\bnotamment\b/i",
"/\bnotre\b/i",
"/\bnous\b/i",
"/\bnous-mêmes\b/i",
"/\bnouveau\b/i",
"/\bnouveaux\b/i",
"/\bnul\b/i",
"/\bnéanmoins\b/i",
"/\bnôtre\b/i",
"/\bnôtres\b/i",
"/\bo\b/i",
"/\boh\b/i",
"/\bohé\b/i",
"/\bollé\b/i",
"/\bolé\b/i",
"/\bon\b/i",
"/\bont\b/i",
"/\bonze\b/i",
"/\bonzième\b/i",
"/\bore\b/i",
"/\bou\b/i",
"/\bouf\b/i",
"/\bouias\b/i",
"/\boust\b/i",
"/\bouste\b/i",
"/\boutre\b/i",
"/\bouvert\b/i",
"/\bouverte\b/i",
"/\bouverts\b/i",
"/\bo|\b/i",
"/\boù\b/i",
"/\bp\b/i",
"/\bpaf\b/i",
"/\bpan\b/i",
"/\bpar\b/i",
"/\bparce\b/i",
"/\bparfois\b/i",
"/\bparle\b/i",
"/\bparlent\b/i",
"/\bparler\b/i",
"/\bparmi\b/i",
"/\bparole\b/i",
"/\bparseme\b/i",
"/\bpartant\b/i",
"/\bparticulier\b/i",
"/\bparticulière\b/i",
"/\bparticulièrement\b/i",
"/\bpas\b/i",
"/\bpassé\b/i",
"/\bpendant\b/i",
"/\bpense\b/i",
"/\bpermet\b/i",
"/\bpersonne\b/i",
"/\bpersonnes\b/i",
"/\bpeu\b/i",
"/\bpeut\b/i",
"/\bpeuvent\b/i",
"/\bpeux\b/i",
"/\bpff\b/i",
"/\bpfft\b/i",
"/\bpfut\b/i",
"/\bpif\b/i",
"/\bpire\b/i",
"/\bpièce\b/i",
"/\bplein\b/i",
"/\bplouf\b/i",
"/\bplupart\b/i",
"/\bplus\b/i",
"/\bplusieurs\b/i",
"/\bplutôt\b/i",
"/\bpossessif\b/i",
"/\bpossessifs\b/i",
"/\bpossible\b/i",
"/\bpossibles\b/i",
"/\bpouah\b/i",
"/\bpour\b/i",
"/\bpourquoi\b/i",
"/\bpourrais\b/i",
"/\bpourrait\b/i",
"/\bpouvait\b/i",
"/\bprealable\b/i",
"/\bprecisement\b/i",
"/\bpremier\b/i",
"/\bpremière\b/i",
"/\bpremièrement\b/i",
"/\bpres\b/i",
"/\bprobable\b/i",
"/\bprobante\b/i",
"/\bprocedant\b/i",
"/\bproche\b/i",
"/\bprès\b/i",
"/\bpsitt\b/i",
"/\bpu\b/i",
"/\bpuis\b/i",
"/\bpuisque\b/i",
"/\bpur\b/i",
"/\bpure\b/i",
"/\bq\b/i",
"/\bqu\b/i",
"/\bquand\b/i",
"/\bquant\b/i",
"/\bquant-à-soi\b/i",
"/\bquanta\b/i",
"/\bquarante\b/i",
"/\bquatorze\b/i",
"/\bquatre\b/i",
"/\bquatre-vingt\b/i",
"/\bquatrième\b/i",
"/\bquatrièmement\b/i",
"/\bque\b/i",
"/\bquel\b/i",
"/\bquelconque\b/i",
"/\bquelle\b/i",
"/\bquelles\b/i",
"/\bquelqu'un\b/i",
"/\bquelque\b/i",
"/\bquelques\b/i",
"/\bquels\b/i",
"/\bqui\b/i",
"/\bquiconque\b/i",
"/\bquinze\b/i",
"/\bquoi\b/i",
"/\bquoique\b/i",
"/\br\b/i",
"/\brare\b/i",
"/\brarement\b/i",
"/\brares\b/i",
"/\brelative\b/i",
"/\brelativement\b/i",
"/\bremarquable\b/i",
"/\brend\b/i",
"/\brendre\b/i",
"/\brestant\b/i",
"/\breste\b/i",
"/\brestent\b/i",
"/\brestrictif\b/i",
"/\bretour\b/i",
"/\brevoici\b/i",
"/\brevoilà\b/i",
"/\brien\b/i",
"/\bs\b/i",
"/\bsa\b/i",
"/\bsacrebleu\b/i",
"/\bsait\b/i",
"/\bsans\b/i",
"/\bsapristi\b/i",
"/\bsauf\b/i",
"/\bse\b/i",
"/\bsein\b/i",
"/\bseize\b/i",
"/\bselon\b/i",
"/\bsemblable\b/i",
"/\bsemblaient\b/i",
"/\bsemble\b/i",
"/\bsemblent\b/i",
"/\bsent\b/i",
"/\bsept\b/i",
"/\bseptième\b/i",
"/\bsera\b/i",
"/\bserai\b/i",
"/\bseraient\b/i",
"/\bserais\b/i",
"/\bserait\b/i",
"/\bseras\b/i",
"/\bserez\b/i",
"/\bseriez\b/i",
"/\bserions\b/i",
"/\bserons\b/i",
"/\bseront\b/i",
"/\bses\b/i",
"/\bseul\b/i",
"/\bseule\b/i",
"/\bseulement\b/i",
"/\bsi\b/i",
"/\bsien\b/i",
"/\bsienne\b/i",
"/\bsiennes\b/i",
"/\bsiens\b/i",
"/\bsinon\b/i",
"/\bsix\b/i",
"/\bsixième\b/i",
"/\bsoi\b/i",
"/\bsoi-même\b/i",
"/\bsoient\b/i",
"/\bsois\b/i",
"/\bsoit\b/i",
"/\bsoixante\b/i",
"/\bsommes\b/i",
"/\bson\b/i",
"/\bsont\b/i",
"/\bsous\b/i",
"/\bsouvent\b/i",
"/\bsoyez\b/i",
"/\bsoyons\b/i",
"/\bspecifique\b/i",
"/\bspecifiques\b/i",
"/\bspeculatif\b/i",
"/\bstop\b/i",
"/\bstrictement\b/i",
"/\bsubtiles\b/i",
"/\bsuffisant\b/i",
"/\bsuffisante\b/i",
"/\bsuffit\b/i",
"/\bsuis\b/i",
"/\bsuit\b/i",
"/\bsuivant\b/i",
"/\bsuivante\b/i",
"/\bsuivantes\b/i",
"/\bsuivants\b/i",
"/\bsuivre\b/i",
"/\bsujet\b/i",
"/\bsuperpose\b/i",
"/\bsur\b/i",
"/\bsurtout\b/i",
"/\bt\b/i",
"/\bta\b/i",
"/\btac\b/i",
"/\btandis\b/i",
"/\btant\b/i",
"/\btardive\b/i",
"/\bte\b/i",
"/\btel\b/i",
"/\btelle\b/i",
"/\btellement\b/i",
"/\btelles\b/i",
"/\btels\b/i",
"/\btenant\b/i",
"/\btend\b/i",
"/\btenir\b/i",
"/\btente\b/i",
"/\btes\b/i",
"/\btic\b/i",
"/\btien\b/i",
"/\btienne\b/i",
"/\btiennes\b/i",
"/\btiens\b/i",
"/\btoc\b/i",
"/\btoi\b/i",
"/\btoi-même\b/i",
"/\bton\b/i",
"/\btouchant\b/i",
"/\btoujours\b/i",
"/\btous\b/i",
"/\btout\b/i",
"/\btoute\b/i",
"/\btoutefois\b/i",
"/\btoutes\b/i",
"/\btreize\b/i",
"/\btrente\b/i",
"/\btres\b/i",
"/\btrois\b/i",
"/\btroisième\b/i",
"/\btroisièmement\b/i",
"/\btrop\b/i",
"/\btrès\b/i",
"/\btsoin\b/i",
"/\btsouin\b/i",
"/\btu\b/i",
"/\bté\b/i",
"/\bu\b/i",
"/\bun\b/i",
"/\bune\b/i",
"/\bunes\b/i",
"/\buniformement\b/i",
"/\bunique\b/i",
"/\buniques\b/i",
"/\buns\b/i",
"/\bv\b/i",
"/\bva\b/i",
"/\bvais\b/i",
"/\bvaleur\b/i",
"/\bvas\b/i",
"/\bvers\b/i",
"/\bvia\b/i",
"/\bvif\b/i",
"/\bvifs\b/i",
"/\bvingt\b/i",
"/\bvivat\b/i",
"/\bvive\b/i",
"/\bvives\b/i",
"/\bvlan\b/i",
"/\bvoici\b/i",
"/\bvoie\b/i",
"/\bvoient\b/i",
"/\bvoilà\b/i",
"/\bvont\b/i",
"/\bvos\b/i",
"/\bvotre\b/i",
"/\bvous\b/i",
"/\bvous-mêmes\b/i",
"/\bvu\b/i",
"/\bvé\b/i",
"/\bvôtre\b/i",
"/\bvôtres\b/i",
"/\bw\b/i",
"/\bx\b/i",
"/\by\b/i",
"/\bz\b/i",
"/\bzut\b/i",
"/\bà\b/i",
"/\bâ\b/i",
"/\bça\b/i",
"/\bès\b/i",
"/\bétaient\b/i",
"/\bétais\b/i",
"/\bétait\b/i",
"/\bétant\b/i",
"/\bétat\b/i",
"/\bétiez\b/i",
"/\bétions\b/i",
"/\bété\b/i",
"/\bétée\b/i",
"/\bétées\b/i",
"/\bétés\b/i",
"/\bêtes\b/i",
"/\bêtre\b/i",
"/\bô\b/i",];
}