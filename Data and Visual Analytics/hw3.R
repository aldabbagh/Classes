#aaldabbagh3
library(ggplot2)
library(GGally)
setwd("C:/Users/AMD/Desktop/Classes/GaTech/Data Visualization/HWs/HW3")

train <- read.csv('mnist_train.csv', header = FALSE)
test <- read.csv('mnist_test.csv', header = FALSE)

#Partitioning Training Set
train_0_1 = train[,train[785,]==0 | train[785,]==1]
train_3_5 = train[,train[785,]==5 | train[785,]==3]

#partitioning Testing Set
test_0_1 = test[,test[785,]==0 | test[785,]==1]
test_3_5 = test[,test[785,]==5 | test[785,]==3]

#Isolate label vectors
true_label_train_0_1 = train_0_1[785,]
train_0_1 = train_0_1[1:784,]

true_label_train_3_5 = train_3_5[785,]
train_3_5 = train_3_5[1:784,]

true_label_test_0_1 = test_0_1[785,]
test_0_1 = test_0_1[1:784,]

true_label_test_3_5 = test_3_5[785,]
test_3_5 = test_3_5[1:784,]

#creating image matrices
idx0 = which(true_label_train_0_1==0)[1]
class_0_image = apply(matrix(train_0_1[,idx0],28,28), 2, rev)

idx1 = which(true_label_train_0_1==1)[1]
class_1_image = apply(matrix(train_0_1[,idx1],28,28), 2, rev)

idx3 = which(true_label_train_3_5==3)[1]
class_3_image = apply(matrix(train_3_5[,idx3],28,28), 2, rev)

idx5 = which(true_label_train_3_5==5)[1]
class_5_image = apply(matrix(train_3_5[,idx5],28,28), 2, rev)

#plotting image matrices
heatmap(class_0_image,Rowv=NA,Colv=NA,col=paste("gray",1:99,sep=""))
heatmap(class_1_image,Rowv=NA,Colv=NA,col=paste("gray",1:99,sep=""))
heatmap(class_3_image,Rowv=NA,Colv=NA,col=paste("gray",1:99,sep=""))
heatmap(class_5_image,Rowv=NA,Colv=NA,col=paste("gray",1:99,sep=""))

#########       End of part0       #########

#part 2
insertRow <- function(existingDF, newrow, r) {
  existingDF[seq(r+1,nrow(existingDF)+1),] <- existingDF[seq(r,nrow(existingDF)),]
  existingDF[r,] <- newrow
  existingDF
}


logisticRegression = function(trainingSamples, labels, LR, threshold=1e-07, numIterations=100){
  #Add Bias to training set
  X = insertRow(trainingSamples, rep(1,ncol(trainingSamples)),1)
  thetas = runif(nrow(X), min = -1, max = 1)
  #transpose set so that each row is a sample
  X = t(X)
  numSamples = nrow(X)
  iteration = 1
  currentGradient = rep(9999999999999999999999999999999999999999999999999,ncol(X))
  gradients = rep(0,ncol(X))
  while (iteration<=numIterations){
    h = 1/(1+exp(-(X%*%(thetas) )) )
    for (i in seq(1,numSamples)){
      y = labels[1,i]
      Xi =X[i,]
      grad = (y-h[i])
      thetas = thetas + LR*grad*Xi
      
      
      #   Stopping criterion. For the newton-cg and lbfgs solvers, the iteration
      #   will stop when ``max{|g_i | i = 1, ..., n} <= tol``
      #   where ``g_i`` is the i-th component of the gradient.
      
      gradients[i] = grad-currentGradient[i]
      difference = max(abs(currentGradient))
      if (iteration==2){
        #browser()
      }
      if (difference<=threshold){
        return (thetas)
      }
      currentGradient[i] = grad
      #currentGradient[i] = currentGradient[1]

        
    }
    iteration = iteration+1
  }
  warning("might not have converged!")
  return (thetas)
  
}

