<?php

declare(strict_types=1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Training\LogReader\Model\LogFile;

class Download implements HttpGetActionInterface {

    // Restrict the access to the controller
    const ADMIN_RESOURCE = 'Training_LogReader::download';

    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;
    protected $resultFactory;
    private $urlInterface;
    private $driverFile;
    private $fileFactory;
    private LogFile $logFileModel;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    public function __construct(
            PageFactory $pageFactory, 
            ResultFactory $resultFactory,
            UrlInterface $urlInterface,
            File $driverFile,
            FileFactory $fileFactory,
            LogFile $logFileModel,
            RequestInterface $request
    ) {
        $this->pageFactory = $pageFactory;
        $this->resultFactory = $resultFactory;
        $this->urlInterface = $urlInterface;
        $this->driverFile = $driverFile;
        $this->fileFactory = $fileFactory;
        $this->request = $request;
        $this->logFileModel = $logFileModel;        
    }

    public function execute() {

        $this->downloadFile($this->logFileModel->getFilePath());       
    }

    // Download log file
    public function downloadFile(string $filePath) {
        try {
            $downloadedFileName = $this->logFileModel->getFileNameFromUrl($filePath) . '_' . date('Y/m/d H:i:s');
            $fileContent = $this->driverFile->fileGetContents($filePath);
            $this->fileFactory->create($downloadedFileName, $fileContent, DirectoryList::ROOT, 'application/octet-stream');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                    __('An error %1 occurred while downloading the file %2.', '"' . $e->getMessage() . '"', $filePath)
            );
        }
    }

}
