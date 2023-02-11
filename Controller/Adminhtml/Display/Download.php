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
use Training\LogReader\Model\LogFile;

class Download extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Training_LogReader::download';
    
    protected $resultPageFactory = false;
    private $urlInterface;
    private $driverFile;
    private $fileFactory;
    private LogFile $logFileModel;
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
        LogFile $logFileModel,
        RequestInterface $request    
    ) {        
        $this->resultPageFactory = $resultPageFactory;
        $this->urlInterface = $urlInterface;
        $this->driverFile = $driverFile;
        $this->fileFactory = $fileFactory;
        $this->request = $request;
        $this->logFileModel = $logFileModel;
        parent::__construct($context);
    } 

    public function execute()
    { 
        $this->downloadFile($this->logFileModel->getFilePath());        
    } 
    
    public function downloadFile(string $filePath)
    {
        $fileName = $this->logFileModel->getFileNameFromUrl($filePath) . '_' . date('Y/m/d H:i:s');
        $fileContent = $this->driverFile->fileGetContents($filePath);
        $this->fileFactory->create($fileName, $fileContent, DirectoryList::ROOT, 'application/octet-stream');
    }
}
