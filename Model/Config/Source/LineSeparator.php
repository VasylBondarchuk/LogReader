<?php

declare(strict_types=1);

namespace Training\LogReader\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Description of DateFormat
 *
 * @author vasyl
 */
class LineSeparator implements ArrayInterface {

    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('One  line break')],
            ['value' => '1', 'label' => __('Double line break')],
            ['value' => '2', 'label' => __('Horizontal line')],
        ];
    }    

}
