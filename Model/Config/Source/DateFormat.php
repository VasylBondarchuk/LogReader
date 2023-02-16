<?php

declare(strict_types=1);

namespace Training\LogReader\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Description of DateFormat
 *
 * @author vasyl
 */
class DateFormat implements ArrayInterface {

    public function toOptionArray()
    {
        return [
            ['value' => 'F j, Y, g:i a', 'label' => __('March 10, 2001, 5:16 pm')],
            ['value' => 'D M j G:i:s T Y', 'label' => __('Sat Mar 10 17:16:18 MST 2001')],
            ['value' => 'Y-m-d H:i:s', 'label' => __('2001-03-10 17:16:18')]
        ];
    }    

}
