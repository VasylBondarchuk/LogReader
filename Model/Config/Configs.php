<?php

declare(strict_types=1);

namespace Training\LogReader\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Configs {

    // Path to directory containing log files
    const LOG_DIR_PATH = BP . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'log';
    
    // Deafault customizable configs values
    const DEFAULT_LINES_QTY = 10;
    const DEFAULT_ADD_LINE_NUMBER = false;
    const DEFAULT_TIME_FORMAT = 'F j, Y, g:i a';
    const DEFAULT_FILE_SIZE_FORMAT = 1000;
    
    // Request fields names
    const FILE_NAME_REQUEST_FIELD = 'file_name';
    const LINES_QTY_REQUEST_FIELD = 'lines_qty';
    
    // Configs pathes
    const DEFAULT_LINES_QTY_CONFIGS_PATH = 'logreader_configuration/logreader_configuration_general/default_last_lines_qty';
    const LINE_SEPARATOR_PATH = 'logreader_configuration/logreader_configuration_general/line_separator';
    const ADD_LINES_NUMBER_CONFIGS_PATH = 'logreader_configuration/logreader_configuration_general/add_lines_numbers_to_output';    
    const LINE_NUMBER_FORMAT_PATH = 'logreader_configuration/logreader_configuration_general/line_number_format';
    const LINE_NUMBER_SEPARATOR_PATH = 'logreader_configuration/logreader_configuration_general/line_number_separator';
    const MODIFICATION_DATE_FORMAT_PATH = 'logreader_configuration/logreader_configuration_general/modification_date_format';
    const FILE_SIZE_FORMAT_PATH = 'logreader_configuration/logreader_configuration_general/file_size_format';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    private function getConfigParamValue(string $configPath){
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * 
     * @return int
     */
    public function getDefaultLinesToRead(): int {
        return $this->isLastLinesQtyValid($this->getConfigParamValue(self::DEFAULT_LINES_QTY_CONFIGS_PATH))
                ? (int) $this->getConfigParamValue(self::DEFAULT_LINES_QTY_CONFIGS_PATH)
                : self::DEFAULT_LINES_QTY;
    }

    /**
     * 
     * @return int
     */
    public function getAddLineNumber(): bool {
        return $this->isLastLinesQtyValid(
                        $this->scopeConfig->getValue(
                                Configs::DEFAULT_LINES_QTY_CONFIGS_PATH,
                                ScopeInterface::SCOPE_STORE)
                ) ? (bool) $this->scopeConfig->getValue(self::ADD_LINES_NUMBER_CONFIGS_PATH, ScopeInterface::SCOPE_STORE) : self::DEFAULT_ADD_LINE_NUMBER;
    }
    
    /**
     * 
     * @return int
    */
    public function getLineNumberFormat(): string {
        return $this->getConfigParamValue(self::LINE_NUMBER_FORMAT_PATH);
    }
    
    /**
     * 
     * @return string
     */
    public function getLineNumberSeparator(): string {
        return $this->getConfigParamValue(self::LINE_NUMBER_SEPARATOR_PATH);
    }
    
    /**
     * 
     * @return string
    */
    public function getLineSeparator(): int {        
        return (int)$this->getConfigParamValue(self::LINE_SEPARATOR_PATH);
    }

    /**
     * 
     * @return int
     */
    public function getTimeFormat(): string {
        return $this->isLastLinesQtyValid(
                        $this->scopeConfig->getValue(
                                Configs::DEFAULT_LINES_QTY_CONFIGS_PATH,
                                ScopeInterface::SCOPE_STORE)
                )
                ? (string) $this->scopeConfig->getValue(self::MODIFICATION_DATE_FORMAT_PATH, ScopeInterface::SCOPE_STORE)
                : self::DEFAULT_TIME_FORMAT;
    }
    
    /**
     * 
     * @return int
    */
    public function getFileSizeFormat(): int {
        return  $this->scopeConfig->getValue(self::FILE_SIZE_FORMAT_PATH, ScopeInterface::SCOPE_STORE)
                ? (int)$this->scopeConfig->getValue(self::FILE_SIZE_FORMAT_PATH, ScopeInterface::SCOPE_STORE)
                : self::DEFAULT_FILE_SIZE_FORMAT;
    }

    /**
     * 
     * @return int
     */
    public function isLastLinesQtyValid($linesQty): bool {
        return !empty($linesQty) && (int) $linesQty > 0;
    }

}
