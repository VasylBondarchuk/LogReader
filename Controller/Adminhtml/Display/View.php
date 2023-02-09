<?php
declare(strict_types = 1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;

class View extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Training_LogReader::logfiles_view'; 
    
    protected $resultPageFactory = false;
    
    /**
     * @var RequestInterface
    */
    private RequestInterface $request;

    public function __construct(            
            PageFactory $resultPageFactory,
            RequestInterface $request,
            Context $context
    )
    {        
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__($this->getFileName()));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Training_LogReader::menu');
    }
    
    private function getFileName(): string
    {        
        $fileName= $this->request->getParam('file_name');        
        return $fileName;
    }
}
