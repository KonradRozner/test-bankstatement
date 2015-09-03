<?php

namespace Vindication\Payment\DirectDebit\IO;

use Vindication\Payment\DirectDebit\Entity\Agreement;
use Vindication\Payment\DirectDebit\Iterator\AgreementIterator;

/**
 * klasa odpowiadajaca za generowanie pliku *.MNB
 *
 */
class WriterMNB extends AbstractWriter
{

    protected function init()
    {
        $this
            ->setIterator(new AgreementIterator)
            ->setEntity(new Agreement)
            ->setTemplate(new Template\TemplateMNB);
    }

    /**
     * @param string $fileName
     * @throws \Exception
     */
    public function output($fileName = null)
    {
        $fileName = $fileName ?: date('ymd');
        parent::output($fileName . '.MNB');
    }
}