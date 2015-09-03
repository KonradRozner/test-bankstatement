<?php

namespace Vindication\Payment\DirectDebit\IO;

use Vindication\Payment\DirectDebit\Entity\DirectDebit;
use Vindication\Payment\DirectDebit\Iterator\DirectDebitIterator;

/**
 * klasa odpowiadajaca za generowanie pliku PLD
 * plik PLD służy do zlecania transakcji Polecenia zapłaty
 *
 */
class WriterPLD extends AbstractWriter
{
    protected function init()
    {
        $this
            ->setIterator(new DirectDebitIterator)
            ->setEntity(new DirectDebit)
            ->setTemplate(new Template\TemplatePLD);
    }
    /**
     * @param string $fileName
     * @throws \Exception
     */
    public function output($fileName = null)
    {
        $fileName = $fileName ?: date('ymd');
        parent::output($fileName . '.PLD');
    }
}