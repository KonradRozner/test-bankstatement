<?php

namespace Vindication\BankStatement\Parser;

use Vindication\Application\Parsers\Abstracts;

class Factory extends Abstracts\Factory
{
    const PARSER_ING_SIMP2 = 1;
    const PARSER_ING_PZI   = 2;
    const PARSER_ING_MT940 = 3;
    const PARSER_MIL_MT940 = 4;

    protected $availableReaders = array(
        self::FORMAT_SIMP2 	=> '\Vindication\BankStatement\Parser\SIMP2\Reader',
        self::FORMAT_PZI 	=> '\Vindication\BankStatement\Parser\PZI\Reader',
        self::FORMAT_MT940 	=> '\Vindication\BankStatement\Parser\MT940\Reader',
    );
    
    protected $file;

    /**
     * 
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * 
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
    protected $reader = null;

    /**
     * 
     * @param File $file
     * @return \Vindication\BankStatement\Parser\Abstracts\Reader
     * @throws Exception
     */
    public function getReader()
    {
        if (null !== $this->reader) {
            return $this->reader;
        }

        $reader = $this->availableReaders;
        $format = $this->getFile()->getFormatName();

        if (isset($reader[strtolower($format)])) {
            $this->reader = new $reader[strtolower($format)]($this->getFile());
        } else {
            throw new \Exception('Nie odnaleziono klasy Reader dla formatu '.$format);
        }

        return $this->reader;
    }
}