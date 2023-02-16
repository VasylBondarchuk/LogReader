<?php

declare(strict_types=1);

namespace Training\LogReader\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\LogReader\Model\LogFile;
use Training\LogReader\Model\Lines;

/**
 * Class DataProvider
 */
class Form extends AbstractDataProvider {

    /**
     * 
     * @var LogFile
     */
    private LogFile $logFileModel;
    
    /**
     * 
     * @var LogFile
     */
    private Lines $lines;
    
    public function __construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            LogFile $logFileModel,
            Lines $lines,    
            array $meta = [],
            array $data = []
    ) {
        $this->logFileModel = $logFileModel;
        $this->lines = $lines;     
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array {
        $result = [
            $this->logFileModel->getFileNameFromUrl() =>
            [
                'file_name' => $this->logFileModel->getFileNameFromUrl(),
                'file_size' => $this->logFileModel->getFileSize($this->logFileModel->getFilePath()),
                'modified_at' => $this->logFileModel->getModificationTime($this->logFileModel->getFilePath()),
                'total_lines_qty' => $this->lines->getFileTotalLinesQty(),
                'lines_qty' => $this->lines->getLastLinesQty()                
            ]
        ];
        return $result;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter) {
        return;
    }

}
