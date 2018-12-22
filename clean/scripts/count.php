<?php

	$full_data=file_get_contents("../dataset/step1-clean-unlabeled-data.csv");

foreach (array_count_values(str_word_count($full_data, 1)) as $key => $value) {
    echo $key." ".$value.'<br>';
}