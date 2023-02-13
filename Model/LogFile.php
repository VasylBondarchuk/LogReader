<?php

namespace Training\LogReader\Model;

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
    private File $driverFile;
    /**
     * 
     * @var FileFactory
     */
    private FileFactory $fileFactory;   

    public function __construct(
            RequestInterface $request,
            File $driverFile,
            FileFactory $fileFactory,
    ) {
        $this->request = $request;
        $this->driverFile = $driverFile;
        $this->fileFactory = $fileFactory;        
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
        foreach ($this->getFileLines($this->getFilePath()) as $row) {
            $fileContentArray[] = $row;
        }
        return $fileContentArray;
    }   

    /**
     * 
     * @param string $filename
     */
    private function getFileLines(string $filename) {
        $file = fopen($filename, 'r');
        while (($line = fgets($file)) !== false) {
            yield $line;
        }
        fclose($file);
    }

    /**
     * 
     * @return int
     */
    public function getFileTotalLinesQty(): int {
        return count($this->getFileContent());
    }

    /**
     * 
     */
    public function downloadFile() {
        $downloadedFileName = $this->getFileNameFromUrl($this->getFilePath()) . '_' . date('Y/m/d H:i:s');
        $fileContent = $this->driverFile->fileGetContents($this->getFilePath());
        $this->fileFactory->create($downloadedFileName, $fileContent, DirectoryList::ROOT, 'application/octet-stream');
    }
}
