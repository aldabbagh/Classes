"""MC1-P1: Analyze a portfolio."""

import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
import datetime as dt
from util import get_data, plot_data
import datetime as dt

# This is the function that will be tested by the autograder
# The student must update this code to properly implement the functionality
def assess_portfolio(sd, ed ,syms = ['GOOG','AAPL','GLD','XOM'], allocs=[0.1,0.2,0.3,0.4],  sv=1000000,gen_plot,port_val):

    # Read in adjusted closing prices for given symbols, date range
    dates = pd.date_range(sd, ed)
    prices_all = get_data(syms, dates)  # automatically adds SPY
    prices = prices_all[syms]  # only portfolio symbols
    prices_SPY = prices_all['$SPX']  # only SPY, for comparison later

    # Get daily portfolio value

    # Compare daily portfolio value with SPY using a normalized plot
    if gen_plot:
        # add code to plot here
        df_temp = pd.concat([port_val/port_val.ix[0], prices_SPY/prices_SPY.ix[0]], keys=['Portfolio', '$SPX'], axis=1)
        ax = df_temp.plot(title="Daily portfolio value and $SPX", fontsize=12)
        ax.set_xlabel("Date")
        ax.set_ylabel("Normalized price")
        plt.grid()
        #plt.savefig("plot.png")
        plt.show()


    # Add code here to properly compute end value
    return port_val[-1]


