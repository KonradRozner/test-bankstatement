<?php

namespace Vindication\Payment\DirectDebit\IO;


class WriterFile
{
    private $filePath = null;

    /**
     * @return string
     */
    public function getPath() {
        if( null === $this->filePath ) {
            $this->filePath = tempnam(sys_get_temp_dir(), uniqid());
        }
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getContent() {
        return file_get_contents($this->getPath());
    }

    /**
     * @param $content
     * @return $this
     */
    public function putContent($content) {
        file_put_contents($this->getPath(), $content);
        return $this;
    }
}