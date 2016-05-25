#! /usr/env python

from pyKriging.krige import kriging
import demjson
import numpy as np


class Interpolation:
    """The interpolation class"""

    _method = ""
    _xMin = 0.0
    _xMax = 0.0
    _yMin = 0.0
    _yMax = 0.0
    _nX = 0
    _nY = 0
    _dX = 0.0
    _dY = 0.0
    _X = []
    _Y = []
    _points = []
    _output = None
    _json_output = ""

    def __init__(self):
        pass

    def from_file(self, filename):
        try:
            _file = open(filename, 'r')
            json_input = _file.read()
        except IOError as exc:
            raise Exception(str(exc))

        self.decode_json(json_input)

    def decode_json(self, json_input):
        json_dict = demjson.decode(json_input)

        if 'type' in json_dict:
            self._method = json_dict['type']

        if 'bounding_box' in json_dict:
            bounding_box = json_dict['bounding_box']

            if 'x_min' in bounding_box:
                self._xMin = float(bounding_box['x_min'])

            if 'x_max' in bounding_box:
                self._xMax = float(bounding_box['x_max'])

            if 'y_min' in bounding_box:
                self._yMin = float(bounding_box['y_min'])

            if 'y_max' in bounding_box:
                self._yMax = float(bounding_box['y_max'])

        if 'grid_size' in json_dict:
            grid_size = json_dict['grid_size']

            if 'n_x' in grid_size:
                self._nX = grid_size['n_x']

            if 'n_y' in grid_size:
                self._nY = grid_size['n_y']

        self._dX = (self._xMax - self._xMin) / self._nX
        self._dY = (self._yMax - self._yMin) / self._nY

        if 'point_values' in json_dict:
            self._points = json_dict['point_values']

        for point in self._points:
            if 'x' in point and 'y' in point:
                self._X.append([point['y'], point['x']])

            if 'value' in point:
                self._Y.append(point['value'])

    def calculate(self):
        if self._method == 'kriging':
            self._output = self.kriging(self._nX, self._nY, self._X, self._Y, self._xMin, self._yMin, self._dX, self._dY)
        else:
            print('method %s is not supported' % self._method)

    def render_output(self):
        self.render(self._method, self._output)

    @staticmethod
    def render(method, output):
        if method == 'kriging':
            print(demjson.encode({"raster": output}))
        elif method == 'error':
            print(demjson.encode({"error": output}))
        else:
            print('{"error": "method %s is not supported"}' % method)

    @staticmethod
    def kriging(nx, ny, x, y, x_min, y_min, dx, dy):
        grid = np.zeros((ny, nx))

        k = kriging(np.array(x), np.array(y))
        k.train()
        for i in range(ny):
            for j in range(nx):
                cell = np.array([y_min + dy * j + .5 * dy, x_min + dx * i + .5 * dx])
                grid[i][j] = k.predict(cell)
        return grid
