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
 * PDF info file size model
 */
class PdfInfoFileSizeModel
{
    /**
     * @var int File size in bytes
     */
    public $Bytes = 0;

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
