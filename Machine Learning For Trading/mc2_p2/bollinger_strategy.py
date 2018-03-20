import pandas as pd
import numpy as np
import datetime as dt
import os
from util import get_data, plot_data
import matplotlib.pyplot as plt
from marketsim import compute_portvals

def bollinger(orders_file = "./orders/orders.csv", start_val = 1000000):

    # Toggle this to plot the bolinger bands
    Plot = False


    start_date = dt.datetime(2009,12,31)
    end_date = dt.datetime(2011,12,31)
    portvals = get_data(['IBM'], pd.date_range(start_date, end_date))
    portvals = portvals[['IBM']]  # remove SPY
    portvals['SMA'] = np.nan
    portvals['Upper'] = np.nan
    portvals['Lower'] = np.nan

    for i in range(len(portvals)):
        if i>=19:
            SMA = portvals['IBM'][i-19:i+1].mean()
            SDV = portvals['IBM'][i-19:i+1].std()
            portvals['SMA'][i] = SMA
            portvals['Upper'][i] = SMA + 2*SDV
            portvals['Lower'][i] = SMA - 2*SDV

    #portvals = portvals[19:]
    if Plot:
        ax = portvals.plot(title="Bollinger Bands", fontsize=12)
        ax.set_xlabel("Date")
        ax.set_ylabel("Price")
        plt.grid()
        #plt.savefig("plot.png")
        plt.show()

    return portvals


blngr = bollinger()

orders = blngr.copy()
orders['Order'] = 'nan'
orders['Symbol'] = 'IBM'
orders['action'] = 'nan'
orders['Shares'] = np.nan
orders['Date'] = orders.index

Entry = False
Exit = True

for i in range(len(orders)):
    if i>= 19:
        #LONG
        if orders['IBM'][i]>orders['Lower'][i] and orders['IBM'][i-1]<orders['Lower'][i-1] and  Entry==False and Exit:
            orders['Order'][i] = 'BUY'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'long entry'
            Entry = True
            Exit = False

        if orders['IBM'][i]>orders['SMA'][i] and orders['IBM'][i-1]<orders['SMA'][i-1] and  Exit==False and Entry:
            orders['Order'][i] = 'SELL'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'long exit'
            Exit = True
            Entry = False

        #Short
        if orders['IBM'][i]<orders['Upper'][i] and orders['IBM'][i-1]>orders['Upper'][i-1] and  Entry==False and Exit:
            orders['Order'][i] = 'SELL'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'short entry'
            Entry = True
            Exit = False

        if orders['IBM'][i]<orders['SMA'][i] and orders['IBM'][i-1]>orders['SMA'][i-1] and  Exit==False and Entry:
            orders['Order'][i] = 'BUY'
            orders['Shares'][i] = 100.0
            orders['action'][i] = 'short exit'
            Exit = True
            Entry = False


#print orders.dropna()

to_csv = orders.dropna()

ax = blngr['IBM'].plot(title = 'Bollinger Bands Strategy', fontsize=12, legend=True)
ax.set_xlabel("Date")
ax.set_ylabel("Price")
blngr['SMA'].plot(color = 'goldenrod', label = 'SMA', legend=True)
blngr['Upper'].plot( color = 'mediumturquoise', label = 'Bollinger Band', legend=True)
blngr['Lower'].plot( color = 'mediumturquoise', label = 'Bollinger Band')

for i in range(len(to_csv)):
    if to_csv['action'][i] == 'short entry':
        plt.axvline(x=to_csv['Date'][i], color = 'red')
    elif to_csv['action'][i] == 'short exit':
        plt.axvline(x=to_csv['Date'][i], color = 'black')
    elif to_csv['action'][i] == 'long entry':
        plt.axvline(x=to_csv['Date'][i], color = 'green')
    elif to_csv['action'][i] == 'long exit':
        plt.axvline(x=to_csv['Date'][i], color = 'black')


plt.savefig("Bolinger Strategy.png", dpi=200)

csv_data = to_csv[['Symbol', 'Order', 'Shares']]
csv_data.to_csv("./bolinger_order.csv",index_label='Date')

of = "./bolinger_order.csv"

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
plt.savefig("Daily portfolio value and $SPX-Bolinger.png")
plt.show()

print portfolio_values['Portfolio'][-1]-sv