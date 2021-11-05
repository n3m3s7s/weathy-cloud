#!/bin/bash

docker build -t weathy/service:0.1 --build-arg PORT=9090 .