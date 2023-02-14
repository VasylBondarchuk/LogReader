<?php

namespace Training\LogReader\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Training\LogReader\Configs;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Description of LogFile
 *
 * @author vasyl
 */
class LogFile {

    /**
     * 
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * 
     * @var File
     */
    private File $file;

    /**
     * 
     * @var FileFactory
     */
    private FileFactory $fileFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
            RequestInterface $request,
            File $file,
            FileFactory $fileFactory,
            ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->file = $file;
        $this->fileFactory = $fileFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * 
     * @return string
     */
    public function getFileNameFromUrl(): string {
        return $this->request->getParam(Configs::FILE_NAME_REQUEST_FIELD);
    }

    /**
     * 
     * @return int
     */
    public function getLastLinesQtyFromUrl(): int {
        return (int) $this->request->getParam(Configs::LINES_QTY_REQUEST_FIELD);
    }

    /**
     * 
     * @return int
     */
    public function getLastLinesQty(): int {
        $lastLinesQty = $this->getDefaultLastLinesQty();
        $lastLinesQtyFromUrl = $this->getLastLinesQtyFromUrl();
        $correctQty = ($lastLinesQtyFromUrl < $this->getFileTotalLinesQty()) && $lastLinesQtyFromUrl > 0;
        if ($correctQty) {
            $lastLinesQty = $lastLinesQtyFromUrl;
        }
        return $lastLinesQty;
    }

    /**
     * 
     * @return int
     */
    public function getDefaultLastLinesQty(): int {
        return $this->isLastLinesQtyValid(
                        $this->scopeConfig->getValue(Configs::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE)) ? (int) $this->scopeConfig->getValue(Configs::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE) : Configs::DEFAULT_LINES_QTY;
    }

    /**
     * 
     * @return int
     */
    public function isLastLinesQtyValid(string $linesQty): bool {
        return !empty($linesQty) && (int) $linesQty > 0;
    }

    /**
     * 
     * @return string
     */
    public function getFilePath(): string {
        return Configs::LOG_DIR_PATH . DIRECTORY_SEPARATOR . $this->getFileNameFromUrl();
    }

    /**
     * 
     * @return array
     */
    public function getFileContent(): array {
        $fileContentArray = [];
        foreach ($this->getFileLinesGenerator($this->getFilePath()) as $row) {
            $fileContentArray[] = $row;
        }
        return $fileContentArray;
    }

    /**
     * 
     * @param string $filename
     */
    private function getFileLinesGenerator(string $filename) {

        $file = $this->file->fileOpen($filename, 'r');
        while (($line = fgets($file)) !== false) {
            yield $line;
        }
        $this->file->fileClose($file);
    } 

    /**
     * 
     * @return int
     */
    public function getFileTotalLinesQty(): int {
        return count($this->getFileContent());
        return 1;
    }

    /**
     * 
     */
    public function downloadFile() {
        $downloadedFileName = $this->getFileNameFromUrl($this->getFilePath()) . '_' . date('Y/m/d H:i:s');
        $fileContent = $this->file->fileGetContents($this->getFilePath());
        $this->fileFactory->create($downloadedFileName, $fileContent, DirectoryList::ROOT, 'application/octet-stream');
    }

    /**
     * 
     * @param string $directoryPath
     * @return array
     */
    public function getFilesInDirectory(string $directoryPath): array {
        $fileNames = [];
        $content = $this->file->readDirectory($directoryPath);

        foreach ($content as $item) {
            if ($this->file->isFile($item)) {
                $fileNames[] = $this->getFileName($item);
            }
        }
        return $fileNames;
    }

    /**
     * 
     * @param string $filePath
     * @return string
     */
    public function getFileSize(string $filePath): string {
        $result = '';
        $bytes = floatval($this->file->stat($filePath)['size']);

        $arBytes = [
            ["UNIT" => "TB", "VALUE" => pow(1024, 4)],
            ["UNIT" => "GB", "VALUE" => pow(1024, 3)],
            ["UNIT" => "MB", "VALUE" => pow(1024, 2)],
            ["UNIT" => "KB", "VALUE" => 1024],
            ["UNIT" => "B", "VALUE" => 1]
        ];

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem['VALUE']) {
                $result = $bytes / $arItem['VALUE'];
                $result = str_replace('.', ',', (string) (round($result, 2))) . ' ' . $arItem['UNIT'];
                break;
            }
        }
        return $result ? $result : "0 B";
    }

    /**
     * 
     * @param type $filePath
     * @return string
     */
    public function getModificationTime($filePath): string {
        return date("l, dS F, Y, h:ia", $this->file->stat($filePath)['mtime']);
    }

    /**
     * 
     * @param string $directoryPath
     * @return int
     */
    public function getFilesNumber(string $directoryPath): int {
        return count($this->getFilesInDirectory($directoryPath));
    }

    /**
     * 
     * @param string $filePath
     * @return string
     */
    public function getFileName(string $filePath): string {
        return basename($filePath);
    }

    /**
     * 
     * @return string
     */
    public function displayFileContent(): string {

        $firstDisplayedLineNumber = $this->getFileTotalLinesQty() - $this->getLastLinesQty() + 1;
        $outputHtml = '';
        foreach ($this->getFileContentArray() as $lineIndex => $lineText) {
            $lineNumber = $firstDisplayedLineNumber + $lineIndex;
            $outputHtml .= $this->getFormattedLine($lineNumber, $lineText);
        }
        return $outputHtml;
    }

    /**
     * 
     * @param int $lineNumber
     * @param string $lineText
     * @param string $lineSeparator
     * @return string
     */
    public function getFormattedLine(int $lineNumber, string $lineText, string $lineSeparator = '<br />'): string {

        $outPutFormat = "%s $lineText %s";
        return sprintf($outPutFormat, $this->getLinePrefix($lineNumber), $lineSeparator);
    }

    /**
     * 
     * @param int $lineNumber
     * @return string|null
     */
    private function getLinePrefix(int $lineNumber): ?string {
        return $this->addLineNumber() ? '<b>Line #' . $lineNumber . '</b>' : '';
    }

    /**
     * 
     * @return array
     */
    public function getFileContentArray(): array {
        $resource = $this->file->fileOpen($this->getFilePath(),'r');
        // Real maximum length of your records
        $maxLineLength = 1000; 
        // Moves cursor back from the end of file
        fseek($resource, -$maxLineLength * $this->getLastLinesQty(), SEEK_END);  
        $res = [];
        while (($buffer = fgets($resource, $maxLineLength)) !== false) {
            $res[] = $buffer;
        }
        $this->file->fileClose($resource);
        //print_r($res);exit;
        return array_slice($res, -$this->getLastLinesQty());
    }

    /**
     * 
     * @return bool
     */
    private function addLineNumber(): bool {
        return (bool) $this->scopeConfig->getValue(Configs::ADD_LINES_NUMBER_CONFIGS_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return bool
     */
    public function isLogFileText(): bool {
        return $this->isLogFileExists()
                ? explode("/", mime_content_type($this->getFilePath()))[0] === 'text'
                : false;
    }

    /**
     * 
     * @return bool
     */
    public function isLogFileExists(): bool {
        return $this->file->isExists($this->getFilePath());
    }

    /**
     * 
     * @return bool
     */
    public function isLogFileReadable(): bool {
        return $this->isLogFileExists()
                ? $this->file->isReadable($this->getFilePath())
                : false;
    }

    /**
     * 
     * @return bool
     */
    public function isLogFileValid(): bool {
        return $this->isLogFileExists() &&
                $this->isLogFileReadable() &&
                $this->isLogFileText();
    }

}
