<?php

	$full_data=file_get_contents("../dataset/step1-clean-unlabeled-data.csv");

$words=array_count_values(str_word_count($full_data, 1)) ;
asort($words);
foreach ($words as $key => $value) {
    echo $value." ".$key.'<br>';
}