<?php
/**
 *   ___ ___  ___  ___ _    _    ___ _  _  ___ ___ ___
 *  / __/ __|/ _ \| __| |  | |  |_ _| \| |/ __| __| _ \
 * | (__\__ \ (_) | _|| |__| |__ | || .` | (_ | _||   /
 *  \___|___/\___/|___|____|____|___|_|\_|\___|___|_|_\
 *
 * PHP version 7.*
 *
 * @author    Christopher Söllinger <christopher.soellinger@gmail.com>
 *
 * @see https://github.com/CSoellinger/php-xpdf-cli-pdfinfo
 */

declare(strict_types=1);

namespace Tests\XpdfCliTools\PdfInfo;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use XpdfCliTools\PdfInfo\Model\PdfInfoFileSizeModel;
use XpdfCliTools\PdfInfo\Model\PdfInfoModel;
use XpdfCliTools\PdfInfo\Model\PdfInfoPageSizeModel;
use XpdfCliTools\PdfInfo\PdfInfo;

/**
 * Testing pdf info class.
 *
 * @covers \XpdfCliTools\PdfInfo\PdfInfo
 */
class PdfInfoTest extends TestCase
{
    /**
     * @var string Path to example pdf
     */
    private $examplePdf = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'test.pdf';

    /**
     * @var string Path to example pdf
     */
    private $exampleProtectedPdf = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'test-protected.pdf';

    private $pdfPassword = 'Pass12345678';

    /**
     * @var string Path to wrong example pdf
     */
    private $exampleWrongPdf = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'test.png';

    /**
     * @var array Testdata
     */
    private $testData = [
        'Creator' => 'Acrobat Pro DC 20.6.20034',
        'Producer' => 'Acrobat Pro DC 20.6.20034',
        'CreationDate' => 'Mon Feb 17 17:16:36 2020',
        'ModDate' => 'Mon Feb 17 17:18:29 2020',
        'Tagged' => false,
        'Form' => 'none',
        'Pages' => 1,
        'Encrypted' => false,
        'PageSize' => [
            'WidthPts' => 612,
            'HeightPts' => 792,
            'Format' => 'letter',
            'RotatedDegrees' => 0,
            'raw' => 'Page size:      612 x 792 pts (letter) (rotated 0 degrees)',
        ],
        'FileSize' => [
            'Bytes' => 9146,
            'raw' => 'File size:      9146 bytes',
        ],
        'Optimized' => true,
        'PDFVersion' => '1.6',
        'raw' => "Creator:        Acrobat Pro DC 20.6.20034\n" .
                 "Producer:       Acrobat Pro DC 20.6.20034\n" .
                 "CreationDate:   Mon Feb 17 17:16:36 2020\n" .
                 "ModDate:        Mon Feb 17 17:18:29 2020\n" .
                 "Tagged:         no\n" .
                 "Form:           none\n" .
                 "Pages:          1\n" .
                 "Encrypted:      no\n" .
                 "Page size:      612 x 792 pts (letter) (rotated 0 degrees)\n" .
                 "MediaBox:           0.00     0.00   612.00   792.00\n" .
                 "CropBox:            0.00     0.00   612.00   792.00\n" .
                 "BleedBox:           0.00     0.00   612.00   792.00\n" .
                 "TrimBox:            0.00     0.00   612.00   792.00\n" .
                 "ArtBox:             0.00     0.00   612.00   792.00\n" .
                 "File size:      9146 bytes\n" .
                 "Optimized:      yes\nPDF version:    1.6",
    ];

    /**
     * Test pdf info with an existing file.
     *
     * @testdox Test pdf info with an existing file.
     *
     * @covers \XpdfCliTools\PdfInfo\PdfInfo
     */
    public function testWithFileExist(): void
    {
        $pdfInfo = new PdfInfo();
        $info = $pdfInfo->exec($this->examplePdf);

        $this->assertInstanceOf(PdfInfoModel::class, $info);

        $this->assertEquals($this->testData['Creator'], $info->Creator);
        $this->assertEquals($this->testData['Producer'], $info->Producer);
        $this->assertEquals($this->testData['CreationDate'], $info->CreationDate);
        $this->assertEquals($this->testData['ModDate'], $info->ModDate);
        $this->assertEquals($this->testData['Tagged'], $info->Tagged);
        $this->assertEquals($this->testData['Form'], $info->Form);
        $this->assertEquals($this->testData['Pages'], $info->Pages);
        $this->assertEquals($this->testData['Encrypted'], $info->Encrypted);
        $this->assertEquals($this->testData['Optimized'], $info->Optimized);
        $this->assertEquals($this->testData['PDFVersion'], $info->PDFVersion);
        $this->assertEquals($this->testData['raw'], $info->raw);

        if (isset($this->testData['PageSize'])) {
            $this->assertInstanceOf(PdfInfoPageSizeModel::class, $info->PageSize);
            $this->assertEquals($this->testData['PageSize']['WidthPts'], $info->PageSize->WidthPts);
            $this->assertEquals($this->testData['PageSize']['HeightPts'], $info->PageSize->HeightPts);
            $this->assertEquals($this->testData['PageSize']['Format'], $info->PageSize->Format);
            $this->assertEquals($this->testData['PageSize']['RotatedDegrees'], $info->PageSize->RotatedDegrees);
            $this->assertEquals($this->testData['PageSize']['raw'], $info->PageSize->raw);
        }

        if (isset($this->testData['FileSize'])) {
            $this->assertInstanceOf(PdfInfoFileSizeModel::class, $info->FileSize);
            $this->assertEquals($this->testData['FileSize']['Bytes'], $info->FileSize->Bytes);
            $this->assertEquals($this->testData['FileSize']['raw'], $info->FileSize->raw);
        }
    }

