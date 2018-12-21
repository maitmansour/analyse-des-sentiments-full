<?php

// Read Files
$test_data = file_get_contents("../dataset/step7-clean-test-data.csv", 'r');

// Merge Tweets
$full_test_data_with_number=addNumbersToTestData($test_data);

// Write merged File
echo file_put_contents("/var/www/html/analyse-des-sentiments-full/clean/dataset/step8-final-test-data.csv",$full_test_data_with_number);





function addNumbersToTestData($test_data)
{
	$full_data=[];

	$test_data_array=explode("\n", $test_data);

	$tmp_contents="";
	foreach ($test_data_array as $keyFD => $valueFD) {
		$tmp_contents.=($keyFD+1)."\t".$valueFD."\t"."objective"."\n";
	}

	return $tmp_contents;
}