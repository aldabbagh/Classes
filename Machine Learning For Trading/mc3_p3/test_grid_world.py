import os
import unittest
from grid_world import GridWorldAgent


class TestGridWorld(unittest.TestCase):

    @classmethod
    def setUpClass(cls):
        cls.base_dir = 'testworlds'
        cls.worlds = ['01', '02', '03', '04', '05',
                      '06', '07', '08', '09', '10']
        cls.optimal = [15, 16, 58, 26, 18,
                       18, 16, 16, 17, 30]
        cls.max_time = 2
        cls.max_dyna_time = 10
        cls.settings = {'alpha': 0.2, 'gamma': 0.9,
                        'rar': 0.98, 'radr': 0.999,
                        'verbose': False}

    def test_base(self):
        for i, world in enumerate(self.worlds):
            fname = os.path.join(self.base_dir, 'world' + world + '.csv')
            agent = GridWorldAgent(fname, **self.settings)
            time, steps = agent.learn(episodes=500)
            self.assertTrue(time < self.max_time,
                            'world {}: {} sec'.format(world, time))
            self.assertTrue(steps < 1.5 * self.optimal[i],
                            'world {}: {} steps'.format(world, steps))

    def test_dyna(self):
        self.settings['dyna'] = 200
        self.settings['rar'] = 0.5
        self.settings['radr'] = 0.99
        for i, world in enumerate(self.worlds[:2]):
            fname = os.path.join(self.base_dir, 'world' + world + '.csv')
            agent = GridWorldAgent(fname, **self.settings)
            time, steps = agent.learn(episodes=50)
            self.assertTrue(time < self.max_dyna_time,
                            'world {}: {} sec'.format(world, time))
            self.assertTrue(steps < 1.5 * self.optimal[i],
                            'world {}: {} steps'.format(world, steps))


if __name__ == '__main__':
    unittest.main()