<?php
declare(strict_types = 1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

class View extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Training_LogReader::view';
    
    protected $resultPageFactory = false;
    
    /**
     * @var ManagerInterface
     */
    //private ManagerInterface $messageManager;
    
    /**
     * @var RequestInterface
    */
    private RequestInterface $request;

    public function __construct(            
            PageFactory $resultPageFactory,
            RequestInterface $request,
            ManagerInterface $messageManager,
            Context $context
    )
    {        
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }
    public function execute()
    {
        echo $this->getLastLinesQtyFromUrl();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__($this->getFileNameFromUrl));
        if ($this->getLastLinesQtyFromUrl()> 1000) {
            $this->messageManager->addErrorMessage(__('The total rows number is exceeded'));
        }
        return $resultPage;
    }   
    
    private function getFileNameFromUrl(): string
    {        
        $fileName= $this->request->getParam('file_name');        
        return $fileName;
    }
    
    public function getLastLinesQtyFromUrl(): int {        
        return (int)$this->request->getParam('lines_qty');
    }
}
