<?php

declare(strict_types=1);

namespace Training\LogReader\Model;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;
use Training\LogReader\Model\FileStatisticsCollector;

/**
 * Collects file statistics
 *
 * @author vasyl
 */
class FileValidator {

    /**
     * 
     * @var File
     */
    private FileStatisticsCollector $fileStatCollector;

    /**
     * 
     * @var File
     */
    private File $file;

    /**
     * 
     * @var FileFactory
     */
    private FileFactory $fileFactory;

    public function __construct(
            FileStatisticsCollector $fileStatCollector,
            File $file,
            FileFactory $fileFactory
    ) {
        $this->fileStatCollector = $fileStatCollector;
        $this->file = $file;
        $this->fileFactory = $fileFactory;
    }

    /**
     * 
     * @return bool
     */
    public function isFileExists(): bool {
        return $this->file->isExists($this->fileStatCollector->getFilePath());
    }

    /**
     * 
     * @return bool
     */
    public function isFileText(): bool {
        return $this->isFileExists()
                ? explode('/', mime_content_type($this->fileStatCollector->getFilePath()))[0] === 'text'
                : false;
    }

    /**
     * 
     * @return bool
     */
    public function isFileReadable(): bool {
        return $this->isFileExists()
                ? $this->file->isReadable($this->fileStatCollector->getFilePath())
                : false;
    }

    /**
     * 
     * @return bool
     */
    public function isFileValid(): bool {
        return $this->isFileExists() &&
                $this->isFileReadable() &&
                $this->isFileText();
    }

}
