"""
A simple wrapper for linear regression.  (c) 2015 Tucker Balch
"""

import numpy as np
#import KNNLearner as knn
#import LinRegLearner as lrl

class BagLearner(object):

    def __init__(self, learner, kwargs = {"k":3}, bags = 20, boost = False, verbose = False):

        self.learner = learner
        self.kwargs = kwargs
        self.bags = bags
        self.learners = []
        for i in range(0,bags):
            self.learners.append(learner(**kwargs))
        self.boost = boost
        self.verbose = verbose


    def addEvidence(self,dataX,dataY):
        """
        @summary: Add training data to learner
        @param dataX: X values of data to add
        @param dataY: the Y training values
        """


        # Add training data to each learner
        for learner in self.learners:
            # generate random samples of data assuming X and Y have the same dimensions
            samples = np.random.randint(len(dataX),size=len(dataX))
            # I made the mistake of generating x and y samples independently
            learner.addEvidence(dataX[samples],dataY[samples])

    def query(self,points):
        """
        @summary: Estimate a set of test points given the model we built.
        @param points: should be a numpy array with each row corresponding to a specific query.
        @returns the estimated values according to the saved model.
        """
        num_points = points.shape[0]

        prediction = np.ones((num_points,self.bags))

        j = 0
        for learner in self.learners:
            prediction[:,j] = learner.query(points)
            j+=1

        prediction = np.mean(prediction,axis =1)


        return prediction

if __name__=="__main__":
    print "the secret clue is 'zzyzx'"
