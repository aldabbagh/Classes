import time
import numpy as np

from QLearner import QLearner


_NORTH = 0
_EAST = 1
_SOUTH = 2
_WEST = 3


class GridWorld(object):

    _action_code = {
        _NORTH: u'\u2191',
        _EAST: u'\u2192',
        _SOUTH: u'\u2193',
        _WEST: u'\u2190'
    }

    def __init__(self, grid, reward, penalty):
        self.grid = grid
        self.originalmap = grid.copy()
        self.r = grid.shape[0]
        self.c = grid.shape[1]
        self.reward = reward
        self.penalty = penalty
        self.n_states = self.r * self.c

    def states(self):
        return self.n_states

    def restore_grid(self):
        self.grid = self.originalmap.copy()

    # convert the location to a single integer
    def discretize(self, pos):
        return pos[0] * self.r + pos[1]

    def drawpath(self, oldpos, newpos, action):
        self.grid[oldpos] = 4 + action
        self.grid[newpos] = 2

    def printpolicy(self, policy):
        print "-" * 2 * self.c
        for row in range(self.r):
            for col in range(self.c):
                pol = policy[self.discretize[row, col]]
                print self._action_code[pol],
            print
        print "-" * 2 * self.c

    # print out the map
    def printmap(self):
        print "-" * 2 * self.c
        for row in range(self.r):
            for col in range(self.c):
                if (row, col) == self.startpos:
                    print u'\u25CC',
                elif self.grid[row, col] == 0:
                    print ' ',
                elif self.grid[row, col] == 1:
                    print u'\u25A9',
                elif self.grid[row, col] == 2:
                    print u'\u25CE',
                elif self.grid[row, col] == 3:
                    print "X",
                elif self.grid[row, col] >= 4:
                    print self._action_code[self.grid[row, col] - 4],
            print
        print "-" * 2 * self.c

    # find where the robot is in the map
    def getrobotpos(self):
        R = -999
        C = -999
        for row in range(self.r):
            for col in range(self.c):
                if self.grid[row, col] == 2:
                    C = col
                    R = row
        if R + C < 0:
            print "warning: start location not defined"
        self.startpos = R, C
        return R, C

    # find where the goal is in the map
    def getgoalpos(self):
        R = -999
        C = -999
        for row in range(self.r):
            for col in range(self.c):
                if self.grid[row, col] == 3:
                    C = col
                    R = row
        if R + C < 0:
            print "warning: goal location not defined"
        self.goal = R, C
        return R, C

    # move the robot according to the action and the map
    def movebot(self, oldpos, action):
        testr, testc = oldpos
        reward = self.penalty

        # update the test location
        if action == _NORTH:
            testr = testr - 1
        elif action == _EAST:
            testc = testc + 1
        elif action == _SOUTH:
            testr = testr + 1
        elif action == _WEST:
            testc = testc - 1

        # see if it is legal. if not, revert
        if testr < 0:  # off the map
            testr, testc = oldpos
        elif testr >= self.r:  # off the map
            testr, testc = oldpos
        elif testc < 0:  # off the map
            testr, testc = oldpos
        elif testc >= self.c:  # off the map
            testr, testc = oldpos
        elif self.grid[testr, testc] == 1:  # it is an obstacle
            testr, testc = oldpos

        if (testr, testc) == self.goal:
            reward = self.reward

        return [reward, (testr, testc)]


def create_grid_world(fname=None, shape=(40, 40), start=None, goal=None,
                      obstacles=None, reward=1, penalty=-1):
    if fname is not None:
        return GridWorld(np.loadtxt(fname, delimiter=',', dtype=int),
                         reward=reward, penalty=penalty)

    grid = np.zeros(shape)

    # Random start always in leftmost column
    if start is None:
        start = (np.random.randint(shape[0]), 0)
    grid[start] = 2

    # Random goal always in rightmost column
    if goal is None:
        goal = (np.random.randint(shape[0]), shape[1] - 1)
    grid[goal] = 3

    # Place random obstacles near middle
    if obstacles is None:
        for i in range(shape[0]):
            for j in range(2, shape[1] - 2):
                if np.random.random() < 0.25:
                    grid[i, j] = 1
    else:
        for obs in obstacles:
            grid[obs] = 1

    return GridWorld(grid, reward=reward, penalty=penalty)


class GridWorldAgent(QLearner):

    def __init__(self, grid=None, **settings):
        if grid is None:
            self.world = create_grid_world()
        elif isinstance(grid, str):
            self.world = create_grid_world(grid)
        else:
            self.world = grid
        self.start = self.world.getrobotpos()
        self.goal = self.world.getgoalpos()
        settings['num_states'] = self.world.states()
        super(GridWorldAgent, self).__init__(**settings)

    def learn(self, episodes=500, verbose=False):
        # each iteration involves one trip to the goal
        start = time.time()
        for iteration in range(episodes):
            self.world.restore_grid()
            steps = 0
            pos = self.start
            state = self.world.discretize(pos)
            action = self.querysetstate(state)
            while pos != self.goal:

                # move to new location according to action and get new action
                r, newpos = self.world.movebot(pos, action)
                self.world.drawpath(pos, newpos, action)
                state = self.world.discretize(newpos)
                action = self.query(state, r)

                pos = newpos
                steps += 1

            if verbose:
                print iteration, steps

        elapsed = round(time.time() - start, 3)

        if verbose:
            print 'Took {} seconds'.format(elapsed)
            print 'Shortest path is {} steps'.format(steps)
            self.world.printmap()
        return elapsed, steps