<?php
declare(strict_types = 1);

namespace Training\LogReader\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\LogReader\Configs;

class Grid extends AbstractDataProvider
{
    private $fileScanner;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        FileScanner $fileScanner,
        array $meta = [],
        array $data = []
    ) {
        $this->fileScanner = $fileScanner;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getFilesDetailsArray(string $directoryPath) : array
    {
        $filesDetailsArray = [];

        foreach ($this->fileScanner->getFilesInDirectory($directoryPath) as $fileName) {
            $filesDetailsArray[] = [
                'file_name' => $fileName,
                'file_size' => $this->fileScanner->getFileSize($directoryPath . DIRECTORY_SEPARATOR . $fileName),
                'modified_at' => $this->fileScanner->getModificationTime($directoryPath . DIRECTORY_SEPARATOR . $fileName)
            ];
        }
        return $filesDetailsArray;
    }

    public function getData() : array
    {
        $result = [
            'items' => $this->getFilesDetailsArray(Configs::LOG_DIR_PATH),
            'totalRecords' => $this->fileScanner->getFilesNumber(Configs::LOG_DIR_PATH)
        ];
        return $result;
    }
}
