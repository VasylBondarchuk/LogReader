<?php

declare(strict_types=1);

namespace Training\LogReader\Block\Adminhtml;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\LogReader\Configs;
use Magento\Framework\Escaper;
use \Magento\Framework\App\Request\Http;

class View extends Template {

    private $file;
    private $urlInterface;
    private $escaper;
    private $request;

    public function __construct(
            Context $context,
            File $file,
            UrlInterface $urlInterface,
            Escaper $escaper,
            Http $request
    ) {
        $this->file = $file;
        $this->urlInterface = $urlInterface;
        $this->escaper = $escaper;
        $this->request = $request;

        parent::__construct($context);
    }

    public function getCurrentPageUrl() {
        return $this->urlInterface->getCurrentUrl();
    }

    public function getLastLinesQty(): int {
        $lastLinesQty = Configs::DEFAULT_LINES_QTY;        
        $lastLinesQtyFromUrl = $this->getLastLinesQtyFromUrl();
        $correctQty = ($lastLinesQtyFromUrl < $this->getTotalLinesQty()) && $lastLinesQtyFromUrl > 0;
        if($correctQty){            
              $lastLinesQty = $lastLinesQtyFromUrl;
        }
        return $lastLinesQty;
    }

    public function getFileName(): string {        
        return $this->request->getParam('file_name');
    }
    
    public function getLastLinesQtyFromUrl(): int {        
        return (int)$this->request->getParam('lines_qty');
    }


    public function getFilePath(): string {
        return Configs::LOG_DIR_PATH . DIRECTORY_SEPARATOR . $this->getFileName();
    }

    public function getTotalLinesQty(): int {
        return count($this->getFileContent());        
    }
    
     public function getFileContent(): array {
        $fileContentArray = [];
        foreach ($this->getFileRows($this->getFilePath()) as $row) {
            $fileContentArray[] = $row;
        }
        return $fileContentArray;
    }

    public function displayFileContent()
    {
        $fileContentArray = $this->getFileContent();
        $size = count($fileContentArray);
        $outputHtml = '' ;        
        for ($i = 0; $i < $this->getLastLinesQty(); $i++) {
             $outputHtml.= '<b> Line # ' . $size  - $this->getLastLinesQty() + $i + 1 . '</b> : '
                     . $fileContentArray[$size  - $this->getLastLinesQty() + $i] .'<br />';
        }        
        return $outputHtml;
        
    }

    private function getFileRows($filename) {
        $file = fopen($filename, 'r');
        while (($line = fgets($file)) !== false) {
            yield $line;
        }        
        fclose($file);
    }
}
