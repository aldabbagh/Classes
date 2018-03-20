"""
Test a Strategy Learner.  (c) 2016 Tucker Balch
"""

import pandas as pd
import datetime as dt
import util as ut
import StrategyLearner as sl

def test_code(verb = True):

    # instantiate the strategy learner
    learner = sl.StrategyLearner(verbose = verb)

    # set parameters for training the learner
    sym = "GOOG"
    stdate =dt.datetime(2008,1,1)
    enddate =dt.datetime(2008,1,15) # just a few days for "shake out"

    # train the learner
    learner.addEvidence(symbol = sym, sd = stdate, \
        ed = enddate, sv = 10000) 

    # set parameters for testing
    sym = "IBM"
    stdate =dt.datetime(2009,1,1)
    enddate =dt.datetime(2009,1,15)

    # get some data for reference
    syms=[sym]
    dates = pd.date_range(stdate, enddate)
    prices_all = ut.get_data(syms, dates)  # automatically adds SPY
    prices = prices_all[syms]  # only portfolio symbols
    if verb: print prices

    # test the learner
    df_trades = learner.testPolicy(symbol = sym, sd = stdate, \
        ed = enddate, sv = 10000)

    # a few sanity checks
    # df_trades should be a single column DataFrame (not a series)
    # including only the values 100, 0, -100
    if isinstance(df_trades, pd.DataFrame) == False:
        print "Returned result is not a DataFrame"
    if prices.shape != df_trades.shape:
        print "Returned result is not the right shape"
    tradecheck = abs(df_trades.cumsum()).values
    tradecheck[tradecheck<=100] = 0
    tradecheck[tradecheck>0] = 1
    if tradecheck.sum(axis=0) > 0:
        print "Returned result violoates holding restrictions (more than 100 shares)"

    if verb: print df_trades
    # we will add code here to evaluate your trades

if __name__=="__main__":
    test_code(verb = False)
