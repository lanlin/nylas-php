#!/bin/bash

# Whole File: ./do.sh AccountTest.php
# Single Method: ./do.sh AccountTest.php --filter testGetAccount

../vendor/bin/phpunit --configuration phpunit.xml --do-not-cache-result $1 $2 $3 $4 $5 $6