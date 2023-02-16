<?php

declare(strict_types=1);

namespace Training\LogReader\Model;

use Training\LogReader\Model\Config\Configs;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Description of File
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

    
    public function readFileToCollection(int $lineToStartReading, int $linesToRead){
        return new \LimitIterator(
                    new \SplFileObject($this->getFilePath()),
                    $lineToStartReading,
                    $linesToRead
                );       
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
        return date($this->configs->getTimeFormat(), $this->file->stat($filePath)['mtime']);
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
