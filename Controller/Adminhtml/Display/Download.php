<?php

declare(strict_types=1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Training\LogReader\Model\LogFile;

class Download implements HttpGetActionInterface {

    // Restrict the access to the controller
    const ADMIN_RESOURCE = 'Training_LogReader::download';

    /**
     * 
     * @var ResultFactory
     */       
    private ResultFactory $resultFactory;
    /**
     * 
     * @var LogFile
     */
    private LogFile $logFileModel;

    /**
     * 
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    public function __construct(            
            ResultFactory $resultFactory,
            ManagerInterface $messageManager,            
            LogFile $logFileModel          
            
    ) {        
        $this->resultFactory = $resultFactory; 
        $this->messageManager = $messageManager;      
        $this->logFileModel = $logFileModel;
    }

    public function execute() {
        try {
            $this->logFileModel->downloadFile();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                    __('An error %1 occurred while downloading the file %2.', '"' . $e->getMessage() . '"', $this->logFileModel->getFilePath())
            );
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
    }
}
