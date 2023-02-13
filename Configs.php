<?php

namespace Training\LogReader;

class Configs
{
    const LOG_DIR_PATH = BP. DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR .'log';
    const DEFAULT_LINES_QTY = 10;
    const FILE_NAME_REQUEST_FIELD = 'file_name';
    const LINES_QTY_REQUEST_FIELD = 'lines_qty';
}
