<?php

$full_data=[];

$file = fopen('../dataset/step0-original-test-data.csv', 'r');
while (($line = fgetcsv($file,0,"\t")) !== FALSE) {
		array_push($full_data, $line);
}

fclose($file);
$tmp_contents="";

foreach ($full_data as $keyFD => $valueFD) {
	$tmp_words=explode(" ", $valueFD[0]);
	unset($tmp_words[0]);
	$tmp_pair=implode(" ", $tmp_words);
	$tmp_contents.=$tmp_pair."\n";
}

echo file_put_contents("/var/www/html/analyse-des-sentiments-full/clean/dataset/step6-unformated-test-data.csv",$tmp_contents);
