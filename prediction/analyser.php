<?php

$full_data=[];
$words=[];
$index=1;
$train_limit=0;
$file = fopen('data/task1-train.csv', 'r');
while (($line = fgetcsv($file,0,"\t")) !== FALSE) {
	if (count($line)==3) {
		$line[1] = stringFormatter($line[1]);
		array_push($full_data, $line);
	}
  }
fclose($file);
$train_limit=count($full_data)-1;

$file = fopen('data/step1.csv', 'r');
while (($line = fgetcsv($file,0,"\t")) !== FALSE) {
	if (count($line)==3) {

		$line[1] = stringFormatter($line[1]);
		array_push($full_data, $line);
	}
  }
fclose($file);

$tmp_contents="";
foreach ($full_data as $keyFD => $valueFD) {
	$tmp_words=explode(" ", $valueFD[1]);

	switch ($valueFD[2]) {
		case 'positif':
			$label="1";
			break;
		case 'negatif':
			$label="2";
			break;
		case 'mixte':
			$label="3";
			break;
		default:
			$label="0";
			break;
	}

	$tmp_pair="";
	$tmp_pairs_array=[];
	foreach ($tmp_words as $key => $value) {
		if (isset($words[$value])) {
			$index_word=$words[$value];
		}else{
			$words[$value]=$index;
			$index_word=$index;
			$index++;
		}

		$tmp_repetition=0;
		foreach ($tmp_words as $keyTmp => $valueTmp) {
			if ($value==$valueTmp) {
				$tmp_repetition++;
			}
		}

		$tmp_pair=$index_word.":".$tmp_repetition;
		$tmp_pairs_array[$index_word]=$tmp_pair;

	}
	ksort($tmp_pairs_array);
	//var_dump($tmp_pairs_array); die;
	$tmp_pair=implode(" ", $tmp_pairs_array);
	$tmp_contents.=$label." ".$tmp_pair."\n";
	if($keyFD==$train_limit){
		echo file_put_contents("/var/www/html/analyse-des-sentiments-full/prediction/output/train.svm",$tmp_contents);
		$tmp_contents="";
	}
}

		echo file_put_contents("/var/www/html/analyse-des-sentiments-full/prediction/output/test.svm",$tmp_contents);



function stringFormatter($string){
	//  STR TO LOWER
	$string= strtolower($string);

	// delete ponctuation
	$string = preg_replace('/[[:punct:]]/', ' ', $string);

	return $string;
}
