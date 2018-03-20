import numpy as np
import os
from time import time
from sklearn import ensemble, metrics
from sklearn import neighbors
import matplotlib.pyplot as plt
from sklearn.metrics import classification_report

dataSet = "Cancer" #could be either "Cancer" or "Character"

def plot_learning_curve(estimator, title, X, y, ylim=None, cv=None,
                        n_jobs=1, train_sizes=np.linspace(0.01, 1.0, 15)):
    """
    Generate a simple plot of the test and traning learning curve.

    Parameters
    ----------
    estimator : object type that implements the "fit" and "predict" methods
        An object of that type which is cloned for each validation.

    title : string
        Title for the chart.

    X : array-like, shape (n_samples, n_features)
        Training vector, where n_samples is the number of samples and
        n_features is the number of features.

    y : array-like, shape (n_samples) or (n_samples, n_features), optional
        Target relative to X for classification or regression;
        None for unsupervised learning.

    ylim : tuple, shape (ymin, ymax), optional
        Defines minimum and maximum yvalues plotted.

    cv : integer, cross-validation generator, optional
        If an integer is passed, it is the number of folds (defaults to 3).
        Specific cross-validation objects can be passed, see
        sklearn.cross_validation module for the list of possible objects

    n_jobs : integer, optional
        Number of jobs to run in parallel (default 1).
    """
    plt.figure()
    plt.title(title)
    if ylim is not None:
        plt.ylim(*ylim)
    plt.xlabel("Training examples")
    plt.ylabel("Score")
    train_sizes, train_scores, test_scores = learning_curve(
        estimator, X, y, cv=cv, n_jobs=n_jobs, train_sizes=train_sizes)
    train_scores_mean = np.mean(train_scores, axis=1)
    train_scores_std = np.std(train_scores, axis=1)
    test_scores_mean = np.mean(test_scores, axis=1)
    test_scores_std = np.std(test_scores, axis=1)
    plt.grid()

    plt.fill_between(train_sizes, train_scores_mean - train_scores_std,
                     train_scores_mean + train_scores_std, alpha=0.1,
                     color="r")
    plt.fill_between(train_sizes, test_scores_mean - test_scores_std,
                     test_scores_mean + test_scores_std, alpha=0.1, color="g")
    plt.plot(train_sizes, train_scores_mean, 'o-', color="r",
             label="Training score")
    plt.plot(train_sizes, test_scores_mean, 'o-', color="g",
             label="Cross-validation score")

    plt.legend(loc="best")
    return plt

def readCancerData(fn):
    data = np.recfromcsv(fn)
    dataList = []
    for i in range(len(data)):
        dataList.append(list(data[i]))

    dataList = np.asarray(dataList)

    for i in range(len(dataList)):
        if dataList[i,1]=="M":
            dataList[i,1] = 1

        if dataList[i,1]=="B":
            dataList[i,1] = -1

    dataList= np.asarray(dataList)

    newData = np.zeros((len(dataList),len(dataList[0])-1))
    newData[:,0:-1] = dataList[:,2:]
    newData[:,-1] = dataList[:,1]

    return np.asarray(newData)