logisticRegression2 = function(trainingSamples, labels, LR, threshold=1e-09, numIterations=100,shuffle=0,portions=1){
  #Add Bias to training set
  X = insertRow(trainingSamples, rep(1,ncol(trainingSamples)),1)
  #thetas = runif(nrow(X), min = -1, max = 1)
  thetas = rep(0,nrow(X))
  #transpose set so that each row is a sample
  X = t(X)
  labels = t(labels)
  if (shuffle==1){
    numRows = floor(portions*nrow(X))
    if (numRows<=1){
      numRows = 2
    }
    selectedSamples = sample(nrow(X), numRows)
    X = X[selectedSamples, ]
    labels = labels[selectedSamples,]
  }
  labels = unname(labels)
  numSamples = nrow(X)
  iteration = 1
  while (iteration<=numIterations){
    h = 1/(1+exp(-(X%*%(thetas) )) )
    for (i in seq(1,numSamples)){
      y = labels[i]
      Xi =X[i,]
      grad = (y-h[i])
      thetas = thetas + LR*grad*Xi
      
      #   Stopping criterion. is the number of iterations
    }
    iteration = iteration+1
  }
  return (thetas)
  
}

logisticRegression3 = function(trainingSamples, labels, LR, threshold=1e-09, numIterations=100,shuffle=0){
  #Add Bias to training set
  X = insertRow(trainingSamples, rep(1,ncol(trainingSamples)),1)
  #thetas = runif(nrow(X), min = -1, max = 1)
  thetas = rep(0,nrow(X))
  #transpose set so that each row is a sample
  X = t(X)
  labels = t(labels)
  if (shuffle==1){
    selectedSamples = sample(nrow(X), floor(0.85*nrow(X)))
    X = X[selectedSamples, ]
    labels = labels[selectedSamples,]
  }
  labels = unname(labels)
  numSamples = nrow(X)
  iteration = 1
  currentGradient = rep(9999999999999999999999999999999999999999999999999,ncol(X))
  gradients = rep(0,ncol(X))
  while (iteration<=numIterations){
    h = 1/(1+exp(-(X%*%(thetas) )) )
    for (i in seq(1,numSamples)){
      y = labels[i]
      Xi =X[i,]
      grad = (h[i]-y)
      thetas = thetas - LR*grad*Xi
      
      #   Stopping criterion. is the number of iterations
    }
    iteration = iteration+1
  }
  return (thetas)
  
}


#########       End of part2       #########

#change labels to 0 and 1
true_label_train_0_1[true_label_train_0_1==0] = 0
true_label_train_3_5[true_label_train_3_5==3] = 0
true_label_train_3_5[true_label_train_3_5==5] = 1


true_label_test_0_1[true_label_test_0_1==0] =0
true_label_test_3_5[true_label_test_3_5==3] =0
true_label_test_3_5[true_label_test_3_5==5] = 1


#### Predict on testing set ####

predictOnData = function(samples, thetas){
  #Add Bias to training set
  X = insertRow(samples, rep(1,ncol(samples)),1)
  #transpose set so that each row is a sample
  X = t(X)
  predictions = 1/(1+exp(-(X%*%(thetas) )) )
  
  return (predictions)
}


getAccuracy = function(predicted, actual){
  numSamples = ncol(actual)
  correct = 0
  for (i in seq(1,numSamples)){
    if (abs(round(predicted[i,1])-actual[1,i])==0){
      correct = correct+1
    }
  }
  return (correct/numSamples)
}

#train the model for 0_1 & 3_5
thetas_train_0_1 = logisticRegression2(train_0_1,true_label_train_0_1,0.01,numIterations = 10,shuffle=1)
thetas_train_3_5 = logisticRegression2(train_3_5,true_label_train_0_1,0.01,numIterations = 10,shuffle=1)

#this is the actual predicted values between 0 and 1. They will need to be rounded to get the right class label
raw_0_1_predictions = predictOnData(test_0_1,thetas_train_0_1)
raw_3_5_predictions = predictOnData(test_3_5,thetas_train_3_5)

accuracy_0_1 = getAccuracy(raw_0_1_predictions,true_label_train_0_1)
accuracy_3_5 = getAccuracy(raw_3_5_predictions,true_label_test_3_5)

##########################
thetas_train_0_1 = logisticRegression4(train_0_1,true_label_train_0_1,0.001)
raw_0_1_predictions = predictOnData(test_0_1,thetas_train_0_1)
accuracy_0_1 = getAccuracy(raw_0_1_predictions,true_label_test_0_1)


##########################

#run the tests 10 times
train_1_0_accuracies = rep(0,10)
test_1_0_accuracies = rep(0,10)
train_3_5_accuracies = rep(0,10)
test_3_5_accuracies = rep(0,10)


