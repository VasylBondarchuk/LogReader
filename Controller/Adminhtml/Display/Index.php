<?php

declare(strict_types = 1);

namespace Training\LogReader\Controller\Adminhtml\Display;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Index extends Action implements HttpGetActionInterface
{
    // Restrict the access to the controller
    const ADMIN_RESOURCE = 'Training_LogReader::view';
    
    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;    

    public function __construct(
            Context $context,
            PageFactory $pageFactory)
    {  
        $this->pageFactory = $pageFactory;
        parent::__construct($context);        
    }

    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend(__('Log Files List'));
        return $page; 
    }    
}
