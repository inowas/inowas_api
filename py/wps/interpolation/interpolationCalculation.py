#! /usr/env python

import sys
from interpolation import Interpolation

fileName = sys.argv[1]

ip = Interpolation()
ip.from_file(fileName)
ip.calculate()
ip.render_output()
