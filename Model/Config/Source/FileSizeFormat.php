<?php

declare(strict_types=1);

namespace Training\LogReader\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Description of DateFormat
 *
 * @author vasyl
 */
class FileSizeFormat implements ArrayInterface {

    public function toOptionArray()
    {
        return [
            ['value' => 1000, 'label' => __('Decimal (in base 10)')],
            ['value' => 1024, 'label' => __('Binary (in base 2)')]
            
        ];
    }    

}
