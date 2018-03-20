"""MC1-P2: Optimize a portfolio."""

import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
import datetime as dt
from util import get_data, plot_data
import scipy.optimize as spo

# This is the function that will be tested by the autograder
# The student must update this code to properly implement the functionality
def optimize_portfolio(sd=dt.datetime(2008,1,1), ed=dt.datetime(2009,1,1), \
    syms=['GOOG','AAPL','GLD','XOM'], gen_plot=False):

    # Read in adjusted closing prices for given symbols, date range
    dates = pd.date_range(sd, ed)
    prices_all = get_data(syms, dates)  # automatically adds SPY
    prices = prices_all[syms]  # only portfolio symbols
    prices_SPY = prices_all['SPY']  # only SPY, for comparison later

    # Get daily portfolio value
    port_val = prices_SPY # add code here to compute daily portfolio values

    normed = prices/prices.ix[0]

    start_val = 1.0 # can be anything
    # define the sharpie function
    def sharpie_ratio(allocs):
        normed = prices/prices.ix[0]
        alloced = normed*allocs
        pos_vals = alloced*start_val
        port_val = pos_vals.sum(axis=1)
        rfr = 0.0
        sf = 252.0
        cr = (port_val[-1]/port_val[0])-1

        dr = port_val.copy()
        dr.ix[1:] = (port_val[1:] / port_val[:-1].values)-1
        dr.ix[:1] = 0
        dr = dr[1:]

        adr = dr.mean()
        sddr = dr.std()

        k = sf**0.5
        sr = k*(dr-rfr).mean()/sddr
        return -1*sr



    # find the allocations for the optimal portfolio
    # note that the values here ARE NOT meant to be correct for a test case

    allocs = (1.0/len(syms))*np.ones(len(syms))
    #print allocs
    #allocs = np.asarray([0.2, 0.2, 0.3, 0.3, 0.0]) # add code here to find the allocations

    bound = []
    for i in range(len(syms)):
        bound.append((0.0,1.0))


    min_allocs = spo.minimize(sharpie_ratio, allocs, method='SLSQP', options = {'disp':False}, bounds=tuple(bound) ,
                              constraints = ({ 'type': 'eq', 'fun': lambda inputs: 1.0 - np.sum(inputs) }))

    cr, adr, sddr, sr = [0.25, 0.001, 0.0005, 2.1] # add code here to compute stats


    allocs = min_allocs.x # update allocations

    normed = prices/prices.ix[0]
    alloced = normed*allocs
    pos_vals = alloced*start_val
    port_val = pos_vals.sum(axis=1)
    rfr = 0.0
    sf = 252.0
    cr = (port_val[-1]/port_val[0])-1

    dr = port_val.copy()
    dr.ix[1:] = (port_val[1:] / port_val[:-1].values)-1
    dr.ix[:1] = 0
    dr = dr[1:]

    adr = dr.mean()
    sddr = dr.std()

    k = sf**0.5
    sr = k*(dr-rfr).mean()/sddr


    # Compare daily portfolio value with SPY using a normalized plot
    if gen_plot:
        df_temp = pd.concat([port_val/port_val.ix[0], prices_SPY/prices_SPY.ix[0]], keys=['Portfolio', 'SPY'], axis=1)
        ax = df_temp.plot(title="Daily portfolio value and SPY", fontsize=12)
        ax.set_xlabel("Date")
        ax.set_ylabel("Normalized price")
        plt.grid()
        plt.savefig("comparison_optimal.png")
        plt.show()


    return allocs, cr, adr, sddr, sr



if __name__ == "__main__":
    # This code WILL NOT be tested by the auto grader
    # It is only here to help you set up and test your code

    # Define input parameters
    # Note that ALL of these values will be set to different values by
    # the autograder!

    start_date = dt.datetime(2009,1,1)
    end_date = dt.datetime(2010,12,31)
    symbols = ['IBM', 'AAPL','HNZ', 'XOM', 'GLD']

    # Assess the portfolio
    allocations, cr, adr, sddr, sr = optimize_portfolio(sd = start_date, ed = end_date,\
        syms = symbols, \
        gen_plot = True)

    # Print statistics
    print "Start Date:", start_date
    print "End Date:", end_date
    print "Symbols:", symbols
    print "Allocations:", allocations
    print "Sharpe Ratio:", sr
    print "Volatility (stdev of daily returns):", sddr
    print "Average Daily Return:", adr
    print "Cumulative Return:", cr