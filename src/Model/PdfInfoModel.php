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

namespace XpdfCliTools\PdfInfo\Model;

/**
 * PDF info model
 */
class PdfInfoModel
{
    /**
     * @var string PDF creator
     */
    public $Creator = '';

    /**
     * @var string PDF producer
     */
    public $Producer = '';

    /**
     * @var string Creation date
     */
    public $CreationDate = '';

    /**
     * @var string Last modified date
     */
    public $ModDate = '';

    /**
     * @var bool Is the PDF tagged?
     */
    public $Tagged = false;

    /**
     * @var string PDF has form(s)?
     */
    public $Form = '';

    /**
     * @var int Number of PDF pages
     */
    public $Pages = 0;

    /**
     * @var bool Is pdf encrypted?
     */
    public $Encrypted = false;

    /**
     * @var PdfInfoPageSizeModel PDF page size
     */
    public $PageSize;

    /**
     * @var PdfInfoBoxModel Media box
     */
    public $MediaBox;

    /**
     * @var PdfInfoBoxModel Crop box
     */
    public $CropBox;

    /**
     * @var PdfInfoBoxModel Bleed box
     */
    public $BleedBox;

    /**
     * @var PdfInfoBoxModel Trim box
     */
    public $TrimBox;

    /**
     * @var PdfInfoBoxModel Art box
     */
    public $ArtBox;

    /**
     * @var PdfInfoFileSizeModel PDF file size
     */
    public $FileSize;

    /**
     * @var bool Is PDF optimized?
     */
    public $Optimized = false;

    /**
     * @var string PDF version
     */
    public $PDFVersion = '';

    /**
     * @var string Raw shell output
     */
    public $raw = '';

    /**
     * Convert the pdf info model into a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->raw;
    }
}
