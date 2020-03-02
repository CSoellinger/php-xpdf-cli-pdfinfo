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
 * PDF info box model
 */
class PdfInfoBoxModel
{
    /**
     * @var int X coordinate in pts
     */
    public $X = 0;

    /**
     * @var int Y coordinate in pts
     */
    public $Y = 0;

    /**
     * @var int Width in PTS
     */
    public $Width = 0;

    /**
     * @var int Height in PTS
     */
    public $Height = 0;

    /**
     * @var string Raw shell output
     */
    public $raw = '';

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->raw;
    }
}
