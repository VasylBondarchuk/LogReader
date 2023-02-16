<?php

declare(strict_types=1);

namespace Training\LogReader\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\LogReader\Model\FileStatisticsCollector;
use Training\LogReader\Model\FileLineFormatter;

class DisplayFileContent extends Template {

    /**
     * 
     * @var FileStatisticsCollector
     */
    private FileStatisticsCollector $fileStatCollector;
    
   /**
    * 
    * @var FileLineFormatter
    */
    private FileLineFormatter $fileLineFormatter;    
    

    public function __construct(
            Context $context,
            FileStatisticsCollector $fileStatCollector,
            FileLineFormatter $fileLineFormatter           
    ) {
        $this->fileStatCollector = $fileStatCollector;
        $this->fileLineFormatter = $fileLineFormatter;        
        parent::__construct($context);
    }

    /**
     * Display log file content in web-browser
     * 
     * @return string
     */
    public function displayFileContentHtml(): string {        

        $linesToRead = $this->fileLineFormatter->linesToRead();
        $lineToStartReading = $this->fileLineFormatter->lineToStartReading();        
        $linesCollection = $this->fileStatCollector->readFileToCollection($lineToStartReading,$linesToRead);
        $outputHtml = ''; 
        foreach ($linesCollection as $lineIndex => $lineText) {
            $outputHtml.= $this->fileLineFormatter->getOutputLineText($lineIndex + 1, $lineText, 'b', '<br>');
        }    
        return $outputHtml;
    }
}
