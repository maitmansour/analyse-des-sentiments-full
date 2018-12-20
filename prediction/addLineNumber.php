<?php

$full_data=[];

$file = fopen('data/task1-testGold.csv', 'r');
while (($line = fgetcsv($file,0,"\t")) !== FALSE) {
		//$line = array_map('strtolower', $line);
		array_push($full_data, $line);
  }
fclose($file);
$tmp_contents="";
$number=1;
foreach ($full_data as $keyFD => $valueFD) {
	$tmp_words=explode(" ", $valueFD[0]);
	unset($tmp_words[0]);
	$tmp_pair=implode(" ", $tmp_words);
	$tmp_contents.=$number++."\t".$tmp_pair."\t"."objective"."\n";
}

		echo file_put_contents("/var/www/html/analyse-des-sentiments-full/prediction/data/step1.csv",$tmp_contents);
