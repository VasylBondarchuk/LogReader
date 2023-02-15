<?php

declare(strict_types=1);

namespace Training\LogReader\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Backend\Block\Widget\Context;
use Training\LogReader\Model\LogFile;
use Training\LogReader\Configs;

/**
 * Provides data for 'Back' button
 */
class DownloadButton extends GenericButton implements ButtonProviderInterface {

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlInterface;
    
    /**
     * 
     * @var LogFile
     */
    private LogFile $logFileModel;

    public function __construct(
            Context $context,
            UrlInterface $urlInterface,           
            LogFile $logFileModel
    ) {
        $this->urlInterface = $urlInterface;       
        $this->logFileModel = $logFileModel;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getButtonData(): array {
        return [
            'label' => __('Download'),
            'on_click' => sprintf("location.href = '%s';", $this->getDownloadUrl()),            
            'sort_order' => 20
        ];
    }

    private function getDownloadUrl() {
        return $this->urlInterface->getUrl('logfiles/display/download',
                        [Configs::FILE_NAME_REQUEST_FIELD => $this->logFileModel->getFileNameFromUrl()]);
    } 
}
