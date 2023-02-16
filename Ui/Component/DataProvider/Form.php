<?php

declare(strict_types=1);

namespace Training\LogReader\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\LogReader\Model\FileStatisticsCollector;
use Training\LogReader\Model\FileLineFormatter;

/**
 * Class DataProvider
 */
class Form extends AbstractDataProvider {


    private FileStatisticsCollector $fileStatCollector;
    
    /**
     * 
     * @var File
     */
    private FileLineFormatter $fileLineFormatter;
    
    public function __construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            FileStatisticsCollector $fileStatCollector,
            FileLineFormatter $fileLineFormatter,    
            array $meta = [],
            array $data = []
    ) {
        $this->fileStatCollector = $fileStatCollector;
        $this->fileLineFormatter = $fileLineFormatter;     
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array {
        $result = [
            $this->fileStatCollector->getFileNameFromUrl() =>
            [
                'file_name' => $this->fileStatCollector->getFileNameFromUrl(),
                'file_size' => $this->fileStatCollector->getFileSize($this->fileStatCollector->getFilePath()),
                'modified_at' => $this->fileStatCollector->getModificationTime($this->fileStatCollector->getFilePath()),
                'total_lines_qty' => $this->fileLineFormatter->getFileTotalLinesQty(),
                'lines_qty' => $this->fileLineFormatter->getLastLinesQty()                
            ]
        ];
        return $result;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter) {
        return;
    }

}
