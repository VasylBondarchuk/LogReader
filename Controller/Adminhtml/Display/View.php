<?php

declare(strict_types=1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Training\LogReader\Model\LogFile;

class View extends \Magento\Backend\App\Action {

    const ADMIN_RESOURCE = 'Training_LogReader::view';

    protected $resultPageFactory = false;
   
    private RequestInterface $request;
    
    private LogFile $logFileModel;

    public function __construct(
            PageFactory $resultPageFactory,
            RequestInterface $request,
            ManagerInterface $messageManager,
            LogFile $logFileModel,
            Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->logFileModel = $logFileModel;
        parent::__construct($context);
    }

    public function execute() {                
        if ($this->logFileModel->getLastLinesQtyFromUrl() > $this->logFileModel->getFileTotalLinesQty()) {
            $this->messageManager->addErrorMessage(__('Entered qty exceeds the total rows of the file'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__($this->logFileModel->getFileNameFromUrl()));  
        return $resultPage;
    }
}
