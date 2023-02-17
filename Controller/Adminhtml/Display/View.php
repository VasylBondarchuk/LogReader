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

    /**
     * 
     * @var Configs
     */
    private Configs $configs;

    public function __construct(
            PageFactory $pageFactory,
            ResultFactory $resultFactory,
            RequestInterface $request,
            ManagerInterface $messageManager,
            FileStatisticsCollector $fileStatCollector,
            FileValidator $fileValidator,
            FileLineFormatter $fileLineFormatter,
            Configs $configs
    ) {
        $this->pageFactory = $pageFactory;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->fileStatCollector = $fileStatCollector;
        $this->fileValidator = $fileValidator;
        $this->fileLineFormatter = $fileLineFormatter;
        $this->configs = $configs;
    }

    public function execute() {

        // If the file is not valid redirect to the list page
        if (!$this->fileValidator->isFileValid()) {
            $this->validateFile();
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        $this->validateLinesToRead();
        
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend(__($this->fileStatCollector->getFileNameFromUrl()));
        return $page;
    }

    /**
     * Validates user input of lines to read number
     *  
     */
    private function validateLinesToRead() {
        $lastLinesQtyFromUrl = (int) $this->request->getParam(Configs::LINES_QTY_REQUEST_FIELD);
        
        if ($this->request->getPostValue()) {
            if ($lastLinesQtyFromUrl > $this->fileLineFormatter->getFileTotalLinesQty()) {
                $this->messageManager->addErrorMessage(__('Entered lines to read number exceeds the total fileLineFormatter number of the file'));
            }
            if ($lastLinesQtyFromUrl <= 0) {
                $this->messageManager->addErrorMessage(__('Entered lines to read number should a be positive integer number'));
            }            
        }
        // warning if default lines to read excceds total number of lines
        elseif ($this->configs->getDefaultLinesToRead() > $this->fileLineFormatter->getFileTotalLinesQty()) {
                $this->messageManager->addWarningMessage(__('Configured default lines to read exceeds total lines number. Lines to read number was set to %1',
                        Configs::DEFAULT_LINES_QTY));
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
        } elseif (!$this->fileValidator->isFileReadable()) {
            $this->messageManager->addErrorMessage(__('File %1 is not a redable ',
                            $this->fileStatCollector->getFileNameFromUrl())
            );
        } elseif (!$this->fileValidator->isFileText()) {
            $this->messageManager->addErrorMessage(__('File %1 is not a text file ',
                            $this->fileStatCollector->getFileNameFromUrl())
            );
        }
    }

}
