<?php

namespace Vindication\BankStatement\Parser\Abstracts;

use Vindication\BankStatement\Parser\InterfaceReader;
use Vindication\BankStatement\Parser\InterfaceParser;
use Vindication\BankStatement\Parser\File;
use Vindication\BankStatement\Entity\Statement;
use Vindication\BankStatement\Parser\Factory;
use Vindication\BankStatement\Iterator\Transactions;

abstract class Reader implements InterfaceReader
{
    protected $availableParsers = array();

    /**
     * @var $parser \Vindication\BankStatement\Parser\SIMP2\Parser\ParserInterface 
     */
    protected $parser;

    /**
     * 
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;

        $this->getStatement()->setTransactions(new Transactions());
        $this->_parse();
    }

    final private function _parse()
    {
        $parserName = $this->getFile()->getParserName();

        if (isset($this->availableParsers[$parserName])) {
            $this->parser = new $this->availableParsers[$parserName]($this);

            if ($this->parser instanceof InterfaceParser) {
                $this->parser->parse();
            } else {
                throw new \Exception('Parser musi implementowac interfejs: InterfaceParser');
            }
        } else {
            throw new \Exception('Nie odnaleziono klasy parsera: "'.$parserName.'"');
        }
    }

    /**
     * 
     * @return array
     */
    public function getAvailableParsers()
    {
        return $this->availableParsers;
    }
    protected $file;

    public function getFile()
    {
        return $this->file;
    }
    protected $statement = null;

    /**
     *
     * @return \Vindication\BankStatement\Entity\Statement
     */
    public function getStatement()
    {
        if (null === $this->statement) {
            $this->statement = new Statement();
        }
        return $this->statement;
    }
}