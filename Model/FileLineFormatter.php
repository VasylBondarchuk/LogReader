<?php

declare(strict_types=1);

namespace Training\LogReader\Model;

use Training\LogReader\Model\Config\Configs;
use Magento\Framework\App\RequestInterface;
use Training\LogReader\Model\FileStatisticsCollector;

/**
 * Formats lines of the file to display
 * 
 */
class FileLineFormatter {

    /**
     * 
     * @var File
     */
    private FileStatisticsCollector $fileStatCollector;
    /**
     * 
     * @var RequestInterface
     */
    private RequestInterface $request;
    
    /**
     * 
     * @var Configs
     */
    private Configs $configs;

    public function __construct(
            FileStatisticsCollector $fileStatCollector,
            RequestInterface $request,
            Configs $configs
    ) {
        $this->fileStatCollector = $fileStatCollector;
        $this->request = $request;
        $this->configs = $configs;
    }
    
    /**
     * 
     * @return int
     */
    public function getFileTotalLinesQty(): int {
        $file = new \SplFileObject($this->fileStatCollector->getFilePath());
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
        if($this->configs->getAddLineNumber()){
            $outputLineFormat = "%s $lineText %s";
            $outputLineText = sprintf(
                    $outputLineFormat,
                    $this->getLinePrefix($lineNumber, $htmlTag),
                    $lineSeparator);
        }
        return $outputLineText;
    }

    /**
     * 
     * @return int
     */
    public function getLastLinesQty(): int {
        $lastLinesQty = $this->configs->getDefaultLinesToRead();
        $lastLinesQtyFromUrl = (int) $this->request->getParam(Configs::LINES_QTY_REQUEST_FIELD);
        $correctQty = ($lastLinesQtyFromUrl < $this->getFileTotalLinesQty()) && $lastLinesQtyFromUrl > 0;
        if ($correctQty) {
            $lastLinesQty = $lastLinesQtyFromUrl;
        }
        return $lastLinesQty;
    }
    
}
