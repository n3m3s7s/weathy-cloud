#!/bin/bash

docker run -d -p 8888:9090 -v $(pwd)/shared:/opt/shared --name weathy-app --rm weathy/service:0.1