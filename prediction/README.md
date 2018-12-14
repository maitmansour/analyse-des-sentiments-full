# Analyse des sentiments

Analyser les sentiments sur des tweets

# Training

    liblinear-2.21/train -c 4 -e 0.1 -v 5 output/train.svm result/model


# Predection

    liblinear-2.21/predict output/test.svm result/model result/predect

# Résultats

Accuracy = 59.205% (566/956)
