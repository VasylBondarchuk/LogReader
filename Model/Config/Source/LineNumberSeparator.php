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
            ['value' => 1, 'label' => __('One non-breaking space')],
            ['value' => 2, 'label' => __('Double non-breaking space')]            
        ];
    }    

}
