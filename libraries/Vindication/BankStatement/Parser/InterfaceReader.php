<?php

namespace Vindication\BankStatement\Parser;

interface InterfaceReader
{

    /**
     * 
     * @param File $file
     */
    public function __construct(File $file);

    /**
     * 
     * @return \Vindication\BankStatement\Abstracts\File
     */
    public function getFile();

    /**
     * Metoda musi zwracac encje wyciągu
     * 
     * @return \Vindication\BankStatement\Entity\Statement
     */
    public function getStatement();
}