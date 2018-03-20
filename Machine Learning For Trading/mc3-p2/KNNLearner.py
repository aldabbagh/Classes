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

        prediction = np.ones(len(points))

        j = 0
        for point in points:
            distance = np.sqrt(((self.trainX-point)**2.0).sum(axis=1))

            prediction[j] = np.mean(self.trainY[distance.argsort()][:K])
            j += 1

        return prediction

if __name__=="__main__":
    print "the secret clue is 'zzyzx'"
