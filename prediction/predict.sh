#!/bin/bash
echo "START PREDCT SCRIPT"
echo "DELETING OLD FILES"
rm output/test.svm
rm output/train.svm
rm result/model
rm result/predict
rm result/result.txt

echo "ADD LINE NUMBERS"
php addLineNumber.php

echo "RUN ANALYSER"
php analyser.php


echo "RUN PREDICTION"
liblinear-2.21/train -c 4 -e 0.1 output/train.svm result/model
liblinear-2.21/predict output/test.svm result/model result/predect


echo "FORMAT PREDICTION"
php predictFormater.php 
