<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use XpdfCliTools\PdfInfo\Model\PdfInfoModel;
use XpdfCliTools\PdfInfo\PdfInfo;

$pdfInfo = new PdfInfo();

/** @var PdfInfoModel $info */
$info = $pdfInfo->exec(__DIR__ . '/../tests/assets/test.pdf');
// $info = $pdfInfo->exec(__DIR__ . '/../tests/assets/test.pdf', 'OwnerPassword', 'UserPassword');

echo "<pre>";
print_r($info);
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
 *             [Width] => 612
 *             [Height] => 792
 *             [Format] => letter
 *             [RotatedDegrees] => 0
 *             [raw] => 612 x 792 pts (letter) (rotated 0 degrees)
 *         )
 *     [MediaBox] => XpdfCliTools\PdfInfo\Model\PdfInfoBoxModel Object
 *         (
 *             [X] => 0
 *             [Y] => 0
 *             [Width] => 612
 *             [Height] => 792
 *             [raw] => MediaBox:           0.00     0.00   612.00   792.00
 *         )
 *     [CropBox] => XpdfCliTools\PdfInfo\Model\PdfInfoBoxModel Object
 *         (
 *             [X] => 0
 *             [Y] => 0
 *             [Width] => 612
 *             [Height] => 792
 *             [raw] => CropBox:            0.00     0.00   612.00   792.00
 *         )
 *     [BleedBox] => XpdfCliTools\PdfInfo\Model\PdfInfoBoxModel Object
 *         (
 *             [X] => 0
 *             [Y] => 0
 *             [Width] => 612
 *             [Height] => 792
 *             [raw] => BleedBox:           0.00     0.00   612.00   792.00
 *         )
 *     [TrimBox] => XpdfCliTools\PdfInfo\Model\PdfInfoBoxModel Object
 *         (
 *             [X] => 0
 *             [Y] => 0
 *             [Width] => 612
 *             [Height] => 792
 *             [raw] => TrimBox:            0.00     0.00   612.00   792.00
 *         )
 *     [ArtBox] => XpdfCliTools\PdfInfo\Model\PdfInfoBoxModel Object
 *         (
 *             [X] => 0
 *             [Y] => 0
 *             [Width] => 612
 *             [Height] => 792
 *             [raw] => ArtBox:             0.00     0.00   612.00   792.00
 *         )
 *     [FileSize] => XpdfCliTools\PdfInfo\Model\PdfInfoFileSizeModel Object
 *         (
 *             [Bytes] => 9146
 *             [raw] => 9146 bytes
 *         )
 *     [Optimized] => 1
 *     [PDFVersion] => 1.6
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
