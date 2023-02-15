<?php

declare(strict_types=1);

namespace Training\LogReader\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\LogReader\Model\LogFile;

class DisplayFileContent extends Template {

    /**
     * 
     * @var LogFile
     */
    private LogFile $logFileModel;

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
    public function displayFileContentHtml(): string {        

        $linesToRead = $this->linesToRead();
        $lineToSartReading = $this->lineToSartReading();
        $file = new \LimitIterator(
                    new \SplFileObject($this->logFileModel->getFilePath()),
                    $lineToSartReading,
                    $linesToRead
                );
        
        $outputHtml = ''; 
        foreach ($file as $lineIndex => $lineText) {
            $outputHtml.= $this->logFileModel->getOutputLineText($lineIndex + 1, $lineText, 'b', '<br>');
        }    
        return $outputHtml;
    }    
    
    private function linesToRead() : int {
        return $this->logFileModel->getLastLinesQty() < $this->logFileModel->getFileTotalLinesQty()
                ? $this->logFileModel->getLastLinesQty() - 1
                : $this->logFileModel->getFileTotalLinesQty() - 1;
    }
    
     private function lineToSartReading() : int{
        return $this->logFileModel->getLastLinesQty() < $this->logFileModel->getFileTotalLinesQty()
                ? $this->logFileModel->getFileTotalLinesQty()- $this->linesToRead() - 1
                : 0;
            
    }
}
