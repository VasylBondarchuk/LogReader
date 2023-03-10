<?php

declare(strict_types=1);

namespace Training\LogReader\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class Actions extends Column {

    /**
     * 
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    public function __construct(
            ContextInterface $context,
            UiComponentFactory $uiComponentFactory,
            UrlInterface $urlBuilder,
            array $components = [],
            array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * 
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = [
                    'view' => [
                        'href' => $this->urlBuilder->getUrl('logfiles/display/view', ['file_name' => $item['file_name']]),
                        'label' => __('View')
                    ],
                    'download' => [
                        'href' => $this->urlBuilder->getUrl('logfiles/display/download', ['file_name' => $item['file_name']]),
                        'label' => __('Download')
                    ]
                ];
            }
        }
        return $dataSource;
    }

}
