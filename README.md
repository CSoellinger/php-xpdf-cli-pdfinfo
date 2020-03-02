# Xpdf Cli - pdfinfo

A little PHP wrapper around the [Xpdf cli tool: pdfinfo](https://www.xpdfreader.com/).

Fetching the following informations and make them easy accessible:
- Producer
- Creation date
- Modified date
- Tagged
- Form
- Pages
- Encrypted
- Page size
  - Width as points
  - Height as points
  - Format
  - Rotated Degrees
- Box (Media, Crop, Bleed, Trim, Art)
  - X coordinate as points
  - Y coordinate as points
  - Width as points
  - Height as points
- File size
  - Bytes
- Optimized
- PDF version

Note: If you have a password protected pdf file, and not setting the password
param, you will get a process failed exception.

## Getting Started

PHP 7 or above and Composer is expected to be installed on your system.

## Installing

```
composer require csoellinger/xpdf-cli-pdfinfo
```

## Usage

```php
<?php

use XpdfCliTools\PdfInfo\PdfInfo;
use XpdfCliTools\PdfInfo\Model\PdfInfoModel;

$pdfInfo = new PdfInfo();
// Optionally set a custom path to pdfinfo binary file:
// $pdfInfo = new PdfInfo('<Path-To-Binary>');

/** @var PdfInfoModel $info */
$info = $pdfInfo->exec('<Path-To-Pdf>.pdf');

// If you have a passwort protected pdf file:
// $info = $pdfInfo->exec('<Path-To-Pdf>.pdf', 'OwnerPassword');
// $info = $pdfInfo->exec('<Path-To-Pdf>.pdf', null, 'UserPassword');

// Access the pdf informations
echo $info->Creator; // Creator
echo $info->Producer; // Producer
echo $info->CreationDate; // Creation date
echo $info->ModDate; // Modification date
echo $info->Tagged; // Tagged (true/false)
echo $info->Form; // Form(s)
echo $info->Pages; // Number of pages

echo $info->PageSize->Width; // Page width as points
echo $info->PageSize->Height; // Page height as points
echo $info->PageSize->Format; // Page format (if found)
echo $info->PageSize->RotatedDegrees; // Degrees if rotated
echo $info->PageSize->raw; // Raw shell output for page size

// Available boxes: MediaBox, CropBox, BleedBox, TrimBox, ArtBox
echo $info->MediaBox->X; // X coordinate
echo $info->MediaBox->Y; // Y coordinate
echo $info->MediaBox->Width; // Box width as points
echo $info->MediaBox->Height; // Box height as points
echo $info->MediaBox->raw; // Raw shell output for box

echo $info->FileSize->Bytes; // File size in bytes
echo $info->FileSize->raw; // Raw shell output for file size

echo $info->Encrypted; // Encrypted (true/false)
echo $info->Optimized; // Optimized (true/false)
echo $info->PDFVersion; // Version
echo $info->raw; // Raw shell output from pdfinfo

// Get page size as millimeter:
echo $info->PageSize->Width / PdfInfo::MM_TO_PTS; // = Convert points to millimeter
echo $info->PageSize->Height / PdfInfo::MM_TO_PTS; // = Convert points to millimeter
```

## Example

See inside public dir for an example.


## Running the Tests

All tests can be run by executing

```
vendor/bin/phpunit
```

`phpunit` will automatically find all tests inside the `tests` directory and run them based on the configuration in the `phpunit.xml` file.


## Running the Example

PHP has an in-built server for local development. To run this change into the directory `public` and run

```
php -S localhost:8000
```

Then open your browser at `http://localhost:8000/example.php`


## Built With

- [PHP](https://secure.php.net/)
- [Composer](https://getcomposer.org/)
- [PHPUnit](https://phpunit.de/)
- [Xpdf](https://www.xpdfreader.com/)
- [getOS](https://github.com/insign/get-os)
- [Symfony Process](https://symfony.com/doc/current/components/process.html)

## ToDo

- Add different encrypted informations: *Encrypted: yes (print:yes copy:yes change:yes addNotes:yes)*

## License

This project is licensed under the MIT License - see the [LICENCE.md](LICENCE.md) file for details.
