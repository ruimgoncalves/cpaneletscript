# Automated LetsEncrypt SSL certificates for CPanel

This script automates the process of installing and renewing Let's Encrypt certificates on a CPanel shared host.

## Description

First of all, Yes!, you can have a free SSL certificate installed on your CPanel hosted shared server!

The process was tedious, but now is one cron job away, for unlimited certification freedom.

It fetches and installs the certificates for the configured domains(s) or subdomain(s).

**Use at your own risk**, whatever that may be.

## Installation

Just copy the folder to your server ex: **./.lecpanel**, I advise not to put it bellow the public directory, for obvious security reasons.

Edit the **config.php** file or create a new file and name it like **config.mydomain.php**. This way you can have multiple configurations.

The options are self explanatory.

## Usage

Go to the cron job tab on your CPanel and add the command to run every week.
```bash
php -q /home/cpanelusername/.lescript/index.php config=mydomain
```

Requirements
============

- PHP 5.4 and up
- OpenSSL extension
- Curl extension

## Why I created it?
Because I'm a cheap bastard just like you!

> **lescript**: is standalone part of [LEManager](https://github.com/analogic/lemanager)

## License
CPaneLeScript is released under the MIT Licence. See the bundled LICENSE file for details.