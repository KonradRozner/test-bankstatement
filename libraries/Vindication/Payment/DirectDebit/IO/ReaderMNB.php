<?php

namespace Vindication\Payment\DirectDebit\IO;

use Vindication\Payment\DirectDebit\Entity;
use Vindication\Payment\DirectDebit\Iterator\AgreementIterator;

class ReaderMNB extends AbstractReader
{

    /**
     * @return AgreementIterator
     * @throws \Vindication\Payment\DirectDebit\Exception
     */
    public function read()
    {
        $lines = explode("\n", $this->getFile()->getContent());

        $iterator = new AgreementIterator();

        foreach($lines as $line)
        {
            $values = str_getcsv($line, ',', '"');

            $entity = new Entity\Agreement();
            $entity->typTransakcji                  = $values[0];
            $entity->dataZgody                      = $values[1];
            $entity->kwota                          = $values[2];
            $entity->numerRozliczeniowyBanku        = $values[3];
            //constant = 0
            $entity->numerRachunkuWierzyciela       = $values[5];
            $entity->numerRachunkuDluznika          = $values[6];
            $entity->nazwaWierzyciela               = $values[7];
            $entity->nazwaDluznika                  = $values[8];
            //constant = 0
            $entity->numerRozliczeniowyBankuDluznika = $values[10];
            $entity->opisZgody                      = $values[11];
            //constant = 0
            //constant = 0
            $entity->klasyfikacjaDyspozycji         = $values[14];
            //constant = 0

            $iterator->append($entity);
        }

        return $iterator;
    }
}