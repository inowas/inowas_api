# -*- coding: utf-8 -*-

import unittest
from demjson import JSONDecodeError
from interpolation.interpolation import Interpolation

class InterpolationTests(unittest.TestCase):

    _interpolation = None

    @classmethod
    def setUpClass(cls):
        cls._interpolation = Interpolation()

    def test_load_valid_file(self):
        self._interpolation.from_file('./testfiles/valid.json')
        self.assertEqual('kriging', self._interpolation._method)
        self.assertEqual(1.0, self._interpolation._xMin)
        self.assertEqual(1.2, self._interpolation._xMax)
        self.assertEqual(2.1, self._interpolation._yMin)
        self.assertEqual(0.2, self._interpolation._yMax)
        self.assertEqual(12, self._interpolation._nX)
        self.assertEqual(13, self._interpolation._nY)
        self.assertEqual(2, len(self._interpolation._points))

        points = self._interpolation._points
        self.assertEqual(1.1, points[0]['x'])
        self.assertEqual(2.2, points[0]['y'])
        self.assertEqual(3.4, points[0]['value'])
        self.assertEqual(4.4, points[1]['x'])
        self.assertEqual(5.5, points[1]['y'])
        self.assertEqual(6.6, points[1]['value'])

    def test_load_invalid_JSON_format(self):
        try:
            self._interpolation.from_file('./testfiles/invalid.json')
        except JSONDecodeError:
            pass
        except Exception as e:
            self.fail('Unexpected exception raised')
        else:
            self.fail('ExpectedException not raised')

    def test_load_empty_file(self):
        try:
            self._interpolation.from_file('./testfiles/empty.json')
        except JSONDecodeError:
            pass
        except Exception as e:
            self.fail('Unexpected exception raised')
        else:
            self.fail('ExpectedException not raised')

    def test_kriging(self):
        self._interpolation.from_file('./testfiles/valid.json')
        self._interpolation.calculate()
        self.assertEqual(13, len(self._interpolation._output))
        self.assertEqual(12, len(self._interpolation._output[0]))

if __name__ == '__main__':
    unittest.main()
