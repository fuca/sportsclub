#!/bin/bash

echo "API GENERATION SCRIPT STARTED";
echo "=============================";

php apigen.phar generate -s app/modules/ -d ./doc
