# wake-on-lan-cli
A Wake-On-Lan script written in PHP using Symfony package symfony/console 4.2

## Dependencies

In order to build a **phar** executable archive using the `Makefile` provided, you will need:
* PHP CLI (tested version of PHP was 7.1.23)
* GNU Make
* [Box 2](https://box-project.github.io/box2/)

## Compile

Go to root folder of project and type:
```bash
make
```

## Install

```bash
make install
```
will install the phar executable in the `${HOME}/bin/` folder.

## Cleanup

```bash
make clean
```
to clean up any files produced during compilation above.
