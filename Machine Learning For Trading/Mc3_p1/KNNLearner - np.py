"""
A simple wrapper for linear regression.  (c) 2015 Tucker Balch
"""

import numpy as np

class KNNLearner(object):

    def __init__(self, k=3, verbose = False):

        self.K = k


    def addEvidence(self,dataX,dataY):
        """
        @summary: Add training data to learner
        @param dataX: X values of data to add
        @param dataY: the Y training values
        """

        # slap on 1s column so linear regression finds a constant term

        # build and save the model
        self.trainX = dataX
        self.trainY = dataY


    def query(self,points):
        """
        @summary: Estimate a set of test points given the model we built.
        @param points: should be a numpy array with each row corresponding to a specific query.
        @returns the estimated values according to the saved model.
        """
        K = self.K
        trainY = self.trainY
        trainX = self.trainX

        prediction = np.ones(len(points))
        distance = np.zeros(trainY.shape[0])
        for j in np.arange(0,points.shape[0]):
            point = points[j]
            for i in np.arange(0,trainX.shape[0]):
                distance[i] = np.sum((trainX[i]-point)**2)**0.5

            prediction[j] = np.mean(trainY[distance.argsort()][:K])
            distance[:] = 0.0



        return prediction

if __name__=="__main__":
    print "the secret clue is 'zzyzx'"
