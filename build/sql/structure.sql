CREATE LANGUAGE plpython2u;

CREATE EXTENSION postgis;
CREATE EXTENSION postgis_topology;

CREATE DOMAIN simple_raster AS FLOAT[][];