def readCharacterData(fn):
    data = np.recfromcsv(fn)
    dataList = []
    for i in range(len(data)):
        dataList.append(list(data[i]))
    dataList = np.asarray(dataList)
    for i in range(len(dataList)):
        if dataList[i, 0] == "A":
            dataList[i, 0] = 1
        if dataList[i, 0] == "B":
            dataList[i,0] = 2
        if dataList[i, 0] == "C":
            dataList[i, 0] = 3
        if dataList[i,0] == "D":
            dataList[i,0] = 4
        if dataList[i, 0] == "E":
            dataList[i, 0] = 5
        if dataList[i, 0] == "F":
            dataList[i, 0] = 6
        if dataList[i, 0] == "G":
            dataList[i, 0] = 7
        if dataList[i, 0] == "H":
            dataList[i, 0] = 8
        if dataList[i, 0] == "I":
            dataList[i, 0] = 9
        if dataList[i, 0] == "J":
            dataList[i, 0] = 10
        if dataList[i, 0] == "K":
            dataList[i, 0] = 11
        if dataList[i, 0] == "L":
            dataList[i, 0] = 12
        if dataList[i, 0] == "M":
            dataList[i, 0] = 13
        if dataList[i, 0] == "N":
            dataList[i, 0] = 14
        if dataList[i, 0] == "O":
            dataList[i, 0] = 15
        if dataList[i, 0] == "P":
            dataList[i, 0] = 16
        if dataList[i, 0] == "Q":
            dataList[i, 0] = 17
        if dataList[i, 0] == "R":
            dataList[i, 0] = 18
        if dataList[i, 0] == "S":
            dataList[i, 0] = 19
        if dataList[i, 0] == "T":
            dataList[i, 0] = 20
        if dataList[i, 0] == "U":
            dataList[i, 0] = 21
        if dataList[i, 0] == "V":
            dataList[i, 0] = 22
        if dataList[i, 0] == "W":
            dataList[i, 0] = 23
        if dataList[i, 0] == "X":
            dataList[i, 0] = 24
        if dataList[i, 0] == "Y":
            dataList[i, 0] = 25
        if dataList[i, 0] == "Z":
            dataList[i, 0] = 26

    dataList= np.asarray(dataList)

    newData = np.zeros((len(dataList),len(dataList[0])))
    newData[:,0:-1] = dataList[:,1:]
    newData[:,-1] = dataList[:,0]

    return np.asarray(newData)

def reduce_dimension(indices, data):
    reduce_data = []
    temp = []
    for row in data:
        for i in range(len(row)):
            if i in indices:
                temp.append(row[i])
        reduce_data.append(temp)
        temp = []

    return np.asarray(reduce_data)

def normalize(data,param = False):
    if not param:
        mean = []
        for i in range(len(data[0])-1):
            tempMean = data[:,i].mean()
            tempStd = data[:,i].std()
            mean.append([tempMean,tempStd])
            data[:,i] = (data[:,i]-tempMean)/tempStd
        return data, mean
    else:
        for i in range(len(data[0])-1):
            data[:,i] = (data[:,i]-param[i][0])/param[i][1]
        return data, param


def prepare_model(data,percentage,randomize=False):
    if randomize:
        np.random.shuffle(data)
    trainingDataPercentatge = percentage
    index = np.floor(trainingDataPercentatge*len(data))
    training_data =data[:index]
    testing_data = data[index:]
    return training_data, testing_data

# Read Cancer or Character Data

if dataSet == "Cancer":
    data = readCancerData("wdbc.csv")
    target_names = ["Class 1","Class 2"]
    bin_count = 2
elif dataSet == "Character":
    data = readCharacterData("letterRecognition.csv")
    target_names = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z']
    bin_count = 26

training_data , testing_data =  prepare_model(data,0.6,randomize=False) #Splitting data into training and testing

features_train = training_data[:,:-1] #selecting trainging data without labels
labels_train = training_data[:,-1] #selecting training data labels

#plot class distributions for testing Data
plt.hist(labels_train,bins=bin_count)
plt.xlabel("Classes")
plt.ylabel("Count")
plt.title(dataSet+" Sample Distribution of Training Data")
plt.savefig('./Boosting/'+dataSet+' Sample Distribution of Training Data.png')
plt.clf()
plt.close()

features_test = testing_data[:,:-1]  #selecting testing data without labels

#Normalization
normalization = True
if normalization:
    features_train,mean = normalize(features_train)
    features_test,param = normalize(features_test,mean)

#Finding top N Features
numFeatures = 4
from sklearn.ensemble import ExtraTreesClassifier
mod = ExtraTreesClassifier()
mod.fit(features_train,labels_train)
indices = sorted(range(len(mod.feature_importances_)), key=lambda i: mod.feature_importances_[i])[-1*numFeatures:]

#Reduce Dimensions
reduceDimensions = False
if reduceDimensions:
    features_train = reduce_dimension(indices,features_train)
    print("Top "+str(numFeatures)+" Feature indices: ",indices)
    features_test = reduce_dimension(indices,features_test)


