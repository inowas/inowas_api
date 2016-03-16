FROM debian:latest

RUN apt-get -y update && apt-get install -y \
    build-essential \
    gcc \
    gfortran-4.9 \
    make \
    libfreetype6-dev \
    libpng-dev \
    pkg-config \ 
    python \
    python-dev \
    python-pip \
    unzip \ 
    sed \
    wget
WORKDIR /tmp
RUN wget http://water.usgs.gov/ogw/modflow/MODFLOW-2005_v1.11.00/mf2005v1_11_00_unix.zip
RUN unzip mf2005v1_11_00_unix.zip
RUN rm -rf mf2005v1_11_00_unix.zip
WORKDIR /tmp/Unix/src
RUN sed -i '10s/f90/gfortran-4.9/g' makefile
RUN sed -i '16s/SEQUENTIAL/STREAM/' openspec.inc
RUN sed -i '23s/\(.\{1\}\)//' openspec.inc
RUN sed -i '30s/^/C/' openspec.inc
RUN make
RUN cp mf2005 /bin/
WORKDIR /tmp
RUN rm -rf Unix
RUN pip install matplotlib 
RUN pip install flopy 
WORKDIR /data