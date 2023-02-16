<?php

declare(strict_types=1);

namespace Training\LogReader\Model;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Training\LogReader\Model\FileStatisticsCollector;

/**
 * Description of File
 *
 * @author vasyl
 */
class FileManager {
    
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
     * 
     * @var FileStatisticsCollector
     */
    private FileStatisticsCollector $fileStatCollector;


    public function __construct(            
            File $file,
            FileFactory $fileFactory,
            FileStatisticsCollector $fileStatCollector,            
    ) {
        $this->file = $file;
        $this->fileFactory = $fileFactory; 
        $this->fileStatCollector = $fileStatCollector;
    }

    
    /**
     * Reads file content to collection
     * 
     * @param int $lineToStartReading
     * @param int $linesToRead
     * @return \LimitIterator
     */
    public function readFile(int $lineToStartReading, int $linesToRead){
        return new \LimitIterator(
                    new \SplFileObject($this->fileStatCollector->getFilePath()),
                    $lineToStartReading,
                    $linesToRead
                );       
    }    

    /**
     * Downloads file 
     */
    public function downloadFile() {
        $downloadedFileName = $this->fileStatCollector->getFileNameFromUrl() . '_' . date('Y/m/d H:i:s');
        $fileContent = $this->file->fileGetContents($this->fileStatCollector->getFilePath());
        $this->fileFactory->create($downloadedFileName, $fileContent, DirectoryList::ROOT, 'application/octet-stream');
    }  
    
}
