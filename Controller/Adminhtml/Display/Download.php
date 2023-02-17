<?php

declare(strict_types=1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Training\LogReader\Model\FileStatisticsCollector;
use Training\LogReader\Model\FileManager;

class Download extends Action implements HttpGetActionInterface {

    // Restrict the access to the controller
    const ADMIN_RESOURCE = 'Training_LogReader::download';

    /**
     * 
     * @var ResultFactory
     */       
    protected $resultFactory;
    
    /**
     * 
     * @var FileStatisticsCollector
     */
    private FileStatisticsCollector $fileStatCollector;
    
    /**
     * 
     * @var FileManager
     */
    private FileManager $fileManager; 

    /**
     * 
     * @var ManagerInterface
     */
    protected $messageManager;

    public function __construct(            
            Context $context,
            ResultFactory $resultFactory,
            ManagerInterface $messageManager,            
            FileStatisticsCollector $fileStatCollector,
            FileManager $fileManager
            
    ) {        
        $this->resultFactory = $resultFactory; 
        $this->messageManager = $messageManager;      
        $this->fileStatCollector = $fileStatCollector;
        $this->fileManager = $fileManager;
        parent::__construct($context); 
    }

    public function execute() {
        try {
            $this->fileManager->downloadFile();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                    __('An error %1 occurred while downloading the file %2.', '"' . $e->getMessage() . '"', $this->fileStatCollector->getFilePath())
            );
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
    }
}
