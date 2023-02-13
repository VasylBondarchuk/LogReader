<?php

declare(strict_types=1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Training\LogReader\Model\LogFile;

class View implements HttpPostActionInterface, HttpGetActionInterface {

    // Restrict the access to the controller
    const ADMIN_RESOURCE = 'Training_LogReader::view';

    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * 
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * 
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * 
     * @var LogFile
     */
    private LogFile $logFileModel;

    public function __construct(
            PageFactory $pageFactory,
            RequestInterface $request,
            ManagerInterface $messageManager,
            LogFile $logFileModel
    ) {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->logFileModel = $logFileModel;
    }

    public function execute() {

        $this->validatelinesQtyInput();
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend(__($this->logFileModel->getFileNameFromUrl()));
        return $page;
    }

    private function linesQtyInputValidation() {
        if ($this->request->getPostValue()) {
            if ($this->logFileModel->getLastLinesQtyFromUrl() > $this->logFileModel->getFileTotalLinesQty()) {
                $this->messageManager->addErrorMessage(__('Entered qty exceeds the total lines number of the file'));
            }
            if ((int) $this->logFileModel->getLastLinesQtyFromUrl() <= 0) {
                $this->messageManager->addErrorMessage(__('Entered qty should be positive integer number'));
            }
        }
    }
    
     /**
     * @param $post
     * @return void
     * @throws LocalizedException
     */
    private function validatelinesQtyInput()
    {  
        if ($this->request->getPostValue()) {
            if ($this->logFileModel->getLastLinesQtyFromUrl() > $this->logFileModel->getFileTotalLinesQty()) {
                $this->messageManager->addErrorMessage(__('Entered qty exceeds the total lines number of the file'));                
            }
            if ((int) $this->logFileModel->getLastLinesQtyFromUrl() <= 0) {
                $this->messageManager->addErrorMessage(__('Entered qty should be positive integer number'));
            }
        }
    }
}
