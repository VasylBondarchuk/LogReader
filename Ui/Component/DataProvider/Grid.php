<?php

declare(strict_types=1);

namespace Training\LogReader\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\LogReader\Model\LogFile;
use Training\LogReader\Configs;

class Grid extends AbstractDataProvider {

    /**
     * 
     * @var LogFile
     */
    private LogFile $logFileModel;

    public function __construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            LogFile $logFileModel,
            array $meta = [],
            array $data = []
    ) {
        $this->logFileModel = $logFileModel;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array {
        $result = [
            'items' => $this->getFilesDetailsArray(Configs::LOG_DIR_PATH),
            'totalRecords' => $this->logFileModel->getFilesNumber(Configs::LOG_DIR_PATH)
        ];
        return $result;
    }

    public function getFilesDetailsArray(string $directoryPath): array {
        $filesDetailsArray = [];

        foreach ($this->logFileModel->getFilesInDirectory($directoryPath) as $fileName) {
            $filesDetailsArray[] = [
                'file_name' => $fileName,
                'file_size' => $this->logFileModel->getFileSize($directoryPath . DIRECTORY_SEPARATOR . $fileName),
                'modified_at' => $this->logFileModel->getModificationTime($directoryPath . DIRECTORY_SEPARATOR . $fileName)
            ];
        }
        return $filesDetailsArray;
    }
}
