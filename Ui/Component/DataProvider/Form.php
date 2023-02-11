<?php
declare(strict_types=1);

namespace Training\LogReader\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\LogReader\Configs;

/**
 * Class DataProvider
 */
class Form extends AbstractDataProvider {
    
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

    public function getData() : array
    {
        $result = array ( 0 => array('file_name' => 'debug.log', 'lines_qty' => '2' ));
        return $result;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter) {
        return;
    }

}
