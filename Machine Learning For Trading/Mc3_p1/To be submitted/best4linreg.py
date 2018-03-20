import numpy as np


data = np.zeros((1000,3))
x1 = np.arange(start=0, stop=100, step=0.1)
x2 = 2*np.arange(start=1, stop=101, step=0.1)
y = x1+x2

data[:,0] = x1
data[:,1] = x2
data[:,2] = y
np.random.shuffle(data)

np.savetxt("./Data/best4linreg.csv", data, delimiter=",")
