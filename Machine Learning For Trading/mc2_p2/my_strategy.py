import pandas as pd
import numpy as np
import datetime as dt
import os
from util import get_data, plot_data
import matplotlib.pyplot as plt
from marketsim import compute_portvals

def my_strategy(orders_file = "./orders/orders.csv", start_val = 1000000):

    # Toggle this to plot the bolinger bands
    Plot = False


    start_date = dt.datetime(2009,12,31)
    end_date = dt.datetime(2011,12,31)
    portvals = get_data(['IBM'], pd.date_range(start_date, end_date))
    portvals = portvals[['IBM']]  # remove SPY
    portvals['SMA'] = np.nan
    portvals['Upper'] = np.nan
    portvals['Lower'] = np.nan
    portvals['KRI'] = np.nan

    for i in range(len(portvals)):
        if i>=19:
            SMA = portvals['IBM'][i-19:i+1].mean()
            SDV = portvals['IBM'][i-19:i+1].std()
            portvals['SMA'][i] = SMA
            portvals['Upper'][i] = SMA + 2*SDV
            portvals['Lower'][i] = SMA - 2*SDV
            portvals['KRI'][i] = 100.0*(portvals['IBM'][i] - SMA)/SMA
    #portvals = portvals[19:]
    if Plot:
        ax = portvals.plot(title="My Strategy", fontsize=12)
        ax.set_xlabel("Date")
        ax.set_ylabel("Price")
        plt.grid()
        #plt.savefig("plot.png")
        plt.show()

    return portvals


my_strat = my_strategy()
print my_strat['KRI']
orders = my_strat.copy()
orders['Order'] = 'nan'
orders['Symbol'] = 'IBM'
orders['action'] = 'nan'
orders['Shares'] = np.nan
orders['Date'] = orders.index


Entryl = False
Entrys = False


for i in range(len(orders)):
    if i>= 19:
        #LONG
        if orders['KRI'][i]<-0.1 and not Entryl and not Entrys:
            orders['Order'][i] = 'BUY'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'long entry'
            Entryl = True

        elif orders['KRI'][i]>4.15 and Entryl and not Entrys:
            orders['Order'][i] = 'SELL'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'long exit'
            Entryl = False

        elif orders['KRI'][i]>-0.75 and not Entryl and not Entrys:
            orders['Order'][i] = 'SELL'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'short entry'
            Entrys = True

        elif orders['KRI'][i]<3.55 and  Entrys and not Entryl:
            orders['Order'][i] = 'BUY'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'short exit'
            Entrys = False


print orders.dropna()

to_csv = orders.dropna()

ax = my_strat['IBM'].plot(title = 'My Strategy', fontsize=12, legend=True)
ax.set_xlabel("Date")
ax.set_ylabel("Price")
my_strat['SMA'].plot(color = 'goldenrod', label = 'SMA', legend=True)
#my_strat['KRI'].plot( color = 'mediumturquoise', label = 'KRI', legend=True)
#my_strat['Lower'].plot( color = 'mediumturquoise', label = 'Bollinger Band')

for i in range(len(to_csv)):
    if to_csv['action'][i] == 'short entry':
        plt.axvline(x=to_csv['Date'][i], color = 'red')
    elif to_csv['action'][i] == 'short exit':
        plt.axvline(x=to_csv['Date'][i], color = 'black')
    elif to_csv['action'][i] == 'long entry':
        plt.axvline(x=to_csv['Date'][i], color = 'green')
    elif to_csv['action'][i] == 'long exit':
        plt.axvline(x=to_csv['Date'][i], color = 'black')

plt.savefig("My Strat.png", dpi=200)

csv_data = to_csv[['Symbol', 'Order', 'Shares']]
csv_data.to_csv("./my_order.csv",index_label='Date')

of = "./my_order.csv"

sv = 10000

portfolio_values = compute_portvals(orders_file = of, start_val = sv)
portfolio_values.columns=['Portfolio']
#print portfolio_values

start_date = dt.datetime(2009,12,31)
end_date = dt.datetime(2011,12,31)
SPX = get_data(['$SPX'], pd.date_range(start_date, end_date))
SPX = SPX['$SPX']


df_temp1 = portfolio_values/portfolio_values.ix[0]
df_temp1.columns = ['Portfolio']

df_temp2 = SPX/SPX.ix[0]
df_temp2.columns = ['$SPX']

df_temp = df_temp1.join(df_temp2)
ax = df_temp.plot(title = 'Daily portfolio value and $SPX', fontsize=12, legend=True)
ax.set_xlabel("Date")
ax.set_ylabel("Normalized price")
plt.grid()
plt.savefig("Daily portfolio value and $SPX - My Strat.png")
plt.show()

print portfolio_values['Portfolio'][-1]-sv