CREATE LANGUAGE plpython2u;

CREATE EXTENSION postgis;
CREATE EXTENSION postgis_topology;

CREATE AGGREGATE compound_array(basetype = anyarray, stype  = anyarray, sfunc = array_cat, initcond = '{}');