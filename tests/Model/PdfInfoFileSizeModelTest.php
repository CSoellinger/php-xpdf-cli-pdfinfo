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

namespace Tests\XpdfCliTools\PdfInfo\Model;

use PHPUnit\Framework\TestCase;
use XpdfCliTools\PdfInfo\Model\PdfInfoFileSizeModel;

/**
 * Testing pdf info file size model.
 *
 * @covers \XpdfCliTools\PdfInfo\Model\PdfInfoFileSizeModel
 */
class PdfInfoFileSizeModelTest extends TestCase
{
    /**
     * Test pdf info file size model.
     *
     * @testdox Test pdf info file size model.
     *
     * @covers \XpdfCliTools\PdfInfo\Model\PdfInfoFileSizeModel
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testModel(): void
    {
        $pdfInfoModel = new PdfInfoFileSizeModel();
        $pdfInfoModel->raw = 'Raw output';

        $this->assertInstanceOf(PdfInfoFileSizeModel::class, $pdfInfoModel);
        $this->assertEquals('Raw output', $pdfInfoModel->__toString());
    }
}
