<?php

$full_data=[];
$full_results=[];

$file = fopen('data/task1-testGold.csv', 'r');
while (($line = fgetcsv($file,0,"\t")) !== FALSE) {
		$line = array_map('strtolower', $line);
		array_push($full_data, $line);
  }
fclose($file);
$tmp_contents="";
$number=1;

$file = fopen('result/predect', 'r');
while (($line = fgetcsv($file)) !== FALSE) {
		array_push($full_results, $line);
  }
fclose($file);


foreach ($full_data as $keyFD => $valueFD) {
	$tmp_words=explode(" ", $valueFD[0]);
	$annotation="";
	switch ($full_results[$keyFD][0]) {
		case 0:
			# code...
		$annotation="autre";
			break;
		
		case 1:
			# code...
		$annotation="positif";
			break;
		
		case 2:
			# code...
		$annotation="negatif";
			break;
		
		case 3:
			# code...
		$annotation="mixte";
			break;
	}

	//$tmp_words[1]=$annotation;
	$tmp_contents.=$tmp_words[0]." ".$annotation."\n";
	//echo $tmp_pair; die;
	//$tmp_contents.=$number++."\t".$tmp_pair."\t"."objective"."\n";
}

		echo file_put_contents("/var/www/html/analyse-des-sentiments-full/prediction/result/result.txt",$tmp_contents);
