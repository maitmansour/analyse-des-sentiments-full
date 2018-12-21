#!/bin/bash

printf "START PREDICTION SCRIPT \n"

echo "\nSTEP 0/ : DELETE OLD FILES\n"
rm -rf dataset/auto-labeled-train.csv
rm -rf dataset/already-annoted-data.csv
rm -rf dataset/clean-labeled-data.csv
rm -rf dataset/clean-data.csv
rm -rf output/test.svm
rm -rf output/train.svm
rm -rf output/model
rm -rf output/predect
rm -rf result/result.txt

echo "\nSTEP 1/ : CLEAN UNLABELED TWEETS\n"
cd scripts
#php step1-clean-unlabeled-tweets.php
cd ..


echo "\nSTEP 2/ : ANNOTATE UNLABELED TWEETS\n"
cd scripts
#php step2-annotate-unlabeled-tweets.php
cd ..

echo "\nSTEP 3/ : CLEAN LABELED TWEETS\n"
cd scripts
php step3-clean-labeled-tweets.php
cd ..


echo "\nSTEP 4/ : ANNOTATE LABELED TWEETS\n"
cd scripts
php step4-annotate-labeled-tweets.php
cd ..


echo "\nSTEP 5/ : MERGE NEW AND OLD TWEETS\n"
cd scripts
php step5-merge-new-and-old-tweets.php
cd ..

#echo "RUN ANALYSER"
#php analyser.php
#
#
#echo "RUN MODEL CREATION"
#liblinear-2.21/train  -c 4 -e 0.1 -s 2 output/train.svm result/model
#
#echo "RUN PREDICTION"
#liblinear-2.21/predict output/test.svm result/model result/predect
#
#
#echo "FORMAT PREDICTION"
#php predictFormater.php 
