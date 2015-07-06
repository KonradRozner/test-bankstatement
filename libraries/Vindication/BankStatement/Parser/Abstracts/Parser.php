<?php

namespace Vindication\BankStatement\Parser\Abstracts;

use Vindication\BankStatement\Parser\InterfaceReader;
use Vindication\BankStatement\Parser\InterfaceParser;

abstract class Parser implements InterfaceParser
{
    protected $reader;

    /**
     * 
     * @param InterfaceReader $reader
     */
    public function __construct(InterfaceReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * 
     * @return \Vindication\BankStatement\Parser\InterfaceReader
     */
    protected function getReader()
    {
        return $this->reader;
    }
}