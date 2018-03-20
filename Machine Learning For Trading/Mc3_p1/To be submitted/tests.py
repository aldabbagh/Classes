import unittest
import numpy as np
import KNNLearner as knn

from sklearn import neighbors


class KNNRippleTest(unittest.TestCase):
    def setUp(self):
        self.rip = np.genfromtxt("Data/ripple.csv", delimiter=",")
        np.random.shuffle(self.rip)
        self.slice = np.floor(self.rip.shape[0] * .6)
        train,test = self.rip[:self.slice], self.rip[self.slice:]
        self.xtrain = train[:,0:train.shape[1]-1]
        self.ytrain = train[:,train.shape[1]-1]
        self.xtest = test[:, 0:test.shape[1]-1]
        self.ytest = test[:,test.shape[1]-1]

def t_generator(k):
    def t(self):
        sknn = neighbors.KNeighborsRegressor(k)
        learner = knn.KNNLearner(k, False)
        ly = learner.addEvidence(self.xtrain, self.ytrain)

        sy = sknn.fit(self.xtrain, self.ytrain).predict(self.xtest)
        y = learner.query(self.xtest)
        rmse = np.linalg.norm(sy - y) / np.sqrt(len(sy))
        self.assertLess(rmse, 1e-7)
    return t

for k in np.linspace(1,10,10,dtype=int):
    t = t_generator(k)
    name = "test_k{0}".format(k)
    setattr(KNNRippleTest, name, t)

if __name__ == "__main__":
    unittest.main()