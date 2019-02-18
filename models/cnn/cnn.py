from keras.models import Sequential
from keras.layers.core import TimeDistributedDense
from keras.layers.advanced_activations import PReLU
from keras.layers.normalization import BatchNormalization
from keras import backend as K
#from keras.layers.core import Dense, Dropout, Activation, Reshape, Flatten
from keras.layers import TimeDistributed, Lambda, Input, merge, Bidirectional
from keras.layers.core import Dense, Activation, Dropout, Flatten
from keras.layers import GlobalAveragePooling1D, GlobalMaxPooling1D, Conv1D, MaxPooling1D
#from keras.layers.convolutional import Convolution1D, MaxPooling1D, AveragePooling1D
from keras.optimizers import SGD, Adadelta, Adam, Adamax, RMSprop
from keras.models import Model
#from keras.layers.recurrent import LSTM, GRU, SimpleRNN
from keras.constraints import maxnorm
from keras.callbacks import Callback, EarlyStopping
from keras.preprocessing import sequence
from keras.layers.embeddings import Embedding
from keras.regularizers import l2, activity_l2
from keras import regularizers
from keras.models import model_from_json
import theano
from theano import tensor
import warnings
import sys
import time
import os
import numpy as np
import fileinput
import math

warnings.filterwarnings("ignore")



maxlen = 100 #size of word embedding
word_nb_feature_maps = 200 
hidden_size = 64
conv="1,2,3,4,5"

word_embedding_file = sys.argv[1]
train_file = sys.argv[2]
dev_file = sys.argv[3]


sentence_input = Input(shape=(maxlen,))

x = Embedding(word_vocab_size, word_embedding_size, weights=[word_initialize_weight], trainable=False)(sentence_input)
x = Dropout(0.3)(x)

xconv = []
for xnumber in conv.split(","):
    xconv.append( Conv1D(word_nb_feature_maps, (int)(xnumber), activation='relu')(x) )

merged = merge(xconv, mode='concat', concat_axis=1)

max_merged = GlobalMaxPooling1D()(merged)

x = Dropout(0.3)(max_merged)
x = Dense(hidden_size)(x)
x = Activation("relu")(x)

x = Dropout(0.2)(x)
x = Dense(4)(x)

model = Model(input=sentence_input, output=outx)

optim = Adadelta(lr=1.0, rho=0.95, epsilon=1e-06)
model.compile(loss='categorical_crossentropy', optimizer=optim, metrics=['accuracy'])
model.fit(X_word_train, Y_train, nb_epoch=50, batch_size=128, shuffle=True)



