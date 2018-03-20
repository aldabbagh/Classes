"""
Test a Q Learner in a navigation problem.  (c) 2015 Tucker Balch
"""

import numpy as np
import random as rand
import time
import math
import sys
import QLearner as ql
import optparse

opt = None


# print out the map
def printmap(data):
    print "--------------------"
    for row in range(0, data.shape[0]):
        for col in range(0, data.shape[1]):
            if data[row, col] == 0:
                print " ",
            if data[row, col] == 1:
                print "O",
            if data[row, col] == 2:
                print "*",
            if data[row, col] == 3:
                print "X",
            if data[row, col] == 4:
                print ".",
        print
    print "--------------------"


# find where the robot is in the map
def getrobotpos(data):
    R = -999
    C = -999
    for row in range(0, data.shape[0]):
        for col in range(0, data.shape[1]):
            if data[row, col] == 2:
                C = col
                R = row
    if (R + C) < 0:
        print "warning: start location not defined"
    return R, C


# find where the goal is in the map
def getgoalpos(data):
    R = -999
    C = -999
    for row in range(0, data.shape[0]):
        for col in range(0, data.shape[1]):
            if data[row, col] == 3:
                C = col
                R = row
    if (R + C) < 0:
        print "warning: goal location not defined"
    return (R, C)


# move the robot according to the action and the map
def movebot(data, oldpos, a):
    testr, testc = oldpos

    # update the test location
    if a == 0:  # north
        testr -= 1
    elif a == 1:  # east
        testc += 1
    elif a == 2:  # south
        testr += 1
    elif a == 3:  # west
        testc -= 1

    # see if it is legal. if not, revert
    if testr < 0:  # off the map
        testr, testc = oldpos
    elif testr >= data.shape[0]:  # off the map
        testr, testc = oldpos
    elif testc < 0:  # off the map
        testr, testc = oldpos
    elif testc >= data.shape[1]:  # off the map
        testr, testc = oldpos
    elif data[testr, testc] == 1:  # it is an obstacle
        testr, testc = oldpos

    return (testr, testc)  # return the new, legal location


# convert the location to a single integer
def discretize(pos):
    return pos[0] * 10 + pos[1]


# run the code to test a learner
def test(world=1):
    t0 = time.time()
    inf = open('testworlds/world%02d.csv' % (world))
    data = np.array([map(float, s.strip().split(',')) for s in inf.readlines()])
    originalmap = data.copy()  # make a copy so we can revert to the original map later
    startpos = getrobotpos(data)  # find where the robot starts
    goalpos = getgoalpos(data)  # find where the goal is

    if opt.trace:
        printmap(data)

    rand.seed(5)

    learner = ql.QLearner(num_states=100,
                          num_actions=4,
                          rar=opt.rar,
                          radr=opt.radr,
                          alpha=opt.alpha,
                          gamma=opt.gamma,
                          dyna=opt.dyna,
                          verbose=opt.verbose)  # initialize the learner
    # each iteration involves one trip to the goal
    best_steps = sys.maxint
    for iteration in range(0, opt.iteration):
        steps = 0
        data = originalmap.copy()
        robopos = startpos
        state = discretize(robopos)  # convert the location to a state
        action = learner.querysetstate(state)  # set the state and get first action
        while robopos != goalpos:
            # move to new location according to action and then get a new action
            newpos = movebot(data, robopos, action)
            if newpos == goalpos:
                r = 1  # reward for reaching the goal
            else:
                r = -1  # negative reward for not being at the goal
            state = discretize(newpos)
            action = learner.query(state, r)

            data[robopos] = 4  # mark where we've been for map printing
            data[newpos] = 2  # move to new location
            robopos = newpos  # update the location
            if opt.trace:
                printmap(data)
            # if verbose:
            #     time.sleep(1)
            steps += 1
        if steps < best_steps:
            best_steps = steps
        if opt.trace:
            print 'iteration', iteration + 1, 'steps', steps

    if opt.trace:
        printmap(data)
    t1 = time.time()
    yourtime = t1 - t0
    if opt.rubric1:
        fail = best_steps > result1[world][0] * 1.5
        timeout = yourtime > 2.0
        print 'Test case %02d' % world
        if fail:
            print 'FAILED steps over ', result1[world][0] * 1.5, 'steps'
        if timeout:
            print 'TIME EXCEEDED 2s',
        print 'Steps:', best_steps, 'Reference:', result1[world][0],
        print '; Time:', yourtime, 'Reference:', result1[world][1]
    elif opt.rubric2:
        print 'Test case %02d' % world
        fail = best_steps > result2[world][0] * 1.5
        timeout = yourtime > 10.0
        if fail:
            print 'FAILED steps over ', result2[world][0] * 1.5, 'steps'
        if timeout:
            print 'TIME EXCEEDED 10s',
        print 'Steps:', best_steps, 'Reference:', result2[world][0]
        print '; Time:', yourtime, 'Reference:', result2[world][1]
    else:
        print "world%02d best:" % world, best_steps, 'time:', yourtime


result1 = {
    1: [15, 0.59],
    2: [16, 1.066],
    3: [58, 1.16],
    4: [26, 0.988],
    5: [18, 0.619],
    6: [18, 0.573],
    7: [16, 0.756],
    8: [16, 0.488],
    9: [17, 0.728],
    10: [30, 0.902]
}
result2 = {
    1: [17, 3.934],
    2: [16, 5.077],
    3: [58, 10.386],
    4: [27, 6.412],
    5: [20, 4.68],
    6: [18, 4.583],
    7: [18, 4.377],
    8: [16, 4.099],
    9: [17, 4.371],
    10: [33, 6.003]
}


def main():
    global opt

    def split_ints(option, opt, value, parser):
        setattr(parser.values, option.dest, map(int, value.split(',')))

    p = optparse.OptionParser()
    p.add_option("-t", '--trace', action="store_true")
    p.add_option("-1", '--rubric1', action="store_true")
    p.add_option("-2", '--rubric2', action="store_true")
    p.add_option('-w', '--world', type='string', action='callback',
                 callback=split_ints, default=[1])
    p.add_option("-a", "--alpha", type='float', default=0.2)
    p.add_option("-g", "--gamma", type='float', default=0.9)
    p.add_option("-r", "--rar", type='float', default=0.98)
    p.add_option("-d", "--radr", type='float', default=0.999)
    p.add_option("-y", "--dyna", type='int', default=0)
    p.add_option("-i", "--iteration", type='int', default=500)
    p.add_option("-v", "--verbose", action="store_true")

    (opt, args) = p.parse_args()
    if False:
        opt.alpha = 0.2
        opt.gamma = 0.9
        opt.rar = 0.98
        opt.radr = 0.999
        opt.dyna = 0
        opt.iteration = 500
        opt.world = range(1, 11)
    elif True:
        opt.alpha = 0.2
        opt.gamma = 0.9
        opt.rar = 0.5
        opt.radr = 0.99
        opt.dyna = 200
        opt.iteration = 50
        opt.world = range(1, 11)
    for world in sorted(opt.world):
        test(world=world)

if __name__ == "__main__":
    main()