<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Training\LogReader\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;


/**
 * Class DataProvider
 */
class Form extends AbstractDataProvider
{
/**
* Get data
*
* @return array
*/
public function getData()
{
        return [];
}

public function addFilter(\Magento\Framework\Api\Filter $filter)
{
     return;
}   

}

