<?php

declare(strict_types=1);

namespace Training\LogReader\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\LogReader\Model\LogFile;

class View extends Template {
    
    /**
     * 
     * @var LogFile
     */
    private LogFile $logFileModel;
    
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
            Context $context,            
            LogFile $logFileModel            
    ) {        
        $this->logFileModel = $logFileModel;        
        parent::__construct($context);
    }    
   

    /**
     * Display log file content in web-browser
     * 
     * @return string
     */
    public function displayFileContentHtml() : string    {
        
        $firstDisplayedLineNumber =  $this->logFileModel->getFileTotalLinesQty() - $this->logFileModel->getLastLinesQty() + 1;       
        $outputHtml = '' ; 
        foreach($this->logFileModel->getFileContentArray() as $lineIndex => $lineText) {
            $lineNumber = $firstDisplayedLineNumber + $lineIndex;                        
            $outputHtml.= $this->logFileModel->getFormattedLine($lineNumber, $lineText);                    
        }
        
        return $outputHtml; 
    }
}
