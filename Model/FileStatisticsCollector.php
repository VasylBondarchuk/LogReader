<?php

declare(strict_types=1);

namespace Training\LogReader\Model;

use Training\LogReader\Model\Config\Configs;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;

/**
 * Collects files statistics
 *
 * @author vasyl
 */
class FileStatisticsCollector {

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
    * 
    * @var Configs
    */
    private Configs $configs;

    public function __construct(
            RequestInterface $request,
            File $file,
            FileFactory $fileFactory,
            Configs $configs
    ) {
        $this->request = $request;
        $this->file = $file;
        $this->fileFactory = $fileFactory;
        $this->configs = $configs;
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
     * @return string
     */
    public function getFilePath(): string {
        return Configs::LOG_DIR_PATH . DIRECTORY_SEPARATOR . $this->getFileNameFromUrl();
    }
   

    /**
     * 
     * @param string $directoryPath
     * @return array
     */
    public function getFilesNamesInDirectory(string $directoryPath): array {
        $fileNames = [];
        $content = $this->file->readDirectory($directoryPath);

        foreach ($content as $item) {
            if ($this->file->isFile($item)) {
                $fileNames[] = \basename($item);
            }
        }
        return $fileNames;
    }

    /**
     * 
     * @param string $filePath
     * @param type $precision
     * @return string
     */
    public function getFileSize(string $filePath, $precision = 2): string { 
        $kBSizeInBytes = (float)$this->configs->getFileSizeFormat();        
        $size = $this->file->stat($filePath)['size'];        
        $units = $kBSizeInBytes == 1000
                ? ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
                : ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
        $power = $size > 0 ? floor(log($size, $kBSizeInBytes)) : 0;
        return number_format($size / pow($kBSizeInBytes, $power), $precision, '.', ',') . ' ' . $units[$power];
    }  
    

    /**
     * 
     * @param type $filePath
     * @return string
     */
    public function getModificationTime(string $filePath): string {        
        return \date($this->configs->getTimeFormat(), $this->file->stat($filePath)['mtime']);
    }

    /**
     * 
     * @param string $directoryPath
     * @return int
     */
    public function getFilesNumber(string $directoryPath): int {
        return \count($this->getFilesNamesInDirectory($directoryPath));
    } 
    
}
