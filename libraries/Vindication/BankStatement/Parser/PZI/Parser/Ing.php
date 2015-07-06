<?php

namespace Vindication\BankStatement\Parser\PZI\Parser;

use Vindication\BankStatement\Parser\Abstracts\Parser;
use Vindication\BankStatement\Parser\Factory;
use Vindication\BankStatement\Entity;

class Ing extends Parser
{

    /**
     * 
     * @return void
     */
    public function parse()
    {
        $reader  = $this->getReader();
        $content = $reader->getFile()->getContents();

        $lines  = explode("\n", trim($content));
        $header = $lines[0];

        array_shift($lines);

        $statement = $reader->getStatement();
        $file      = $reader->getFile();

        $statement->getFile()->set('parser', Factory::PARSER_ING_PZI);

        $number = 1;
        foreach ($lines as $lineNo => $line)
        {
            $storage = str_getcsv($line, ',', '"');

            $statement->getTransactions()->append(
                new Entity\Transaction(array(
                    'numer_pozycji'             => $number++,
                    'kontrahent_nr_konta'       => $storage[1],
                    'kontrahent_nazwa_adres'    => strtr($storage[2], array('|' => "\n")),
                    'tytul_operacji'            => strtr($storage[6], array('|' => "\n")),
                    'data_waluty'               => $storage[3],
                    'data_operacji'             => $storage[4],
                    'znak_operacji'             => null,
                    'kwota_operacji'            => $storage[5],
                    'referencja'                => null,
                ))
            );
        }
    }
}