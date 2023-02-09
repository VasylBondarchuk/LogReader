<?php

declare(strict_types = 1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Backend\App\Action\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Training\LogReader\Configs;

class Download extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Training_LogReader::download';
    
    protected $resultPageFactory = false;
    private $urlInterface;
    private $driverFile;
    private $fileFactory;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        UrlInterface $urlInterface,
        File $driverFile,
        FileFactory $fileFactory,
        RequestInterface $request    
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->urlInterface = $urlInterface;
        $this->driverFile = $driverFile;
        $this->fileFactory = $fileFactory;
        $this->request = $request;
    }

    public function getFilePathFromUrl()
    {
        $urlArray = explode("/", $this->urlInterface->getCurrentUrl());
        $fileName = $urlArray[count($urlArray)-1];
        return (Configs::LOG_DIR_PATH) . DIRECTORY_SEPARATOR . $fileName;
    }

    private function getFileName(): string
    {        
        $fileName= $this->request->getParam('file_name');        
        return $fileName;
    }

    public function downloadFile(string $filePath)
    {
        $fileName = $this->getFileName($filePath);
        $fileContent = $this->driverFile->fileGetContents($filePath);
        $this->fileFactory->create($fileName, $fileContent, DirectoryList::ROOT, 'application/octet-stream');
    }

    public function execute()
    { 
        $this->downloadFile($this->getFilePathFromUrl());
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }    
}
