import pandas as pd
import numpy as np
import datetime as dt
import os
from util import get_data, plot_data
import matplotlib.pyplot as plt
from marketsim import compute_portvals
import KNNLearner as knn
import math

def my_strategy(orders_file = "./orders/orders.csv", start_val = 1000000, symbol='IBM',start_date=dt.datetime(2007,12,31),
    end_date=dt.datetime(2009,12,31)):

    # Do not toggle this
    Plot = False
    portvals = get_data([symbol], pd.date_range(start_date, end_date))
    portvals = portvals[[symbol]]  # remove SPY
    portvals['SMA'] = np.nan
    portvals['BB'] = np.nan
    portvals['Momentum'] = np.nan
    portvals['KRI'] = np.nan
    portvals['Y'] = np.nan

    for i in range(len(portvals)):
        if i<len(portvals)-5:
            portvals['Y'][i] = (portvals[symbol][i+5]/portvals[symbol][i])-1.0
        if i>=19:
            SMA = portvals[symbol][i-19:i+1].mean()
            SDV = portvals[symbol][i-19:i+1].std()
            portvals['SMA'][i] = SMA
            portvals['BB'][i] = (portvals[symbol][i] - SMA)/ (2.0*SDV)
            portvals['Momentum'][i] = (portvals[symbol][i]/portvals[symbol][i-10]) - 1.0
            portvals['KRI'][i] = ((portvals[symbol][i] - SMA)/SMA)*10.0
    #portvals = portvals[19:]
    if Plot:
        ax = portvals.plot(title="My Strategy", fontsize=12)
        ax.set_xlabel("Date")
        ax.set_ylabel("Price")
        plt.grid()
        #plt.savefig("plot.png")
        plt.show()

    return portvals

sd = dt.datetime(2007,12,31)
ed = dt.datetime(2009,12,31)
symb = 'ML4T-220'
training_data = my_strategy(symbol=symb, start_date= sd, end_date= ed)

testing_data_in_sample = my_strategy(symbol=symb, start_date= sd, end_date= ed)
testing_data_in_sample['Predicted Y'] = np.nan


orders = testing_data_in_sample.copy()
orders['Order'] = 'nan'
orders['Symbol'] = symb
orders['action'] = 'nan'
orders['Shares'] = np.nan
orders['Date'] = orders.index

testing_data_in_sample['actual future price'] = np.nan
testing_data_in_sample['predicted future price'] = np.nan


trainX = training_data.dropna()

trainY = training_data.dropna()

trainX = trainX.as_matrix(columns = ['BB','Momentum','KRI'])


trainY = trainY.as_matrix(columns = ['Y'])
trainY = trainY.flatten()


learner = knn.KNNLearner(k=3, verbose = True) # create a LinRegLearner
learner.addEvidence(trainX, trainY)

predY = learner.query(trainX) # get the predictions
rmse = math.sqrt(((trainY - predY) ** 2).sum()/trainY.shape[0])

print "In sample results"
print "RMSE: ", rmse
c = np.corrcoef(predY, y=trainY)
print "corr: ", c[0,1]


Entryl = False
Entrys = False

for i in range(len(orders)):
    if i>= 19 and i<len(orders)-5:
        xsample = orders.as_matrix(columns = ['BB','Momentum','KRI'])[i]
        #print xsample
        ypredict = learner.query([xsample])
        #print ypredict
        testing_data_in_sample['Predicted Y'][i] = ypredict
        orders['Predicted Y'][i] = ypredict
        testing_data_in_sample['predicted future price'][i] = testing_data_in_sample[symb][i]*ypredict + testing_data_in_sample[symb][i]
        testing_data_in_sample['actual future price'][i] = testing_data_in_sample[symb][i]*testing_data_in_sample['Y'][i] + testing_data_in_sample[symb][i]
        if ypredict >= 0.01 and not Entryl and not Entrys:
            print 'here'
            orders['Order'][i] = 'BUY'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'long entry'
            Entryl = True
            Entrys = False

        elif ypredict <= -0.01 and not Entryl and not Entrys:
            #print 'here1'
            orders['Order'][i] = 'SELL'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'short entry'
            Entrys = True
            Entryl = False

        elif ypredict <= -0.01 and Entryl and not Entrys:
            #print 'here2'
            orders['Order'][i] = 'SELL'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'long exit and short entry'
            Entryl = False
            Entrys = True

        elif ypredict>=0.01 and not Entryl and Entrys:
            #print 'here3'
            orders['Order'][i] = 'BUY'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'short exit and long entry'
            Entrys = False
            Entryl = True

#print orders.dropna()

to_csv = orders.dropna()

ax = training_data[symb].plot(title = 'My Strategy ML4T in Sample Prices', label= 'ML4T-220 Price', fontsize=12, legend=True)
ax.set_xlabel("Date")
ax.set_ylabel("Price")
testing_data_in_sample['predicted future price'].plot(color = 'goldenrod', label = 'predicted future price', legend=True)
testing_data_in_sample['actual future price'].plot(color = 'black', label = 'actual future price', legend=True)

plt.savefig("My Strat ML4T in Sample.png", dpi=200)
#my_strat['KRI'].plot( color = 'mediumturquoise', label = 'KRI', legend=True)
#my_strat['Lower'].plot( color = 'mediumturquoise', label = 'Bollinger Band')
plt.show()
plt.close()

ax2 = training_data[symb].plot(title = 'My Strategy ML4T in Sample Entry Exit', fontsize=12, legend=True)
ax2.set_xlabel("Date")
ax2.set_ylabel("Price")

for i in range(len(to_csv)):
    if to_csv['action'][i] == 'short entry':
        plt.axvline(x=to_csv['Date'][i], color = 'red')
    elif to_csv['action'][i] == 'short exit and long entry':
        plt.axvline(x=to_csv['Date'][i], color = 'green')
    elif to_csv['action'][i] == 'long entry':
        plt.axvline(x=to_csv['Date'][i], color = 'green')
    elif to_csv['action'][i] == 'long exit and short entry':
        plt.axvline(x=to_csv['Date'][i], color = 'red')

plt.savefig("My Strat ML4T in Sample Entry Exit.png", dpi=200)

csv_data = to_csv[['Symbol', 'Order', 'Shares']]
csv_data.to_csv("./my_order_ML4T_in_sample.csv",index_label='Date')

of = "./my_order_ML4T_in_sample.csv"

sv = 10000

portfolio_values = compute_portvals(orders_file = of, start_val = sv, start_date= sd, end_date=ed, symbol=symb)
portfolio_values.columns=['Portfolio']
#print portfolio_values

start_date = dt.datetime(2007,12,31)
end_date = dt.datetime(2009,12,31)
SPX = get_data(['$SPX'], pd.date_range(start_date, end_date))
SPX = SPX['$SPX']


df_temp1 = portfolio_values/portfolio_values.ix[0]
df_temp1.columns = ['Portfolio']

df_temp2 = SPX/SPX.ix[0]
df_temp2.columns = ['$SPX']

df_temp = df_temp1.join(df_temp2)
ax = df_temp.plot(title = 'Daily portfolio value and $SPX ML4T in Sample', fontsize=12, legend=True)
ax.set_xlabel("Date")
ax.set_ylabel("Normalized price")
plt.grid()
plt.savefig("Daily portfolio value and $SPX - My Strat ML4T in Sample Backtest.png")
plt.show()

print 'Profit: ', portfolio_values['Portfolio'][-1]-sv