for (i in seq(1,10)){
  thetas_train_0_1 = logisticRegression2(train_0_1,true_label_train_0_1,0.0001,numIterations = 1,shuffle=1,portions = 0.85)
  thetas_train_3_5 = logisticRegression2(train_3_5,true_label_train_3_5,0.0001,numIterations = 1,shuffle=1,portions = 0.85)
  
  raw_0_1_predictions_train = predictOnData(train_0_1,thetas_train_0_1)
  raw_3_5_predictions_train = predictOnData(train_3_5,thetas_train_3_5)
  
  raw_0_1_predictions_test = predictOnData(test_0_1,thetas_train_0_1)
  raw_3_5_predictions_test = predictOnData(test_3_5,thetas_train_3_5)
  
  accuracy_0_1_train = getAccuracy(raw_0_1_predictions_train,true_label_train_0_1)
  accuracy_3_5_train = getAccuracy(raw_3_5_predictions_train,true_label_train_3_5)
  
  accuracy_0_1_test = getAccuracy(raw_0_1_predictions_test,true_label_test_0_1)
  accuracy_3_5_test = getAccuracy(raw_3_5_predictions_test,true_label_test_3_5)
  
  train_1_0_accuracies[i]=accuracy_0_1_train
  train_3_5_accuracies[i]=accuracy_3_5_train
  
  test_1_0_accuracies[i]=accuracy_0_1_test
  test_3_5_accuracies[i]=accuracy_3_5_test
}

train_1_0_accuracies
train_3_5_accuracies
test_1_0_accuracies
test_3_5_accuracies

mean(train_1_0_accuracies)
mean(train_3_5_accuracies)
mean(test_1_0_accuracies)
mean(test_3_5_accuracies)

### PLOTTING Learning Curves
portions = seq(1,20)/20.0

train_1_0_accuracies = rep(0,20)
test_1_0_accuracies = rep(0,20)
train_3_5_accuracies = rep(0,20)
test_3_5_accuracies = rep(0,20)

for (i in seq(1,20)){
  thetas_train_0_1 = logisticRegression2(train_0_1,true_label_train_0_1,0.001,numIterations = 10,shuffle=1,portions = portions[i])
  thetas_train_3_5 = logisticRegression2(train_3_5,true_label_train_3_5,0.001,numIterations = 10,shuffle=1,portions = portions[i])
  
  raw_0_1_predictions_train = predictOnData(train_0_1,thetas_train_0_1)
  raw_3_5_predictions_train = predictOnData(train_3_5,thetas_train_3_5)
  
  raw_0_1_predictions_test = predictOnData(test_0_1,thetas_train_0_1)
  raw_3_5_predictions_test = predictOnData(test_3_5,thetas_train_3_5)
  
  accuracy_0_1_train = getAccuracy(raw_0_1_predictions_train,true_label_train_0_1)
  accuracy_3_5_train = getAccuracy(raw_3_5_predictions_train,true_label_train_3_5)
  
  accuracy_0_1_test = getAccuracy(raw_0_1_predictions_test,true_label_test_0_1)
  accuracy_3_5_test = getAccuracy(raw_3_5_predictions_test,true_label_test_3_5)
  
  train_1_0_accuracies[i]=accuracy_0_1_train
  train_3_5_accuracies[i]=accuracy_3_5_train
  
  test_1_0_accuracies[i]=accuracy_0_1_test
  test_3_5_accuracies[i]=accuracy_3_5_test
}

train_1_0_accuracies
train_3_5_accuracies
test_1_0_accuracies
test_3_5_accuracies

learning_curve_3_5 = data.frame(portions,train_3_5_accuracies,test_3_5_accuracies)
learning_curve_0_1 = data.frame(portions,train_1_0_accuracies,test_1_0_accuracies)

ggplot(learning_curve_3_5, aes(learning_curve_3_5$portions)) + 
  geom_line(aes(y = learning_curve_3_5$train_3_5_accuracies, color ="Train")) + 
  geom_line(aes(y = learning_curve_3_5$test_3_5_accuracies,color= "Test"))+
  xlab("Percent of Data (%)")+
  ylab("Percent of Correctly Classified Samples (%)")+
  ggtitle("3 & 5 Classes Learning Curves")

