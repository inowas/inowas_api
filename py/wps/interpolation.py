#! /usr/env python

from pyKriging.krige import kriging
import demjson
import numpy as np
import sys

fileName = sys.argv[1]
fileContent = ''

try:
    file = open(fileName, 'r')
    fileContent = file.read()
except IOError:
    e = sys.exc_info()[0]
    print("Error: %s" % e)

json_dict = demjson.decode(fileContent)
method = json_dict['type']
xMin = float(json_dict['bounding_box']['x_min'])
xMax = float(json_dict['bounding_box']['x_max'])
yMin = float(json_dict['bounding_box']['y_min'])
yMax = float(json_dict['bounding_box']['y_max'])
nX = json_dict['grid_size']['n_x']
nY = json_dict['grid_size']['n_y']
dX = (xMax - xMin) / nX
dY = (yMax - yMin) / nY
X = []
Y = []

for point in json_dict['point_values']:
    X.append([point['y'], point['x']])
    Y.append(point['value'])

grid = np.zeros((nY, nX))

if method == 'kriging':
    k = kriging(np.array(X), np.array(Y))
    k.train()
    for i in range(nY):
        for j in range(nX):
            cell = np.array([yMin + dY * j + .5 * dY, xMin + dX * i + .5 * dX])
            grid[i][j] = k.predict(cell)

    output = demjson.encode({"raster": grid})
    print(output)
else:
    print('method %s is not supported' % method)