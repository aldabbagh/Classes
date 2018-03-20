"""
Template for implementing QLearner  (c) 2015 Tucker Balch
"""

import numpy as np
import random as rand

class QLearner(object):

    def __init__(self, \
        num_states=100, \
        num_actions = 4, \
        alpha = 0.2, \
        gamma = 0.9, \
        rar = 0.5, \
        radr = 0.99, \
        dyna = 0, \
        verbose = False):

        # initialize default parameters
        self.verbose = verbose
        self.num_actions = num_actions
        self.s = 0
        self.a = 0
        self.num_states=num_states
        self.num_actions = num_actions
        self.alpha = alpha
        self.gamma = gamma
        self.rar = rar
        self.radr = radr
        self.dyna = dyna

        # contruct the Q[s,a] table.
        # I will use 's' in the rows and 'a' in the columns with uniform random values between -1.0 and 1.0 as per the
        # the project specification
        row = num_states
        column = num_actions
        Q_table = np.random.uniform(-1.0,1.0,size=(row,column))
        self.Q_table = Q_table

        self.inside = False
        self.index = 0
        self.experience = {}

    def querysetstate(self, s):
        """
        @summary: Update the state without updating the Q-table
        @param s: The new state
        @returns: The selected action
        """
        self.s = s
        # find the 'a' that maximizes Q[s,a]
        action = self.Q_table[s, :].argmax()
        #choose either random action or optimal 'a' for Q[s,a]
        p = np.random.uniform(0.0, 1.0)
        if p<=self.rar:
            action = rand.randint(0, self.num_actions-1)

        if self.verbose: print "s =", s,"a =",action
        self.a = action
        return action

    def genjutsu(self):
        self.inside = True
        for i in range(self.dyna):
            len_experiences = len(self.experience)
            instance = rand.randint(0, len_experiences-1)
            self.s = self.experience[instance][0]
            self.a = self.experience[instance][1]
            self.query(self.experience[instance][2],self.experience[instance][3])

    def query(self,s_prime,r):
        """
        @summary: Update the Q table and return an action
        @param s_prime: The new state
        @param r: The ne state
        @returns: The selected action
        """

        # temp = [s,a,s',r]
        state = self.s
        act = self.a



        if self.dyna:
            if not self.inside:
                temp = (self.s,self.a,s_prime,r)
                self.experience[self.index] = temp
                self.index += 1
                self.genjutsu()
                self.inside = False

        self.s = state
        self.a = act

        act = self.a

        later_rewards = (self.Q_table[s_prime, self.Q_table[s_prime,:].argmax()])

        improved_estimate = r+self.gamma*later_rewards

        self.Q_table[self.s,act] = (1-self.alpha)*self.Q_table[self.s,act]\
                              +self.alpha*improved_estimate
        # find the 'a' that maximizes Q[s,a]
        action = self.Q_table[s_prime, :].argmax()
        #choose either random action or optimal 'a' for Q[s,a]
        p = np.random.uniform(0.0, 1.0)
        if p<=self.rar:
            action = rand.randint(0, self.num_actions-1)
            self.rar = self.rar*self.radr

        self.s = s_prime
        if self.verbose: print "s =", s_prime,"a =",action,"r =",r
        self.a = action
        return action

if __name__=="__main__":
    print "Remember Q from Star Trek? Well, this isn't him"
