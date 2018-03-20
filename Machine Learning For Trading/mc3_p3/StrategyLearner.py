"""
Template for implementing StrategyLearner  (c) 2016 Tucker Balch
"""

import datetime as dt
import QLearner as ql
import pandas as pd
import util as ut
import numpy as np

class StrategyLearner(object):

    # constructor
    def __init__(self, verbose = False):
        self.verbose = verbose

    # this method should create a QLearner, and train it for trading
    def addEvidence(self, symbol = "IBM", \
        sd=dt.datetime(2008,1,1), \
        ed=dt.datetime(2009,1,1), \
        sv = 10000): 

        self.learner = ql.QLearner(num_actions=3, alpha=0.2, gamma=0.9, rar=0.9, radr=0.84, dyna=0, verbose=False)
        # add your code to do learning here

        #load the data
        portvals = get_data([symbol], pd.date_range(start_date, end_date))
        portvals = portvals[[symbol]]  # remove SPY
        portvals['SMA'] = np.nan
        portvals['BB'] = np.nan
        portvals['Momentum'] = np.nan
        portvals['KRI'] = np.nan

        for i in range(len(portvals)):
            if i>=19:
                SMA = portvals[symbol][i-19:i+1].mean()
                SDV = portvals[symbol][i-19:i+1].std()
                portvals['SMA'][i] = SMA
                portvals['BB'][i] = (portvals[symbol][i] - SMA)/ (2.0*SDV)
                portvals['Momentum'][i] = (portvals[symbol][i]/portvals[symbol][i-10]) - 1.0
                portvals['KRI'][i] = ((portvals[symbol][i] - SMA)/SMA)*10.0
        portvals.drop('SMA', axis=1, inplace=True)


        # example usage of the old backward compatible util function
        syms=[symbol]
        dates = pd.date_range(sd, ed)
        prices_all = ut.get_data(syms, dates)  # automatically adds SPY
        prices = prices_all[syms]  # only portfolio symbols
        prices_SPY = prices_all['SPY']  # only SPY, for comparison later
        if self.verbose: print prices
  
        # example use with new colname 
        volume_all = ut.get_data(syms, dates, colname = "Volume")  # automatically adds SPY
        volume = volume_all[syms]  # only portfolio symbols
        volume_SPY = volume_all['SPY']  # only SPY, for comparison later
        if self.verbose: print volume

    # this method should use the existing policy and test it against new data
    def testPolicy(self, symbol = "IBM", \
        sd=dt.datetime(2009,1,1), \
        ed=dt.datetime(2010,1,1), \
        sv = 10000):

        # here we build a fake set of trades
        # your code should return the same sort of data
        dates = pd.date_range(sd, ed)
        prices_all = ut.get_data([symbol], dates)  # automatically adds SPY
        trades = prices_all[[symbol,]]  # only portfolio symbols
        trades_SPY = prices_all['SPY']  # only SPY, for comparison later
	trades.values[:,:] = 0 # set them all to nothing
	trades.values[3,:] = 100 # add a BUY at the 4th date
	trades.values[5,:] = -100 # add a SELL at the 6th date 
	trades.values[6,:] = -100 # add a SELL at the 7th date 
	trades.values[8,:] = -100 # add a SELL at the 9th date
        if self.verbose: print type(trades) # it better be a DataFrame!
        if self.verbose: print trades
        if self.verbose: print prices_all
        return trades

if __name__=="__main__":
    print "One does not simply think up a strategy"
