<?php

declare(strict_types=1);

namespace Training\LogReader\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Description of DateFormat
 *
 * @author vasyl
 */
class LineNumberFormat implements ArrayInterface {

    public function toOptionArray()
    {
        return [
            ['value' => 'b', 'label' => __('bold')],
            ['value' => 'i', 'label' => __('italic')]            
        ];
    }    

}
