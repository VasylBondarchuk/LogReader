<?php

declare(strict_types=1);

namespace Training\LogReader\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\LogReader\Model\LogFile;
use Training\LogReader\Model\Lines;

class DisplayFileContent extends Template {

    /**
     * 
     * @var LogFile
     */
    private LogFile $logFileModel;
    
    /**
     * 
     * @var LogFile
     */
    private Lines $lines;    
    

    public function __construct(
            Context $context,
            LogFile $logFileModel,
            Lines $lines            
    ) {
        $this->logFileModel = $logFileModel;
        $this->lines = $lines;        
        parent::__construct($context);
    }

    /**
     * Display log file content in web-browser
     * 
     * @return string
     */
    public function displayFileContentHtml(): string {        

        $linesToRead = $this->lines->linesToRead();
        $lineToStartReading = $this->lines->lineToStartReading();        
        $linesCollection = $this->logFileModel->readFileToCollection($lineToStartReading,$linesToRead);
        $outputHtml = ''; 
        foreach ($linesCollection as $lineIndex => $lineText) {
            $outputHtml.= $this->lines->getOutputLineText($lineIndex + 1, $lineText, 'b', '<br>');
        }    
        return $outputHtml;
    } 
    
}
