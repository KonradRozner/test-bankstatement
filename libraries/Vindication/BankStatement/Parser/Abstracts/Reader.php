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

        $this->getStatement()->setTransactions(
            new Transactions()
        );
        $this->_validate();
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
     * @throws \Exception
     */
    final private function _validate()
    {
        /* sprawdza naglowek czy jest poprawny format */
        list($firstLine) = explode("\n", trim($this->getFile()->getContents()));

        switch ($this->getFile()->getFormatName()) {
            case Factory::FORMAT_SIMP2:
                switch ($this->getFile()->getParserName()) {
                    case Factory::BANK_ING:
                        if (false === strpos(strtolower($firstLine), '<simp2>')) {
                            throw new \Exception("Błędny format pliku. Bank: <b>ING</b>, wymagany format: <b>SIMP2</b>");
                        }
                        break;
                }
                break;
            case Factory::FORMAT_PZI:
                switch ($this->getFile()->getParserName()) {
                    case Factory::BANK_ING:
                        if (false === strpos(strtolower($firstLine), 'pzbsk')) {
                            throw new \Exception("Błędny format pliku. Bank: <b>ING</b>, wymagany format: <b>PZI</b>");
                        }
                        break;
                }
                break;
            case Factory::FORMAT_MT940:
                switch ($this->getFile()->getParserName()) {
                    case Factory::BANK_ING:
                        if (false === strpos(strtolower($firstLine), 'mt940')) {
                            throw new \Exception("Błędny format pliku. Bank: <b>ING</b>, wymagany format: <b>MT940</b>");
                        }
                        break;
                    case Factory::BANK_MILLENIUM:
                        if (!preg_match('/:20:[0-9]{7}/', $firstLine)) {
                            throw new \Exception("Błędny format pliku. Bank: <b>Millenium</b>, wymagany format: <b>MT940</b>");
                        }
                        break;
                }
                break;
        }
        //var_dump($firstLine, $this->getFile()->getParserName()); exit();
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