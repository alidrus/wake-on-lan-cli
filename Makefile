#
# Makefile for building and installing XDMS Command Line Tool phar executable.
#

SOURCEDIR = src

SOURCES := $(shell find $(SOURCEDIR) -name '*.php')

all: wol.phar

wol.phar: bin/wol box.json $(SOURCES)
	box build -v

install: wol.phar
	install -m 0755 -T wol.phar $(HOME)/bin/wol

clean:
	rm -f wol.phar
