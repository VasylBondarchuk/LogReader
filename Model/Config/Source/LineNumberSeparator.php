<?php

declare(strict_types=1);

namespace Training\LogReader\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Description of DateFormat
 *
 * @author vasyl
 */
class LineNumberSeparator implements ArrayInterface {

    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('one non-breaking space')],
            ['value' => 2, 'label' => __('double non-breaking space')]            
        ];
    }    

}
