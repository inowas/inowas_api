import urllib
import urllib2

url = 'http://app.dev.inowas.com/api/results.json'
params = urllib.urlencode({
    'id': '1',
    'propertyType': 'gwHead',
    'width': 4,
    'height': 4,
    'upperLeftX': 0.005,
    'upperLeftY': 0.005,
    'scaleX': 1,
    'scaleY': 1,
    'skewX': 0,
    'skewY': 0,
    'srid': 4326,
    'bandPixelType': '\'32BF\'::text',
    'bandInitValue': 200,
    'bandNoDataVal': -9999,
    'data': [[0,1,2,3],[0,1,2,3],[0,1,2,3],[0,1,2,3]],
    'date': '2016-02-23 12:32:12'
})
response = urllib2.urlopen(url, params).read()
print response
