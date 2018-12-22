#!/bin/bash

printf "START PREDICTION SCRIPT \n"

echo "\nSTEP 0/ : DELETE OLD FILES\n"
#rm -rf dataset/step1-clean-unlabeled-data.csv
#rm -rf dataset/step2-auto-annoted-data.csv
rm -rf dataset/step3-clean-labeled-data.csv
rm -rf dataset/step4-already-annoted-data.csv
rm -rf dataset/step5-final-train-data.csv
rm -rf dataset/step6-unformated-test-data.csv
rm -rf dataset/step7-clean-test-data.csv
rm -rf dataset/step8-final-test-data.csv
rm -rf output/test.svm
rm -rf output/train.svm
rm -rf output/model
rm -rf output/predect
rm -rf result/result.txt


echo "\nSTART EXECUTING SCRIPTS\n"
cd scripts

echo "\nSTEP 1/ : CLEAN UNLABELED TWEETS\n"
#php step1-clean-unlabeled-tweets.php



echo "\nSTEP 2/ : ANNOTATE UNLABELED TWEETS\n"
php step2-annotate-unlabeled-tweets.php


echo "\nSTEP 3/ : CLEAN LABELED TWEETS\n"
php step3-clean-labeled-tweets.php



echo "\nSTEP 4/ : ANNOTATE LABELED TWEETS\n"
php step4-annotate-labeled-tweets.php



echo "\nSTEP 5/ : MERGE NEW AND OLD TWEETS AND ADD NUMBERS\n"
php step5-merge-new-and-old-tweets.php



echo "\nSTEP 6/ : FORMAT TEST DATA\n"
php step6-format-test-data.php



echo "\nSTEP 7/ : CLEAN TEST DATA\n"
php step7-clean-test-data.php


echo "\nSTEP 8/ : ADD NUMBERS AND ANNOTATION TO TEST DATA\n"
php step8-add-numbers-and-annotation-to-test-data.php

echo "\nSTEP 9/ : PREPARE SVM FILES\n"
php step9-prepare-svm-files.php

echo "\nSCRIPTS EXECUTED\n"
cd ..

echo "\nSTEP 10/ : TRAIN MODEL WITH LIBLINEAR\n"
libs/liblinear-2.21/train  -c 4 -e 0.1 -s 2 output/train.svm result/model


echo "\nSTEP 10/ : PREDECT TEST ANNOTAION USING LIBLINEAR\n"
libs/liblinear-2.21/predict output/test.svm result/model result/predect

echo "\nSTEP 11/ : FORMATING PREDECTED DATA\n"
cd scripts
php step11-format-svm-predection.php
cd ..