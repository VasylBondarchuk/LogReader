<?php

declare(strict_types=1);

namespace Training\LogReader\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Training\LogReader\Model\Config\Configs;
use Magento\Framework\App\RequestInterface;
use Training\LogReader\Model\LogFile;

/**
 * Description of LogFile
 *
 * @author vasyl
 */
class Lines {

    /**
     * 
     * @var LogFile
     */
    private LogFile $logFileModel;
    /**
     * 
     * @var RequestInterface
     */
    private RequestInterface $request;    

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
            LogFile $logFileModel,
            RequestInterface $request,            
            ScopeConfigInterface $scopeConfig
    ) {
        $this->logFileModel = $logFileModel;
        $this->request = $request;        
        $this->scopeConfig = $scopeConfig;
    }
    
    /**
     * 
     * @return int
     */
    public function getFileTotalLinesQty(): int {
        $file = new \SplFileObject($this->logFileModel->getFilePath());
        $file->seek($file->getSize());
        $totalLines = $file->key() + 1;
        return $totalLines;
    }
    
    public function linesToRead() : int {
        return $this->getLastLinesQty() < $this->getFileTotalLinesQty()
                ? $this->getLastLinesQty()
                : $this->getFileTotalLinesQty() - 1;
    }
    
     public function lineToStartReading() : int{
        return $this->getLastLinesQty() < $this->getFileTotalLinesQty()
                ? $this->getFileTotalLinesQty()- $this->linesToRead() - 1
                : 0;            
    }
    
    /**
     * Adds a prefix to the line showing the line number depending on a user configuration
     * 
     * @param int $lineNumber
     * @return string|null
     */
    private function getLinePrefix(int $lineNumber, string $htmlTag)  {        
        return  __("<%1> Line# %2 </%1>", $htmlTag, $lineNumber);         
        
    }
    
    /**
     * 
     * @param int $lineNumber
     * @param string $lineText
     * @param string $htmlTag
     * @param string $lineSeparator
     * @return string
     */
    public function getOutputLineText(int $lineNumber, string $lineText, string $htmlTag = '', string $lineSeparator = '<br>'): string {
        $outputLineText = $lineText . $lineSeparator;
        if($this->addLineNumber()){
            $outputLineFormat = "%s $lineText %s";
            $outputLineText = sprintf($outputLineFormat, $this->getLinePrefix($lineNumber, $htmlTag), $lineSeparator);
        }
        return $outputLineText;
    } 

    /**
     * Defines whether add line number to output depending on a user configuration 
     * 
     * @return bool
     */
    private function addLineNumber(): bool {
        return (bool) $this->scopeConfig->getValue(
                        Configs::ADD_LINES_NUMBER_CONFIGS_PATH,
                        ScopeInterface::SCOPE_STORE);
    }   
    

    /**
     * 
     * @return int
     */
    public function getLastLinesQty(): int {
        $lastLinesQty = $this->getValidDefaultLastLinesQty();
        $lastLinesQtyFromUrl = (int) $this->request->getParam(Configs::LINES_QTY_REQUEST_FIELD);
        $correctQty = ($lastLinesQtyFromUrl < $this->getFileTotalLinesQty()) && $lastLinesQtyFromUrl > 0;
        if ($correctQty) {
            $lastLinesQty = $lastLinesQtyFromUrl;
        }
        return $lastLinesQty;
    }

    /**
     * 
     * @return int
     */
    public function getValidDefaultLastLinesQty(): int {
        return $this->isLastLinesQtyValid($this->scopeConfig->getValue(Configs::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE))
                ? (int) $this->scopeConfig->getValue(Configs::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE)
                : Configs::DEFAULT_LINES_QTY;
    }

    /**
     * 
     * @return int
     */
    public function isLastLinesQtyValid(string $linesQty): bool {
        return !empty($linesQty) && (int) $linesQty > 0;
    }
    
}
