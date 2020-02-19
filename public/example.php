<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use XpdfCliTools\PdfInfo\Model\PdfInfoModel;
use XpdfCliTools\PdfInfo\PdfInfo;

$pdfInfo = new PdfInfo();

/** @var PdfInfoModel $info */
$info = $pdfInfo->exec(__DIR__ . '/../tests/assets/test.pdf');

echo "<pre>";
print_r($pdfInfo->exec(__DIR__ . '/../tests/assets/test.pdf'));
echo "</pre>";

/**
 * Output:
 *
 * XpdfCliTools\PdfInfo\Model\PdfInfoModel Object
 * (
 *     [Creator] => Acrobat Pro DC 20.6.20034
 *     [Producer] => Acrobat Pro DC 20.6.20034
 *     [CreationDate] => Mon Feb 17 17:16:36 2020
 *     [ModDate] => Mon Feb 17 17:18:29 2020
 *     [Tagged] =>
 *     [Form] => none
 *     [Pages] => 1
 *     [Encrypted] =>
 *     [PageSize] => XpdfCliTools\PdfInfo\Model\PdfInfoPageSizeModel Object
 *         (
 *             [WidthPts] => 612
 *             [HeightPts] => 792
 *             [Format] => letter
 *             [RotatedDegrees] => 0
 *             [raw] => 612 x 792 pts (letter) (rotated 0 degrees)
 *         )
 *     [FileSize] => XpdfCliTools\PdfInfo\Model\PdfInfoFileSizeModel Object
 *         (
 *             [Bytes] => 9146
 *             [raw] => 9146 bytes
 *         )
 *     [Optimized] => 1
 *     [PdfVersion] => 1.6
 *     [raw] => Creator:        Acrobat Pro DC 20.6.20034
 *              Producer:       Acrobat Pro DC 20.6.20034
 *              CreationDate:   Mon Feb 17 17:16:36 2020
 *              ModDate:        Mon Feb 17 17:18:29 2020
 *              Tagged:         no
 *              Form:           none
 *              Pages:          1
 *              Encrypted:      no
 *              Page size:      612 x 792 pts (letter) (rotated 0 degrees)
 *              File size:      9146 bytes
 *              Optimized:      yes
 *              PDF version:    1.6
 * )
 */
