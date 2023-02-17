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
            ['value' => '0', 'label' => __('bold')],
            ['value' => '1', 'label' => __('italic')],
            ['value' => '2', 'label' => __('one line break')],            
        ];
    }    

}
