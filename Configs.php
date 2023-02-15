<?php

declare(strict_types=1);

namespace Training\LogReader;

class Configs
{
    const LOG_DIR_PATH = BP. DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR .'log';
    const DEFAULT_LINES_QTY = 10;
    const FILE_NAME_REQUEST_FIELD = 'file_name';
    const LINES_QTY_REQUEST_FIELD = 'lines_qty';
    const DEFAULT_LINES_QTY_CONFIGS_PATH = 'logreader_configuration/logreader_configuration_general/default_last_lines_qty';
    const ADD_LINES_NUMBER_CONFIGS_PATH = 'logreader_configuration/logreader_configuration_general/add_lines_numbers_to_output';
}
