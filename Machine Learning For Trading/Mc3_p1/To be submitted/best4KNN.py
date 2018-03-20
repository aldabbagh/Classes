import numpy as np


data = np.zeros((1000,3))
x1 = np.arange(start=-20, stop=20, step=0.04)
x2 = 2*np.arange(start=-20, stop=20, step=0.04)
y = np.sin((x1**2+x2**2)**0.5)


data[:,0] = x1
data[:,1] = x2
data[:,2] = y
np.random.shuffle(data)


np.savetxt("./Data/best4KNN.csv", data, delimiter=",")
