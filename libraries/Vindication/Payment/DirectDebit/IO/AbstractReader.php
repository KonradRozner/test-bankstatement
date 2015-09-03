<?php

namespace Vindication\Payment\DirectDebit\IO;

use Vindication\Payment\DirectDebit\Exception as DirectDebitException;

abstract class AbstractReader
{
    abstract public function read();

    const TYPE_MNB = 'MNB';
    const TYPE_MND = 'MND';

    /**
     * @return array
     */
    public static function getAvailableReaderTypes() {
        return array(
            self::TYPE_MND,
        );
    }

    /**
     * @param $filePath
     * @return $this
     * @throws DirectDebitException
     */
    public function load($filePath)
    {
        if( false === file_exists($filePath) ) {
            throw new DirectDebitException('Podany plik nie istnieje');
        }

        $this->readerFile = new ReaderFile($filePath);
        return $this;
    }

    private $readerFile = null;

    /**
     * @return ReaderFile
     * @throws DirectDebitException
     */
    public function getFile()
    {
        if( null === $this->readerFile ) {
            throw new DirectDebitException('Brak załadowanego pliku!');
        }
        return $this->readerFile;
    }
}