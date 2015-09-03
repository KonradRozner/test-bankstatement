<?php

namespace Vindication\Payment\DirectDebit\IO;

use Vindication\Payment\DirectDebit\Entity;
use Vindication\Payment\DirectDebit\Iterator\OrderRegisterIterator;

class ReaderMND extends AbstractReader
{

    /**
     * @return OrderRegisterIterator
     * @throws \Vindication\Payment\DirectDebit\Exception
     */
    public function read()
    {
        $lines = explode("\n", trim($this->getFile()->getContent()));
        $header = explode(',', $lines[0]);
        array_shift($lines);

        $iterator = new OrderRegisterIterator();

        foreach($lines as $line)
        {
            $values = str_getcsv($line, ',', '"');

            $entity = new Entity\OrderRegister();
            $entity->status         = $values[0];
            $entity->nrKonta        = $values[1];
            $entity->nazwaDluznika  = $values[2];
            $entity->data           = $values[3];
            $entity->dataZlecenia   = $values[4];
            $entity->kwota          = $values[5];
            $entity->opis           = $values[6];

            $iterator->append($entity);
        }

        return $iterator;
    }
}