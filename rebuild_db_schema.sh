#!/bin/bash
echo "Rebuild database schema";
echo "=======================";

echo "Settings privileges";
sudo chmod 777 -R temp/

echo "Validating and pushing schema into database";
sudo php www/index.php \orm:schema:up --force

echo "Settings privileges";
sudo chmod 777 -R temp/