#!/bin/bash

# Whole File: ./do.sh AccountTest.php
# Single Method: ./do.sh AccountTest.php --filter testGetAccount

../vendor/bin/phpunit --configuration phpunit.xml $1 $2 $3 $4 $5 $6