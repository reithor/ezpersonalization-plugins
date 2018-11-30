#!/usr/bin/env bash

rm -f yc-oxid6.zip yoochoose-oxid.zip yc-oxid6.zip composer.lock

zip -r yoochoose-oxid.zip ./* -x package.sh

mkdir Artifacts
mv yoochoose-oxid.zip Artifacts/yoochoose-oxid.zip
zip -r yc-oxid6.zip Artifacts
rm -rf Artifacts