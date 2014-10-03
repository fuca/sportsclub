#!/bin/bash
echo "Clear cache of SportsClub project";
echo "================================="

echo "> clearing /temp/";
sudo rm -R ./temp/_*;

echo "> clearing temp/proxies/";
sudo rm -R ./temp/proxies/_*;

echo "> clearing temp/cache/\n"
sudo rm -R ./temp/cache/_*;

echo "> clearing temp/cache/services/";
sudo rm -R ./temp/cache/services/_*;
sudo rm -R ./temp/cache/services/*/_*;

echo "> deleting temp/btfj.dat";
sudo rm ./temp/btfj.dat

echo "Cache clean complete";
