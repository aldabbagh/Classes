"""MC2-P1: Market simulator."""

import pandas as pd
import numpy as np
import datetime as dt
import os
from util import get_data, symbol_to_path, plot_data


def get_sorted_values(dict_of_values,dates):
    list_of_values = []
    for date in dates:
        list_of_values.append(dict_of_values[date])

    return list_of_values


def get_repeated_dates(records):
    dates = records.index
    dictionary = {}
    for date in dates:
        dictionary[date] = []
    for i,date in enumerate(dates):
        dictionary[date].append(i)
    return dictionary

def get_stocks(balance,date,data):
    value = 0

    for company in balance:
        price = data.ix[date,company]
        shares = balance[company]
        value+= price * shares
    return value

def get_shorts_longs(balance,prices,date):
    shorts = 0.
    longs = 0.

    for company in balance:
        stocks = balance[company]
        price = prices.ix[date,company]
        if stocks  >= 0:
            longs += price*stocks
        else:
            shorts -= price*stocks
    return shorts,longs

def get_leverage_with_order(account, prices, date, company, order, shares, price, cash):
    balance = {}
    for symbol in account:
        balance[symbol] = account[symbol]
    cash = float(cash)
    if order == 'BUY':
        cash -= shares*price
        balance[company] += shares

    elif order == 'SELL':
        cash += shares*price
        balance[company]-= shares

    shorts,longs = get_shorts_longs(balance,prices,date)


    leverage = (longs + shorts) / (longs - shorts + cash)
    return leverage

def compute_portvals(orders_file = "./orders/orders.csv", start_val = 10000):
    # this is the function the autograder will call to test your code
    # TODO: Your code here

    # In the template, instead of computing the value of the portfolio, we just
    # read in the value of IBM over 6 months
    start_date = dt.datetime(2008,1,1)
    end_date = dt.datetime(2008,6,1)
    portvals = get_data(['IBM'], pd.date_range(start_date, end_date))
    portvals = portvals[['IBM']]  # remove SPY
    orders = pd.read_csv(orders_file, index_col='Date',
                parse_dates=True, usecols=['Date', 'Symbol', 'Order', 'Shares'], na_values=['nan'])
    orders.sort_index()

    cash = start_val
    start_date = orders.index.min()
    end_date = orders.index.max()

    stock_list = []
    number_stocks = []
    for i in range(len(orders)):
        temp = orders.ix[i][0]
        if temp not in stock_list:
            stock_list.append(temp)
            number_stocks.append(0)
    stocks = get_data(stock_list,pd.date_range(start_date, end_date))
    portvals=stocks['SPY'].copy().to_frame()

    stocks = stocks[stock_list]


    number_of_current_stocks = dict(zip(stock_list,number_stocks))
    #order_index = 0


    #print portvals.ix[0]
    def leverage(cash, number_of_current_stocks,action, stock, stocks, share):
        #sl = {'short':0, 'long':0}
        num_stocks = dict(number_of_current_stocks)
        longs = 0.0
        shorts = 0.0
        lev = 0.0
        if action == 'BUY':
            num_stocks[stock] += share
            stock_price = stocks[stock][day]
            cash -= share*stock_price
        else:
            num_stocks[stock] -= share
            cash += share*stocks[stock][day]

        for key, value in num_stocks.iteritems():
            if value > 0:
                longs += value*stocks[key][day]
            else:
                shorts += value*stocks[key][day]

        lev = (longs + abs(shorts)) / ((longs) - (abs(shorts)) + cash)
        return lev

    for i in range(len(portvals)):
        day_value = cash
        day = portvals.index[i]
        for order_index in range(len(orders)):
            if day == orders.index[order_index]:
                stock = orders.ix[order_index][0]
                action = orders.ix[order_index][1]
                share = orders.ix[order_index][2]
                if leverage(cash, number_of_current_stocks,action, stock, stocks, share)<=2.0:
                    if action == 'BUY':
                        number_of_current_stocks[stock] += share
                        stock_price = stocks[stock][day]
                        cash -= share*stock_price
                    else:
                        number_of_current_stocks[stock] -= share
                        cash += share*stocks[stock][day]

        day_value = cash
        for key, value in number_of_current_stocks.iteritems():
            day_value += value*stocks[key][day]

        portvals.ix[i] = day_value

    #print stocks['AAPL'][portvals.index[0]]


    return portvals



