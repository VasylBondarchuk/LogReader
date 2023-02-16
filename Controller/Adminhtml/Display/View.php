<?php

declare(strict_types=1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Training\LogReader\Model\FileStatisticsCollector;
use Training\LogReader\Model\FileValidator;
use Training\LogReader\Model\FileLineFormatter;
use Training\LogReader\Model\Config\Configs;

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
     * @var File
     */
    private FileStatisticsCollector $fileStatCollector;
    
    /**
     * 
     * @var File
     */
    private FileValidator $fileValidator;
    
    
    /**
     * 
     * @var File
     */
    private FileLineFormatter $fileLineFormatter;

    public function __construct(
            PageFactory $pageFactory,
            ResultFactory $resultFactory,
            RequestInterface $request,
            ManagerInterface $messageManager,
            FileStatisticsCollector $fileStatCollector,
            FileValidator $fileValidator,
            FileLineFormatter $fileLineFormatter 
    ) {
        $this->pageFactory = $pageFactory;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->fileStatCollector = $fileStatCollector;
        $this->fileValidator = $fileValidator;
        $this->fileLineFormatter= $fileLineFormatter;   
    }

    public function execute() {
        
        if (!$this->fileValidator->isFileValid()) {            
            $this->validateFile();
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        } 
        
        $this->validatefileLineFormatterQtyInput();
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend(__($this->fileStatCollector->getFileNameFromUrl()));
        return $page;
    }

    /**
     * Validates user input of fileLineFormatter to read number
     *  
     */    
    private function validatefileLineFormatterQtyInput() {       
        $lastLinesQtyFromUrl = (int)$this->request->getParam(Configs::LINES_QTY_REQUEST_FIELD);
        if ($this->request->getPostValue()) {
            if ($lastLinesQtyFromUrl > $this->fileLineFormatter->getFileTotalLinesQty()) {
                $this->messageManager->addErrorMessage(__('Entered qty exceeds the total fileLineFormatter number of the file'));
            }
            if ($lastLinesQtyFromUrl <= 0) {
                $this->messageManager->addErrorMessage(__('Entered fileLineFormatter qty should a be positive integer number'));
            }
        }
    }

    /**
     * 
     * Validates log file before show its content 
     */
    private function validateFile() {
        if (!$this->fileValidator->isFileExists()) {
            $this->messageManager->addErrorMessage(__('File %1 can not be found in %2 ',
                            $this->fileStatCollector->getFileNameFromUrl(),
                            Configs::LOG_DIR_PATH)
            );            
        }

        elseif (!$this->fileValidator->isFileReadable()) {
            $this->messageManager->addErrorMessage(__('File %1 is not a redable ',
                            $this->fileStatCollector->getFileNameFromUrl())
            );            
        }   
        
        elseif (!$this->fileValidator->isFileText()) {
            $this->messageManager->addErrorMessage(__('File %1 is not a text file ',
                            $this->fileStatCollector->getFileNameFromUrl())
            );           
        } 
        
    }

}
