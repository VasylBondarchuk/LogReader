<?php

declare(strict_types = 1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Backend\App\Action\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Training\LogReader\Configs;

class Download extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    private $urlInterface;
    private $driverFile;
    private $fileFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        UrlInterface $urlInterface,
        File $driverFile,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->urlInterface = $urlInterface;
        $this->driverFile = $driverFile;
        $this->fileFactory = $fileFactory;
    }

    public function getFilePathFromUrl()
    {
        $urlArray = explode("/", $this->urlInterface->getCurrentUrl());
        $fileName = $urlArray[count($urlArray)-1];
        return (Configs::LOG_DIR_PATH) . DIRECTORY_SEPARATOR . $fileName;
    }

    public function getFileName(string $filePath):string
    {
        $filePathArray = explode(DIRECTORY_SEPARATOR, $filePath);
        return $filePathArray[count($filePathArray)-1];
    }

    public function downloadFile($filePath)
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

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Training_LogReader::menu');
    }
}
