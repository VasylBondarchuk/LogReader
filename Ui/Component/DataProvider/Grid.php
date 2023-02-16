<?php

declare(strict_types=1);

namespace Training\LogReader\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\LogReader\Model\FileStatisticsCollector;
use Training\LogReader\Model\Config\Configs;

class Grid extends AbstractDataProvider {

   /**
    * 
    * @var FileStatisticsCollector
    */
    private FileStatisticsCollector $fileStatCollector;

    public function __construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            FileStatisticsCollector $fileStatCollector,
            array $meta = [],
            array $data = []
    ) {
        $this->fileStatCollector = $fileStatCollector;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * 
     * @return array
     */
    public function getData(): array {
        $result = [
            'items' => $this->getFilesDetailsArray(Configs::LOG_DIR_PATH),
            'totalRecords' => $this->fileStatCollector->getFilesNumber(Configs::LOG_DIR_PATH)
        ];
        return $result;
    }

    /**
     * 
     * @param string $directoryPath
     * @return array
     */
    public function getFilesDetailsArray(string $directoryPath): array {
        $filesDetailsArray = [];
        foreach ($this->fileStatCollector->getFilesNamesInDirectory($directoryPath) as $fileName) {
            $filesDetailsArray[] = [
                'file_name' => $fileName,
                'file_size' => $this->fileStatCollector->getFileSize($directoryPath . DIRECTORY_SEPARATOR . $fileName),
                'modified_at' => $this->fileStatCollector->getModificationTime($directoryPath . DIRECTORY_SEPARATOR . $fileName)
            ];
        }
        return $filesDetailsArray;
    }
}
