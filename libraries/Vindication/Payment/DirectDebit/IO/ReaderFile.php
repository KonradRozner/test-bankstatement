<?php

namespace Vindication\Payment\DirectDebit\IO;

use Vindication\Application\Utils\Encoding;

class ReaderFile
{
    private $filePath = null;

    /**
     * @param $filePath
     */
    public function __construct($filePath){
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->filePath;
    }

    private $content = null;

    /**
     * @return string
     */
    public function getContent()
    {
        if( null === $this->content ) {
            $this->content = Encoding::toUTF8( file_get_contents($this->getPath()) );
        }
        return $this->content;
    }

}