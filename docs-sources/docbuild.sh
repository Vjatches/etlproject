#!/bin/bash
mkdocs build
if [ $? -eq 0 ]; then
    RED='\033[0;31m'
    NC='\033[0m'
    GREEN='\033[0;32m'
    echo "Cleaning destination directory /home/docs/public_html"
    rm -rf /home/docs/public_html/*
    if [ $? -eq 0 ]; then
	echo -e "${GREEN}OK${NC}"
	echo "Copying content from /home/docs-sources/site to /home/docs/public_html"
	cp -R /home/docs-sources/site/* /home/docs/public_html/
	if [ $? -eq 0 ]; then
		echo -e "${GREEN}OK${NC}"
	else
		echo -e "${RED}FAIL${NC}"
	fi
    else
	echo -e "${RED}FAIL${NC}"
    fi
else
    echo -e "${RED}FAIL${NC}"
fi
