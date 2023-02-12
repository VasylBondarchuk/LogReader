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

    /**
     * 
     * @return string
     */
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
        return tailCustom($filepath, $lines = 10000, $adaptive = true);
        /*$fileContentArray = $this->getFileContent();
        $size = count($fileContentArray);
        $outputHtml = '';
        for ($i = 0; $i < $this->getLastLinesQty(); $i++) {
            $outputHtml .= '<b> Line # ' . $size - $this->getLastLinesQty() + $i + 1 . '</b> : '
                    . $fileContentArray[$size - $this->getLastLinesQty() + $i] . '<br />';
        }
        return $outputHtml;*/
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

    public function downloadFile() {
        $downloadedFileName = $this->getFileNameFromUrl($filePath) . '_' . date('Y/m/d H:i:s');
        $fileContent = $this->driverFile->fileGetContents($filePath);
        $this->fileFactory->create($downloadedFileName, $fileContent, DirectoryList::ROOT, 'application/octet-stream');
    }

	
    public function tailCustom($filepath, $lines = 1, $adaptive = true) {

		// Open file
		$f = @fopen($filepath, "rb");
		if ($f === false) return false;

		// Sets buffer size, according to the number of lines to retrieve.
		// This gives a performance boost when reading a few lines from the file.
		if (!$adaptive) $buffer = 4096;
		else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

		// Jump to last character
		fseek($f, -1, SEEK_END);

		// Read it and adjust line number if necessary
		// (Otherwise the result would be wrong if file doesn't end with a blank line)
		if (fread($f, 1) != "\n") $lines -= 1;
		
		// Start reading
		$output = '';
		$chunk = '';

		// While we would like more
		while (ftell($f) > 0 && $lines >= 0) {

			// Figure out how far back we should jump
			$seek = min(ftell($f), $buffer);

			// Do the jump (backwards, relative to where we are)
			fseek($f, -$seek, SEEK_CUR);

			// Read a chunk and prepend it to our output
			$output = ($chunk = fread($f, $seek)) . $output;

			// Jump back to where we started reading
			fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

			// Decrease our line counter
			$lines -= substr_count($chunk, "<br />'");

		}

		// While we have too many lines
		// (Because of buffer size we might have read too many)
		while ($lines++ < 0) {

			// Find first newline and remove all text before that
			$output = substr($output, strpos($output, "<br />'") + 1);

		}

		// Close file and return
		fclose($f);
		return trim($output);
	}
        
        
}
