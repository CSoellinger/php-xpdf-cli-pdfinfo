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

namespace XpdfCliTools\PdfInfo;

use Exception;
use insign\getOS;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use XpdfCliTools\PdfInfo\Model\PdfInfoFileSizeModel;
use XpdfCliTools\PdfInfo\Model\PdfInfoModel;
use XpdfCliTools\PdfInfo\Model\PdfInfoPageSizeModel;

/**
 * xPDF info wrapper class.
 *
 * Read the following informations from a PDF file:
 * - Producer
 * - Creation date
 * - Modified date
 * - Tagged
 * - Form
 * - Pages
 * - Encrypted
 * - Page size
 *   - Width as points
 *   - Height as points
 *   - Format
 *   - Rotated Degrees
 * - File size
 *   - Bytes
 * - Optimized
 * - PDF version
 */
class PdfInfo
{
    /**
     * Just a conversion helper to get the size in centimeter.
     */
    const MM_TO_PTS = 2.83464567;

    /**
     * Another helper to get os names by integer from getOs::ofServer().
     */
    const OS_NAMES = [
        getOS::OSX => 'osx',
        getOS::WIN => 'win',
        getOS::LINUX => 'linux',
    ];

    /**
     * @var string Executable binary path
     */
    private $binPath = '';

    /**
     * Construct.
     *
     * @param string $binPath (optional) Set a custom path to pdfinfo binary.
     *
     * @throws Exception
     */
    public function __construct($binPath = '')
    {
        if (!$binPath) {
            // Get default executable bin
            $binPath = implode(DIRECTORY_SEPARATOR, [
                __DIR__,
                '..',
                '.bin',
                'pdfinfo-' . self::OS_NAMES[getOS::ofServer()] . (getOS::ofServer() === getOS::WIN ? '.exe' : ''),
            ]);
        }

        $this->binPath = $binPath;

        // If bin file not exists throw an exception.
        if (file_exists($this->binPath) === false) {
            throw new Exception('Binary file not found: ' . $this->binPath, 1);
        }
    }

    /**
     * Execute pdf info and fetch informations.
     *
     * @param string $file PDF file to read.
     *
     * @throws Exception
     * @throws ProcessFailedException
     */
    public function exec(string $file = ''): PdfInfoModel
    {
        // If pdf not exists throw an exception
        if (file_exists($file) === false) {
            throw new Exception('Pdf file not found: ' . $file, 1);
        }

        $process = new Process([$this->binPath, $file]);
        $process->run();

        // On execution error we also throe an exception
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $pdfInfoOutput = trim($process->getOutput());
        $pdfInfoModel = new PdfInfoModel();

        $pdfInfoModel->raw = $pdfInfoOutput;

        // Regex to fetch all informations
        $regex = '';
        $regex .= '/';
        $regex .= '(Creator:\s*(?\'Creator\'(.*))\n?)?';
        $regex .= '(Producer:\s*(?\'Producer\'(.*))\n?)?';
        $regex .= '(CreationDate:\s*(?\'CreationDate\'(.*))\n?)?';
        $regex .= '(ModDate:\s*(?\'ModDate\'(.*))\n?)?';
        $regex .= '(Tagged:\s*(?\'Tagged\'(no|yes))\n?)?';
        $regex .= '(Form:\s*(?\'Form\'(.*))\n?)?';
        $regex .= '(Pages:\s*(?\'Pages\'(\d*))\n?)?';
        $regex .= '(Encrypted:\s*(?\'Encrypted\'(no|yes))\n?)?';
        $regex .= '(Page size:\s*(?\'PageSize\'(.*))\n?)?';
        $regex .= '(File size:\s*(?\'FileSize\'(.*))\n?)?';
        $regex .= '(Optimized:\s*(?\'Optimized\'(no|yes))\n?)?';
        $regex .= '(PDF version:\s*(?\'PdfVersion\'(.*))\n?)?';
        $regex .= '/';

        $matches = [];

        preg_match_all($regex, $pdfInfoOutput, $matches);

        foreach (get_object_vars($pdfInfoModel) as $key) {
            if (isset($matches[$key])) {
                if ($key === 'Tagged' || $key === 'Encrypted' || $key === 'Optimized') {
                    $matches[$key][0] = $matches[$key][0] === 'yes' ? true : false;
                }

                if ($key === 'Pages') {
                    $matches[$key][0] = (int) $matches[$key][0];
                }

                // For page size we have another model called PdfInfoPageSizeModel
                if ($key === 'PageSize') {
                    $matchesPageSize = [];

                    $regexPageSize = '';
                    $regexPageSize .= '/';
                    $regexPageSize .= '(?\'WidthPts\'[\d\.\,]*) x (?\'HeightPts\'[\d\.\,]*)';
                    $regexPageSize .= '( pts \((?\'Format\'[\w]*)\))?';
                    $regexPageSize .= '( \(rotated (?\'RotatedDegrees\'[\d\.\,\-]*) degrees\))?';
                    $regexPageSize .= '/';

                    preg_match_all($regexPageSize, (string) $matches[$key][0], $matchesPageSize);

                    $pageSize = new PdfInfoPageSizeModel();
                    foreach (['WidthPts', 'HeightPts', 'RotatedDegrees'] as $subKey) {
                        $pageSize->$subKey = isset($matchesPageSize[$subKey]) ? (float) $matchesPageSize[$subKey][0] : 0;
                    }
                    $pageSize->Format = isset($matchesPageSize['Format']) ? $matchesPageSize['Format'][0] : '';
                    $pageSize->raw = (string) $matches[$key][0];

                    $matches[$key][0] = $pageSize;
                }

                // For file size we have another model called PdfInfoFileSizeModel
                if ($key === 'FileSize') {
                    $fileSize = new PdfInfoFileSizeModel();
                    $fileSize->raw = (string) $matches[$key][0];
                    $fileSize->Bytes = (int) trim(str_replace(['byte', 'bytes'], '', (string) $matches[$key][0]));

                    $matches[$key][0] = $fileSize;
                }

                $pdfInfoModel->$key = $matches[$key][0];
            }
        }

        return $pdfInfoModel;
    }
}
