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
     * Returns the number of lines of the file 
     * 
     * @return int
     */
    public function getFileTotalLinesQty(): int {
        $file = new \SplFileObject($this->fileStatCollector->getFilePath());
        $file->seek($file->getSize());
        $totalLines = $file->key();
        return $totalLines;
    }
    
    /**
     * If the configured default lines to read number exceeds the total lines number
     * the default value on the level of script will be set
     * 
     * @return int
     */
    private function getCorrectDefaultLinesToRead(): int{
        return $this->configs->getDefaultLinesToRead() <= $this->getFileTotalLinesQty()
                ? (int)$this->configs->getDefaultLinesToRead()
                : Configs::DEFAULT_LINES_QTY;
    }
    
    /**
     * Returns the number of file lines to read
     * 
     * @return int
     */
    public function linesToRead() : int {        
        $linesToRead = $this->getCorrectDefaultLinesToRead();
        $enteredLinesToRead = (int)$this->request->getParam(Configs::LINES_QTY_REQUEST_FIELD);
        $correctQty = ($enteredLinesToRead <= $this->getFileTotalLinesQty()) && $enteredLinesToRead > 0;
        if ($correctQty) {
            $linesToRead = $enteredLinesToRead;
        }        
        return $linesToRead;
    }
    
    /**
     * Returns the line number to start file reading 
     * 
     * @return int
     */
    public function lineToStartReading() : int{
        return $this->linesToRead() < $this->getFileTotalLinesQty()
                ? $this->getFileTotalLinesQty()- $this->linesToRead()
                : $this->getFileTotalLinesQty() - Configs::DEFAULT_LINES_QTY;            
    }
    
    /**
     * Adds a prefix to the line showing the line number depending on a user configuration
     * 
     * @param int $lineNumber
     * @return string|null
     */
    private function geFormattedtLineNumber(int $lineNumber, string $lineNumberTag = 'b')  {
        return sprintf("%s $lineNumber %s", "<$lineNumberTag>", "</$lineNumberTag>");         
        
    }
    
    /**
     * 
     * @return type
     */
    private function geLineNumberSeparator()  {
        return str_repeat('&nbsp;', (int)$this->configs->getLineNumberSeparator()); 
    }
    
    /**
     * 
     * @return type
    */
    private function geLineSeparator()  {
        $separators = ['<br>','<br><br>','<hr>'];
        return $separators[$this->configs->getLineSeparator()]; 
    }
    
    /**
     * Returns formatted line
     * 
     * @param int $lineNumber
     * @param string $lineNumLineTextSeparator
     * @param string $lineText     
     * @return string
     */
    public function getFormattedLine(int $lineNumber, string $lineText): string {        
        $formattedLine = $lineText . $this->geLineSeparator();        
        if($this->configs->getAddLineNumber()){            
            $lineNumberTag = $this->configs->getLineNumberFormat(); 
            $lineNumberSeparator = $this->geLineNumberSeparator(); 
            $formattedLine = $this->geFormattedtLineNumber($lineNumber, $lineNumberTag) . $lineNumberSeparator . $formattedLine;
        }
        return $formattedLine;
    }    
}
