# EXT:mbox (Mail Client)

TYPO3 CMS backend module to view mbox file contents, like an email client.

This extension has been supported by [**Institut der deutschen Wirtschaft Köln Medien GmbH**](https://www.iwmedien.de/)

![IW Medien](Documentation/Logos/IwMedienLogo.png)


## Screenshots

### Inbox view

![EXT:mbox Inbox view](Documentation/Screenshots/mbox-inbox.png)

### Detail view (with HTML output)

![EXT:mbox detail view (with HTML output)](Documentation/Screenshots/mbox-detail-html-view.png)

### Detail view (with attachments)

![EXT:mbox detail view (with attachments)](Documentation/Screenshots/mbox-detail-with-attachments.png)


## Features

- Simple web mail client for emails stored in local mbox file
- Sort mails by date (asc/desc) and store choice in BE-User session
- HTML and text viewer
- Download attachments separately
- Download whole mail message as EML file (e.g. for Microsoft Outlook)
- Clear mbox (delete all mails) action


## Requirements

- TYPO3 11.5 LTS or 12
- PHP >=7.4


## Installation

Just install the extension, like any other TYPO3 CMS extension.

Link to TER: https://extensions.typo3.org/extension/mbox

For Composer, you can use:

```bash
composer require t3/mbox
```

## Configuration

To make EXT:mbox work you need to configure the TYPO3 mail configuration to use mbox, like this:

```php
<?php

$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'] = 'mbox';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file'] = '/var/www/html/path/to/mbox-file.mbox';
```

If you have different transport configured, the backend module will display an error notice.

### Debug setting

For debugging and testing purposes, there is a Symfony command shipped, which becomes only available,
when you enable the debug mode in Extension configuration. 

Don't forget to clear all caches from install tool, after enabling the debug mode.

The command got the name ``mbox:testmails:send`` and will send four test mails (with/without attachments).


## Missing features

- Display of inline images in HTML view
- Backend module not translated (English existing only)
- Utilize Caching Framework


## Links

- [Git Repository](https://github.com/a-r-m-i-n/mbox)
- [Issue tracker](https://github.com/a-r-m-i-n/mbox/issues)
- [EXT:mbox in TER](https://extensions.typo3.org/extension/mbox)
- [EXT:mbox on Packagist](https://packagist.org/packages/t3/mbox)
- [The author](https://v.ieweg.de)
- [The sponsor](https://www.iwmedien.de)
- [**Donate**](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2DCCULSKFRZFU)
