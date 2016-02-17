CREATE LANGUAGE plpython2u;

CREATE EXTENSION postgis;
CREATE EXTENSION postgis_topology;

CREATE DOMAIN simple_raster AS FLOAT[][];

CREATE AGGREGATE array_agg_mult (anyarray)  (
SFUNC     = array_cat
  ,STYPE     = anyarray
  ,INITCOND  = '{}'
);