    /**
     * Test pdf info with an existing protected file but with no password/user.
     *
     * @testdox Test pdf info with an existing protected file but with no password/user.
     *
     * @covers \XpdfCliTools\PdfInfo\PdfInfo
     *
     * @throws ProcessFailedException
     */
    public function testWithProtectedFile()
    {
        $this->expectException(ProcessFailedException::class);

        $info = new PdfInfo();
        $info->exec($this->exampleProtectedPdf, $this->pdfPassword);

        $this->assertInstanceOf(PdfInfoModel::class, $info);

        $this->assertEquals($this->testData['Creator'], $info->Creator);
        $this->assertEquals($this->testData['Producer'], $info->Producer);
        $this->assertEquals($this->testData['CreationDate'], $info->CreationDate);
        $this->assertEquals($this->testData['ModDate'], $info->ModDate);
        $this->assertEquals($this->testData['Tagged'], $info->Tagged);
        $this->assertEquals($this->testData['Form'], $info->Form);
        $this->assertEquals($this->testData['Pages'], $info->Pages);
        $this->assertEquals($this->testData['Encrypted'], $info->Encrypted);
        $this->assertEquals($this->testData['Optimized'], $info->Optimized);
        $this->assertEquals($this->testData['PDFVersion'], $info->PDFVersion);
        $this->assertEquals($this->testData['raw'], $info->raw);

        if (isset($this->testData['PageSize'])) {
            $this->assertInstanceOf(PdfInfoPageSizeModel::class, $info->PageSize);
            $this->assertEquals($this->testData['PageSize']['WidthPts'], $info->PageSize->WidthPts);
            $this->assertEquals($this->testData['PageSize']['HeightPts'], $info->PageSize->HeightPts);
            $this->assertEquals($this->testData['PageSize']['Format'], $info->PageSize->Format);
            $this->assertEquals($this->testData['PageSize']['RotatedDegrees'], $info->PageSize->RotatedDegrees);
            $this->assertEquals($this->testData['PageSize']['raw'], $info->PageSize->raw);
        }

        if (isset($this->testData['FileSize'])) {
            $this->assertInstanceOf(PdfInfoFileSizeModel::class, $info->FileSize);
            $this->assertEquals($this->testData['FileSize']['Bytes'], $info->FileSize->Bytes);
            $this->assertEquals($this->testData['FileSize']['raw'], $info->FileSize->raw);
        }
    }

    /**
     * Test pdf info with an existing protected file but with no password/user.
     *
     * @testdox Test pdf info with an existing protected file but with no password/user.
     *
     * @covers \XpdfCliTools\PdfInfo\PdfInfo
     *
     * @throws ProcessFailedException
     */
    public function testWithProtectedFileAndWrongPassword()
    {
        $this->expectException(ProcessFailedException::class);

        $pdfInfo = new PdfInfo();
        $pdfInfo->exec($this->exampleProtectedPdf);
    }

    /**
     * Test with wrong bin path.
     *
     * @testdox Test with wrong bin path.
     *
     * @covers \XpdfCliTools\PdfInfo\PdfInfo
     *
     * @throws Exception
     */
    public function testWithWrongBinPath(): void
    {
        $this->expectException(Exception::class);

        $info = new PdfInfo('xxx');

        $this->assertInstanceOf(PdfInfo::class, $info);
    }

    /**
     * Test with file not exists.
     *
     * @testdox Test with file not exists.
     *
     * @covers \XpdfCliTools\PdfInfo\PdfInfo
     *
     * @throws Exception
     */
    public function testWithFileNotExists(): void
    {
        $this->expectException(Exception::class);

        $pdfInfo = new PdfInfo();
        $pdfInfo->exec($this->examplePdf . '.not-exists');
    }

    /**
     * Test with wrong pdf.
     *
     * @testdox Test with wrong pdf.
     *
     * @covers \XpdfCliTools\PdfInfo\PdfInfo
     *
     * @throws ProcessFailedException
     */
    public function testWithWrongPdf(): void
    {
        $this->expectException(ProcessFailedException::class);

        $pdfInfo = new PdfInfo();
        $pdfInfo->exec($this->exampleWrongPdf);
    }
}
