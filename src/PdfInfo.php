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
use XpdfCliTools\PdfInfo\Model\PdfInfoBoxModel;
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
     * @param string $file          PDF file to read.
     * @param string $ownerPassword PDF owner password.
     * @param string $userPassword  PDF user password.
     *
     * @throws Exception
     * @throws ProcessFailedException
     */
    public function exec(string $file = '', string $ownerPassword = null, string $userPassword = null): PdfInfoModel
    {
        // If pdf not exists throw an exception
        if (file_exists($file) === false) {
            throw new Exception('Pdf file not found: ' . $file, 1);
        }

        $command = [];
        $command[] = $this->binPath;
        $command[] = '-box';

        if (is_string($ownerPassword) && $ownerPassword !== '') {
            $command[] = '-opw';
            $command[] = $ownerPassword;
        }

        if (is_string($userPassword) && $userPassword !== '') {
            $command[] = '-upw';
            $command[] = $userPassword;
        }

        $command[] = $file;

        $process = new Process($command);
        $process->run();

        // On execution error we also throw an exception
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $pdfInfoOutput = trim($process->getOutput());

        $pdfInfoModel = new PdfInfoModel();
        $pdfInfoModel->raw = $pdfInfoOutput;

        $positions = [
            'Creator',
            'Producer',
            'CreationDate',
            'ModDate',
            'Tagged',
            'Form',
            'Pages',
            'Encrypted',
            'Optimized',
            'PDF version',
        ];

        foreach($positions as $pos) {
            $objectName = preg_replace('/\s*/', '', ucwords($pos));

            $match = [];

            $regexGroup = '(.*)';

            if (in_array($pos, ['Pages'])) {
                $regexGroup = '(\d*)';
            }

            if (in_array($pos, ['Tagged', 'Encrypted', 'Optimized'])) {
                $regexGroup = '(no|yes)';
            }

            $regex = '/^' . $pos . ':\s*' . $regexGroup . '$/m';

            preg_match_all($regex, $pdfInfoOutput, $match);

            if (property_exists($pdfInfoModel, $objectName) && count($match[1]) > 0) {
                if (in_array($pos, ['Tagged', 'Encrypted', 'Optimized'])) {
                    $match[1][0] = $match[1][0] === 'yes' ? true : false;
                }

                $pdfInfoModel->$objectName = $match[1][0];
            }
        }

        // Page size
        $match = [];
        $regex = "/^Page size:\s*(?'Width'[\d\.\,]*) x (?'Height'[\d\.\,]*) pts( \((?'Format'[\w]*)\))?( \(rotated (?'RotatedDegrees'[\d\.\,\-]*) degrees\))?$/m";
        preg_match_all($regex, $pdfInfoOutput, $match);

        $pdfInfoModel->PageSize = new PdfInfoPageSizeModel();
        $pdfInfoModel->PageSize->Width = (float) $match['Width'][0];
        $pdfInfoModel->PageSize->Height = (float) $match['Height'][0];
        $pdfInfoModel->PageSize->Format = $match['Format'][0];
        $pdfInfoModel->PageSize->RotatedDegrees = (int) $match['RotatedDegrees'][0];
        $pdfInfoModel->PageSize->raw = $match[0][0];

        $match = [];
        $regex = '/^File size:\s*(.*)$/m';
        preg_match_all($regex, $pdfInfoOutput, $match);

        $pdfInfoModel->FileSize = new PdfInfoFileSizeModel();
        $pdfInfoModel->FileSize->raw = $match[0][0];
        $pdfInfoModel->FileSize->Bytes = (int) trim(str_replace(['bytes', 'byte'], '', $match[1][0]));

        foreach(['MediaBox', 'CropBox', 'BleedBox', 'TrimBox', 'ArtBox'] as $box) {
            $match = [];
            $regex = '/^' . $box . ':\s*([\d,\.\-]*)\s*([\d,\.\-]*)\s*([\d,\.\-]*)\s*([\d,\.\-]*)$/m';
            preg_match_all($regex, $pdfInfoOutput, $match);

            $pdfInfoModel->$box = new PdfInfoBoxModel();
            $pdfInfoModel->$box->raw = $match[0][0];
            $pdfInfoModel->$box->X = (float) $match[1][0];
            $pdfInfoModel->$box->Y = (float) $match[2][0];
            $pdfInfoModel->$box->Width = (float) $match[3][0];
            $pdfInfoModel->$box->Height = (float) $match[4][0];
        }

        return $pdfInfoModel;
    }
}
