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

    private RequestInterface $request;
    private File $driverFile;
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

    public function getFileNameFromUrl(): string {
        return $this->request->getParam('file_name');
    }

    public function getLastLinesQtyFromUrl(): int {
        return (int) $this->request->getParam('lines_qty');
    }

    public function getFilePath(): string {
        return Configs::LOG_DIR_PATH . DIRECTORY_SEPARATOR . $this->getFileNameFromUrl();
    }

    public function getFileContent(): array {
        $fileContentArray = [];
        foreach ($this->getFileRows($this->getFilePath()) as $row) {
            $fileContentArray[] = $row;
        }
        return $fileContentArray;
    }

    public function displayFileContent() {
        $fileContentArray = $this->getFileContent();
        $size = count($fileContentArray);
        $outputHtml = '';
        for ($i = 0; $i < $this->getLastLinesQty(); $i++) {
            $outputHtml .= '<b> Line # ' . $size - $this->getLastLinesQty() + $i + 1 . '</b> : '
                    . $fileContentArray[$size - $this->getLastLinesQty() + $i] . '<br />';
        }
        return $outputHtml;
    }

    private function getFileRows($filename) {
        $file = fopen($filename, 'r');
        while (($line = fgets($file)) !== false) {
            yield $line;
        }
        fclose($file);
    }

    public function getFileTotalLinesQty(): int {
        return count($this->getFileContent());
    }    
}
