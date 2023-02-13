<?php

declare(strict_types=1);

namespace Training\LogReader\Block\Adminhtml;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\LogReader\Configs;
use Magento\Framework\Escaper;
use \Magento\Framework\App\Request\Http;
use Training\LogReader\Model\LogFile;

class View extends Template {

    private const DEFAULT_LINES_QTY_CONFIGS_PATH =
        'logreader_configuration/logreader_configuration_general/default_last_lines_qty';
    
    private const ADD_LINES_NUMBER_CONFIGS_PATH =
        'logreader_configuration/logreader_configuration_general/add_lines_numbers_to_output';
    /**
     * 
     * @var File
     */
    private File $file;
    
    /**
     * 
     * @var UrlInterface
     */
    private UrlInterface $urlInterface;
    
    /**
     * 
     * @var Escaper
     */
    private Escaper $escaper;
    
    /**
     * 
     * @var Http
     */
    private Http $request;
    
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
            File $file,
            UrlInterface $urlInterface,
            Escaper $escaper,
            Http $request,
            LogFile $logFileModel,
            ScopeConfigInterface $scopeConfig
    ) {
        $this->file = $file;
        $this->urlInterface = $urlInterface;
        $this->escaper = $escaper;
        $this->request = $request;
        $this->logFileModel = $logFileModel;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }    

    
    public function getLastLinesQtyFromUrl(): int {
        return (int) $this->request->getParam('lines_qty');
    }
    
    public function getLastLinesQty(): int {
        $lastLinesQty = $this->getDefaultLastLinesQty();        
        $lastLinesQtyFromUrl = $this->getLastLinesQtyFromUrl();
        $correctQty = ($lastLinesQtyFromUrl < $this->logFileModel->getFileTotalLinesQty()) && $lastLinesQtyFromUrl > 0;
        if($correctQty){            
              $lastLinesQty = $lastLinesQtyFromUrl;
        }
        return $lastLinesQty;
    }   

    public function displayFileContent() : string    {          
        
        $firstDisplayedRowNumber =  $this->logFileModel->getFileTotalLinesQty() - $this->getLastLinesQty() + 1;
        $outputHtml = '' ; 
        foreach($this->getFileContentArray() as $index => $lineText) {
            $lineNumber = $firstDisplayedRowNumber + $index;                        
            $outputHtml.= $this->getFormattedLine($lineNumber, $lineText);                    
        }        
        return $outputHtml; 
    }

    public function getDefaultLastLinesQty(): int {
        return !$this->scopeConfig->getValue(self::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE)
                ? Configs::DEFAULT_LINES_QTY 
                : (int)$this->scopeConfig->getValue(self::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE);                               
               
    } 
    
    private function getFormattedLine(int $lineNumber, string $lineText) : string
    {
        $linePrefix = $this->addLinesNumber()
                ? '<b>Line #'. $lineNumber . '</b>'
                : '';
        $outPutFormat = "%s $lineText %s";
        return sprintf($outPutFormat, $linePrefix,'<br/>');
    }
    
    private function getFileContentArray() : array
    {        
        return array_slice($this->logFileModel->getFileContent(), -$this->getLastLinesQty()); 
    }
    
    private function addLinesNumber(): bool {
        return (bool)$this->scopeConfig->getValue(self::ADD_LINES_NUMBER_CONFIGS_PATH, ScopeInterface::SCOPE_STORE);                               
               
    }
    
    public function getTotalLinesQty  (): int {
        return count($this->logFileModel->getFileContent());
    }

  
    
}
