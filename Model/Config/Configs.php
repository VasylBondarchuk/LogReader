<?php

declare(strict_types=1);

namespace Training\LogReader\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Configs {

    const LOG_DIR_PATH = BP . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'log';
    // Deafault configs values
    const DEFAULT_LINES_QTY = 10;
    const DEFAULT_ADD_LINE_NUMBER = false;
    const DEFAULT_TIME_FORMAT = 'F j, Y, g:i a';
    const DEFAULT_LINES_QTY_CONFIGS_PATH = 'logreader_configuration/logreader_configuration_general/default_last_lines_qty';
    const ADD_LINES_NUMBER_CONFIGS_PATH = 'logreader_configuration/logreader_configuration_general/add_lines_numbers_to_output';
    const GET_MODIFICATION_DATE_FORMAT = 'logreader_configuration/logreader_configuration_general/modification_date_format';
    const FILE_NAME_REQUEST_FIELD = 'file_name';
    const LINES_QTY_REQUEST_FIELD = 'lines_qty';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * 
     * @return int
     */
    public function getDefaultLinesToRead(): int {
        return $this->isLastLinesQtyValid($this->scopeConfig->getValue(Configs::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE))
                ? (int) $this->scopeConfig->getValue(self::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE)
                : self::DEFAULT_LINES_QTY;
    }

    /**
     * 
     * @return int
     */
    public function getAddLineNumber(): bool {
        return $this->isLastLinesQtyValid($this->scopeConfig->getValue(Configs::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE))
                ? (bool) $this->scopeConfig->getValue(self::ADD_LINES_NUMBER_CONFIGS_PATH, ScopeInterface::SCOPE_STORE)
                : self::DEFAULT_ADD_LINE_NUMBER;
    }

    /**
     * 
     * @return int
     */
    public function getTimeFormat(): string {
        return $this->isLastLinesQtyValid($this->scopeConfig->getValue(Configs::DEFAULT_LINES_QTY_CONFIGS_PATH, ScopeInterface::SCOPE_STORE))
                ? (string) $this->scopeConfig->getValue(self::GET_MODIFICATION_DATE_FORMAT, ScopeInterface::SCOPE_STORE)
                : self::DEFAULT_TIME_FORMAT;
    }

    /**
     * 
     * @return int
     */
    public function isLastLinesQtyValid(string $linesQty): bool {
        return !empty($linesQty) && (int) $linesQty > 0;
    }

}