ggplot(learning_curve_0_1, aes(learning_curve_0_1$portions)) + 
  geom_line(aes(y = learning_curve_0_1$train_1_0_accuracies, color ="Train")) + 
  geom_line(aes(y = learning_curve_0_1$test_1_0_accuracies,color= "Test"))+
  xlab("Percent of Data (%)")+
  ylab("Percent of Correctly Classified Samples (%)")+
  ggtitle("1 & 0 Classes Learning Curves")

### PLOTTING negative loss
getlosses = function(predictions, labels){
  numSamples = ncol(labels)
  loss = 0
  countedpoints = 0
  for (i in seq(1,ncol(labels))){
    y = labels[1,i]
    h = predictions[i,1]
    if (y==1){
      loss = loss+log(h)
    }
    if (y==0){
      loss = loss+log(1-h)
    }
    #loss = loss+(y*log(h)+(1-y)*log(1-h))
  }
  return (loss/numSamples)
}

portions = seq(1,20)/20.0

train_1_0_accuracies = rep(0,20)
test_1_0_accuracies = rep(0,20)
train_3_5_accuracies = rep(0,20)
test_3_5_accuracies = rep(0,20)

for (i in seq(1,20)){
  thetas_train_0_1 = logisticRegression2(train_0_1,true_label_train_0_1,0.001,numIterations = 10,shuffle=1,portions = portions[i])
  thetas_train_3_5 = logisticRegression2(train_3_5,true_label_train_3_5,0.001,numIterations = 10,shuffle=1,portions = portions[i])
  
  raw_0_1_predictions_train = predictOnData(train_0_1,thetas_train_0_1)
  raw_3_5_predictions_train = predictOnData(train_3_5,thetas_train_3_5)
  
  raw_0_1_predictions_test = predictOnData(test_0_1,thetas_train_0_1)
  raw_3_5_predictions_test = predictOnData(test_3_5,thetas_train_3_5)
  
  accuracy_0_1_train = getlosses(raw_0_1_predictions_train,true_label_train_0_1)
  accuracy_3_5_train = getlosses(raw_3_5_predictions_train,true_label_train_3_5)
  
  accuracy_0_1_test = getlosses(raw_0_1_predictions_test,true_label_test_0_1)
  accuracy_3_5_test = getlosses(raw_3_5_predictions_test,true_label_test_3_5)
  
  train_1_0_accuracies[i]=accuracy_0_1_train
  train_3_5_accuracies[i]=accuracy_3_5_train
  
  test_1_0_accuracies[i]=accuracy_0_1_test
  test_3_5_accuracies[i]=accuracy_3_5_test
}


train_1_0_accuracies
train_3_5_accuracies
test_1_0_accuracies
test_3_5_accuracies

train_1_0_accuracies2= train_1_0_accuracies
train_3_5_accuracies2= train_3_5_accuracies
test_1_0_accuracies2= test_1_0_accuracies
test_3_5_accuracies2= test_3_5_accuracies

train_1_0_accuracies = (train_1_0_accuracies+train_1_0_accuracies2)/2.0
train_3_5_accuracies = (train_3_5_accuracies+train_3_5_accuracies2)/2.0
test_1_0_accuracies =  (test_1_0_accuracies+test_1_0_accuracies2)/2.0
test_3_5_accuracies = (test_3_5_accuracies+test_3_5_accuracies2)/2.0

learning_curve_3_5 = data.frame(portions,train_3_5_accuracies,test_3_5_accuracies)
learning_curve_0_1 = data.frame(portions,train_1_0_accuracies,test_1_0_accuracies)

ggplot(learning_curve_3_5, aes(learning_curve_3_5$portions)) + 
  geom_line(aes(y = learning_curve_3_5$train_3_5_accuracies, color ="Train")) + 
  geom_line(aes(y = learning_curve_3_5$test_3_5_accuracies,color= "Test"))+
  xlab("Percent of Data (%)")+
  ylab("Loss of J(theta)")+
  ggtitle("3 & 5 Classes Loss Curves")

ggplot(learning_curve_0_1, aes(learning_curve_0_1$portions)) + 
  geom_line(aes(y = learning_curve_0_1$train_1_0_accuracies, color ="Train")) + 
  geom_line(aes(y = learning_curve_0_1$test_1_0_accuracies,color= "Test"))+
  xlab("Percent of Data (%)")+
  ylab("Loss of J(theta)")+
  ggtitle("1 & 0 Classes Loss Curves")