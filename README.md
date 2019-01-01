# LetsEncrypt Certbot alternative for CPanel

This script automates the process of installing and renewing LetsEncrypt certificates on a CPanel shared host without WHM or the LetsEncrypt plugin installed.

Think of a Certbot alternative capable of running on a shared host restricted environment.

## Description

First of all, Yes!, you can have a free SSL certificate installed on your CPanel hosted shared server!

The process was tedious, but now is one cron job away, for unlimited certification freedom.

It fetches and installs the certificates for the configured domains(s) or subdomain(s).

LetsEncrypt v2 protocol supported.

## Installation

Clone this repo, and run **composer install** then, just copy the folder to your server ex: **./.sslscript**, I advise not to put it inside the public directory, although all sensitive folders are protected with .htaccess rules.

Edit the **config.php** file or create a new file and name it like **config.mydomain.php**. This way you can have multiple configurations.

The options are self explanatory.

## Usage

Go to the cron job tab on your CPanel and add the command to run every week.

```bash
php -q /home/cpanelusername/.lescript/index.php config=mydomain
```

Or call it from a browser

```html
mydomain.com/ressl.php?config=mydomain
```

Requirements
============

- PHP 5.4 and up
- OpenSSL extension
- Curl extension

> **leclient**: is standalone part of [LEClient](https://github.com/yourivw/leclient)

## License
CPaneLeScript is released under the MIT Licence. See the bundled LICENSE file for details.