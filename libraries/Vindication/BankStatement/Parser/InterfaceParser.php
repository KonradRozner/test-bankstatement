<?php

namespace Vindication\BankStatement\Parser;

use Vindication\BankStatement\Parser\InterfaceReader;

interface InterfaceParser
{

    /**
     * 
     * @param InterfaceReader $reader
     */
    public function __construct(InterfaceReader $reader);

    /**
     * 
     * @return void
     */
    public function parse();
}