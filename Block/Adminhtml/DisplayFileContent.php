<?php

declare(strict_types=1);

namespace Training\LogReader\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\LogReader\Model\FileStatisticsCollector;
use Training\LogReader\Model\FileLineFormatter;
use Training\LogReader\Model\FileManager;

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

    /**
     * 
     * @var FileManager
     */
    private FileManager $fileManager;     
    

    public function __construct(
            Context $context,
            FileStatisticsCollector $fileStatCollector,
            FileLineFormatter $fileLineFormatter,
            FileManager $fileManager 
    ) {
        $this->fileStatCollector = $fileStatCollector;
        $this->fileLineFormatter = $fileLineFormatter;
        $this->fileManager = $fileManager;        
        parent::__construct($context);
    }

    /**
     * Display log file content in web-browser
     * 
     * @return string
     */
    public function displayFileContentHtml(): string {
              
        $linesCollection = $this->fileManager->readFile(
                $this->fileLineFormatter->linesToRead(),
                $this->fileLineFormatter->lineToStartReading()
                );
        $outputHtml = ''; 
        foreach ($linesCollection as $lineIndex => $lineText) {
            $outputHtml.= $this->fileLineFormatter->getOutputLineText($lineIndex + 1, $lineText, 'b', '<br>');
        }    
        return $outputHtml;
    }
}
