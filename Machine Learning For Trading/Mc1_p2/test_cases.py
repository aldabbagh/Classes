from optimization import optimize_portfolio
import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
import datetime as dt
from util import get_data, plot_data
import scipy.optimize as spo



if __name__ == "__main__":
    # test case 1
    print "Testing case 1"
    begin = dt.datetime(2010,01,01)
    finish = dt.datetime(2010,12,31)
    stock_options = ['GOOG', 'AAPL', 'GLD', 'XOM']
    allocations, cr, adr, sddr, sr = optimize_portfolio(sd = begin, ed = finish,\
        syms = stock_options, \
        gen_plot = False)

    correct_allocations = [5.38105153e-16, 3.96661695e-01,6.03338305e-01, -5.42000166e-17]

    for i in range(len(allocations)):
        if abs(allocations[i]-correct_allocations[i])>0.1 or allocations[i]<-0.02 or allocations[i]>1.02:
            print "wrong allocations, "+"Failed test 1!"
            exit(1)

    if sum(allocations)>1.02:
        print "Allocation sum is incorrect, failed test 1"
        exit(1)

    print "passed test case 1"

    # test case 2
    print "Testing case 2"
    begin = dt.datetime(2004,01,01)
    finish = dt.datetime(2006,01,01)
    stock_options = ['AXP', 'HPQ', 'IBM', 'HNZ']
    allocations, cr, adr, sddr, sr = optimize_portfolio(sd = begin, ed = finish,\
        syms = stock_options, \
        gen_plot = False)

    correct_allocations = [  7.75113042e-01,   2.24886958e-01,  -1.18394877e-16,  -7.75204553e-17]

    for i in range(len(allocations)):
        if abs(allocations[i]-correct_allocations[i])>0.1 or allocations[i]<-0.02 or allocations[i]>1.02:
            print "wrong allocations, "+"Failed test 2!"
            exit(1)

    if sum(allocations)>1.02:
        print "Allocation sum is incorrect, failed test 2"
        exit(1)

    print "passed test case 2"

    # test case 3
    print "Testing case 3"
    begin = dt.datetime(2004,12,01)
    finish = dt.datetime(2006,05,31)
    stock_options = ['YHOO', 'XOM', 'GLD', 'HNZ']
    allocations, cr, adr, sddr, sr = optimize_portfolio(sd = begin, ed = finish,\
        syms = stock_options, \
        gen_plot = False)

    correct_allocations = [ -3.84053467e-17,   7.52817663e-02,   5.85249656e-01,   3.39468578e-01]

    for i in range(len(allocations)):
        if abs(allocations[i]-correct_allocations[i])>0.1 or allocations[i]<-0.02 or allocations[i]>1.02:
            print "wrong allocations, "+"Failed test 3!"
            exit(1)

    if sum(allocations)>1.02:
        print "Allocation sum is incorrect, failed test 3"
        exit(1)

    print "passed test case 3"

    # test case 4
    print "Testing case 4"
    begin = dt.datetime(2005,12,01)
    finish = dt.datetime(2006,05,31)
    stock_options = ['YHOO', 'HPQ', 'GLD', 'HNZ']
    allocations, cr, adr, sddr, sr = optimize_portfolio(sd = begin, ed = finish,\
        syms = stock_options, \
        gen_plot = False)

    correct_allocations = [ -1.67414005e-15,   1.01227499e-01,   2.46926722e-01,   6.51845779e-01]

    for i in range(len(allocations)):
        if abs(allocations[i]-correct_allocations[i])>0.1 or allocations[i]<-0.02 or allocations[i]>1.02:
            print "wrong allocations, "+"Failed test 4!"
            exit(1)

    if sum(allocations)>1.02:
        print "Allocation sum is incorrect, failed test 4"
        exit(1)

    print "passed test case 4"