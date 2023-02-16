<?php

declare(strict_types=1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Training\LogReader\Model\LogFile;
use Training\LogReader\Model\Lines;
use Training\LogReader\Configs;

class View implements HttpPostActionInterface, HttpGetActionInterface {

    // Restrict the access to the controller
    const ADMIN_RESOURCE = 'Training_LogReader::view';

    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * 
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

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
    
    /**
     * 
     * @var LogFile
     */
    private Lines $lines;

    public function __construct(
            PageFactory $pageFactory,
            ResultFactory $resultFactory,
            RequestInterface $request,
            ManagerInterface $messageManager,
            LogFile $logFileModel,
            Lines $lines 
    ) {
        $this->pageFactory = $pageFactory;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->logFileModel = $logFileModel;
        $this->lines = $lines;   
    }

    public function execute() {
        
        if (!$this->logFileModel->isLogFileValid()) {            
            $this->validateLogFile();
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        } 
        
        $this->validatelinesQtyInput();
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend(__($this->logFileModel->getFileNameFromUrl()));
        return $page;
    }

    /**
     * Validates user input of lines to read number
     *  
     */    
    private function validatelinesQtyInput() {       
        $lastLinesQtyFromUrl = (int)$this->request->getParam(Configs::LINES_QTY_REQUEST_FIELD);
        if ($this->request->getPostValue()) {
            if ($lastLinesQtyFromUrl > $this->lines->getFileTotalLinesQty()) {
                $this->messageManager->addErrorMessage(__('Entered qty exceeds the total lines number of the file'));
            }
            if ($lastLinesQtyFromUrl <= 0) {
                $this->messageManager->addErrorMessage(__('Entered lines qty should a be positive integer number'));
            }
        }
    }

    /**
     * 
     * Validates log file before show its content 
     */
    private function validateLogFile() {
        if (!$this->logFileModel->isLogFileExists()) {
            $this->messageManager->addErrorMessage(__('File %1 can not be found in %2 ',
                            $this->logFileModel->getFileNameFromUrl(),
                            Configs::LOG_DIR_PATH)
            );            
        }

        elseif (!$this->logFileModel->isLogFileReadable()) {
            $this->messageManager->addErrorMessage(__('File %1 is not a redable ',
                            $this->logFileModel->getFileNameFromUrl())
            );            
        }   
        
        elseif (!$this->logFileModel->isLogFileText()) {
            $this->messageManager->addErrorMessage(__('File %1 is not a text file ',
                            $this->logFileModel->getFileNameFromUrl())
            );           
        } 
        
    }

}
