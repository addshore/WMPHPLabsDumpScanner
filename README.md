LabsDumpScanner
==================

This tool allows for easy scanning of dumps of WMF wikis without having to first download them.

This tool can be found at http://tools.wmflabs.org/dumpscan/

## Install

```bash
$ become dumpscan
$ cd public_html
$ git clone https://github.com/addshore/LabsDumpScanner.git .
$ composer install
```
Navigate to the Url!

The tool requires cron.php to run in order for the dump scans to actually be completed so we should probably add a cron for that!

```bash
*/5 * * * * jsub -mem 1G -N dumpscan php /data/project/dumpscan/public_html/cron.php
```