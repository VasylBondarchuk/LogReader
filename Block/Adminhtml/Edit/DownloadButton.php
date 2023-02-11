<?php

declare(strict_types=1);

namespace Training\LogReader\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;

/**
 * Provides data for 'Back' button
 */
class DownloadButton extends GenericButton implements ButtonProviderInterface {

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlInterface;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    public function __construct(
            Context $context,
            UrlInterface $urlInterface,
            RequestInterface $request
    ) {
        $this->urlInterface = $urlInterface;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getButtonData(): array {
        return [
            'label' => __('Download'),
            'on_click' => sprintf("location.href = '%s';", $this->getDownloadUrl()),            
            'sort_order' => 30
        ];
    }

    private function getDownloadUrl() {
        return $this->urlInterface->getUrl('logfiles/display/download',
                        ['file_name' => $this->getFileName()]);
    }

    private function getFileName(): string {
        $fileName = $this->request->getParam('file_name');
        return $fileName;
    }

}
