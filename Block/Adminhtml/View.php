<?php

declare(strict_types = 1);

namespace Training\LogReader\Block\Adminhtml;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\LogReader\Configs;
use Magento\Framework\Escaper;
use \Magento\Framework\App\Request\Http;

class View extends Template
{
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

    public function getCurrentPageUrl()
    {
        return $this->urlInterface->getCurrentUrl();
    }

    public function getLastLinesQty(): int
    {
        $urlArray = explode("/", $this->getCurrentPageUrl());
        $lastLinesQty = (int)$urlArray[count($urlArray)-1];
        return (is_numeric($lastLinesQty) && $lastLinesQty > 0) ? $lastLinesQty : Configs::DEFAULT_LINES_QTY;
    }

    public function getFileName(): string
    {
        $urlArray = explode("/", $this->getCurrentPageUrl());
        $fileName= $this->request->getParam('file_name');
        return $fileName;
    }

    public function getFilePath(): string
    {
        return Configs::LOG_DIR_PATH . DIRECTORY_SEPARATOR . $this->getFileName();
    }

    public function getFileContent(): string
    {
        return $this->file->isReadable($this->getFilePath()) ?
            $this->file->fileGetContents($this->getFilePath()) : " ";
    }

    public function displayFileContent(): string
    {
        $fileContentArray = explode("\n", $this->getFileContent());

        $linesQty = count($fileContentArray) - 1;

        for ($i = 0; $i < $linesQty; $i++) {
            $fileContentArray[$i] = "<b> Line # ".($i+1)."</b> : ".$fileContentArray[$i];
        }
        return implode("<br>", array_slice($fileContentArray, -($this->getLastLinesQty() + 1)));
    }

    public function displayGoBackButton(string $path, string $text): string
    {
        $escapedUrl = $this->escaper->escapeUrl($this->getUrl($path));
        return '<button class="primary" onclick="location.href=\''. $escapedUrl .'\'" type="button">'.
        $text . '</button>';
    }
}