def get_stats(port_val):
    rfr = 0.0
    sf = 252.0
    #daily_returns = pd.DataFrame()
    sv = port_val.iloc[0]

    daily_returns = port_val.copy()
    daily_returns[1:] = (port_val[1:]/port_val[:-1].values) - 1
    daily_returns.iloc[0] = 0

    daily_risk_free_rate = rfr#((1.0+rfr)**(1/sf))-1



    # Get portfolio statistics (note: std_daily_ret = volatility)
    cr = port_val[-1]/sv -1
    adr = daily_returns[1:].mean()
    sddr = daily_returns[1:].std()
    #cr, adr, sddr = [port_val[-1]/port_val[1], daily_returns[1:].mean(), daily_returns[1:].stdev()] # add code here to compute stats
    sr = (sf**(1/2.))*(daily_returns[1:]-daily_risk_free_rate).mean()/sddr


    return cr,adr,sddr,sr


def get_stats_for_dataframe(df,company):

    df_temp = pd.read_csv(symbol_to_path(company), index_col='Date',
                parse_dates=True, usecols=['Date', 'Adj Close'], na_values=['nan'])
    df_temp = df_temp.rename(columns={'Adj Close': company})
    df = df.join(df_temp,how = 'left')

    fund_cr,fund_adr,fund_sddr,fund_sr = get_stats(df['SPY'])
    SPY_cr,SPY_adr,SPY_sddr,SPY_sr = get_stats(df[company])
    return fund_cr,fund_adr,fund_sddr,fund_sr,SPY_cr,SPY_adr,SPY_sddr,SPY_sr

def test_code():
    # this is a helper function you can use to test your code
    # note that during autograding his function will not be called.
    # Define input parameters

    #of = "./orders/orders2.csv"
    of = './orders/my_order.csv'
    sv = 10000
    a,b,c,d,e,f,g,h = 0.,0.,0.,0.,0.,0.,0.,0.
    # Process orders
    portvals = compute_portvals(orders_file = of, start_val = sv)
    if isinstance(portvals, pd.DataFrame):
        a,b,c,d,e,f,g,h = get_stats_for_dataframe(portvals,'$SPX')
        start_date = portvals.index.min()
        end_date = portvals.index.max()
        portvals = portvals[portvals.columns[0]] # just get the first column
    else:
        "warning, code did not return a DataFrame"
    
    # Get portfolio stats
    # Here we just fake the data. you should use your code from previous assignments.

    cum_ret, avg_daily_ret, std_daily_ret, sharpe_ratio = [a,b,c,d]#[0.2,0.01,0.02,1.5]
    cum_ret_SPY, avg_daily_ret_SPY, std_daily_ret_SPY, sharpe_ratio_SPY = [e,f,g,h]#[0.2,0.01,0.02,1.5]

    # Compare portfolio against $SPX
    print "Date Range: {} to {}".format(start_date, end_date)
    print
    print "Sharpe Ratio of Fund: {}".format(sharpe_ratio)
    print "Sharpe Ratio of SPY : {}".format(sharpe_ratio_SPY)
    print
    print "Cumulative Return of Fund: {}".format(cum_ret)
    print "Cumulative Return of SPY : {}".format(cum_ret_SPY)
    print
    print "Standard Deviation of Fund: {}".format(std_daily_ret)
    print "Standard Deviation of SPY : {}".format(std_daily_ret_SPY)
    print
    print "Average Daily Return of Fund: {}".format(avg_daily_ret)
    print "Average Daily Return of SPY : {}".format(avg_daily_ret_SPY)
    print
    print "Final Portfolio Value: {}".format(portvals[-1])

if __name__ == "__main__":
    test_code()