#TODO: Tune Parameters and Add learning Curves for tuned model
tuneParamaters = True
learningRates = [x/1000.0 for x in range(1,3000,40)]
if tuneParamaters:
    from sklearn.learning_curve import learning_curve
    from sklearn.cross_validation import ShuffleSplit
    from sklearn.grid_search import GridSearchCV

    cv = ShuffleSplit(features_train.shape[0], n_iter=10, test_size=0.2, random_state=0)
    #classifierParams = dict(gamma=gammas,C=[100000,100,1000,10000,10],kernel=['rbf','linear','poly','sigmoid'])
    classifierParams = dict(learning_rate=learningRates)
    print "Params: ", classifierParams
    classifier = GridSearchCV(estimator=ensemble.AdaBoostClassifier(), cv=cv, param_grid=classifierParams)
    classifier.fit(features_train, labels_train)
    gridScores = classifier.grid_scores_
    print "Grid Score: \n", gridScores
    for i in gridScores[0]:
        print "unpacking Tuple: ",i
    #title = 'Learning Curves (SVM, linear kernel, $\gamma=%.6f$, ' %classifier.best_estimator_.gamma + '$C=%.6f$)' %classifier.best_estimator_.C
    title = dataSet+' Learning Curves for Tuned Params (adaBoost, Best LR: '+ str(classifier.best_estimator_.learning_rate)+' )'
    print "Best Learning Rate: ", classifier.best_estimator_.learning_rate
    estimator = ensemble.AdaBoostClassifier(learning_rate=classifier.best_estimator_.learning_rate)
    plot_learning_curve(estimator, title, features_train, labels_train, cv=cv)
    plt.savefig('./Boosting/'+dataSet+' learning rate.png')
    plt.clf()
    plt.close()
#############    SVM Learner    #############
#TODO: change clf below so that it uses the best params
clf = ensemble.AdaBoostClassifier(learning_rate=classifier.best_estimator_.learning_rate)
t0 = time()


#building the classifier
clf.fit(features_train, labels_train)

print "Training time: ", round(time()-t0,3),'s'

t1 = time()

print "--------    Testing    --------"


y_pred = clf.predict(features_test) #Predicting the output of testing data

print "Prediction time: ", round(time()-t1,3), 's'

print "--------    Results    --------"
labels_test = testing_data[:,-1]
print "Classification Score: ", clf.score(features_test,labels_test)*100,"%"

print("\nClassification report for classifier %s:\n%s\n"
      % (clf, classification_report(labels_test, y_pred, target_names=target_names)))
confusionMatrix =metrics.confusion_matrix(labels_test, y_pred)
print("Confusion matrix:\n%s" % confusionMatrix)


#plot class distributions for testing Data
plt.hist(labels_test,bins=bin_count)
plt.xlabel("Classes")
plt.ylabel("Count")
plt.title(dataSet+" Sample Distribution of Testing Data")
plt.savefig('./Boosting/'+dataSet+ ' Sample Distribution of Testing Data.png')
plt.clf()
plt.close()

#Depth vs. Performance Plot ( TODO: This should be on the cross validation set instead of the testing Set which is already calculated above prior to constructing the learning curve)
tree_train, tree_test = np.zeros(len(learningRates)), np.zeros(len(learningRates))
count = 0
for i in learningRates:
    clf2 = ensemble.AdaBoostClassifier(learning_rate=i)
    clf2 = clf2.fit(features_train, labels_train)
    tree_train[count] = clf2.score(features_train,labels_train)
    tree_test[count] = gridScores[count][1]
    count +=1
plt.plot(learningRates,tree_test, linewidth=3, label = "Boosting CVtest Score")
plt.plot(learningRates,tree_train, linewidth=3, label = "Boosting train Score")
plt.legend()
plt.ylim(0.5, 1.01)
plt.xlabel("Learning Rate")
plt.ylabel("Score")
plt.title(dataSet+" Score vs. Learning Rate")
plt.savefig('./Boosting/'+dataSet+'Score vs. Learning Rate.png')
plt.clf()
plt.close()

#TODO: Add training vs. classification errors (Type I vs Type II)
#TODO: Vary Kernels and other SVM Parameters
#TODO: Add cross validation


#This can be used to show confidence
    #predict2 = clf.predict_proba(features_test)
    #print "Confidence :", np.asarray(predict2).max(axis=1)[0]
    #print "Result: ", y_pred[0], " Confidence: ", np.asarray(y_pred).max(axis=1)[0]*100,"%"