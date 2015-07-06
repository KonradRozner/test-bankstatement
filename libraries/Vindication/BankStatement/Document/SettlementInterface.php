<?php

namespace Vindication\BankStatement\Document;

use Vindication\Invoice\Entity\Invoice;
use Vindication\Note\Entity\Note;
use Vindication\Installment\Entity\Installment;

interface SettlementInterface
{

    /**
     * @return float
     * @throws \Exception
     */
    public function getDocumentAmount();

    /**
     * @return string
     * @throws \Exception
     */
    public function getDocumentPaymentDate();
}

trait SettlementDocument
{

    final public function getDocumentAmount()
    {
        if ($this instanceof Invoice) {
            return (float) $this->get('do_zaplaty');
        }
        else if ($this instanceof Note) {
            return (float) $this->get('kwota_noty');
        }
        else if ($this instanceof Installment) {
            return (float) $this->get('kwota_raty');
        }
        else throw new \Exception('Unknown document type!');
    }

    /**
     * @return string
     * @throws \Exception
     */
    final public function getDocumentPaymentDate()
    {
        if ($this instanceof Invoice) {
            return substr($this->get('termin_platnosci'), 0, 10);
        }
        else if ($this instanceof Note) {
            return substr($this->get('termin_platnosci'), 0, 10);
        }
        else if ($this instanceof Installment) {
            return substr($this->get('data_raty'), 0, 10);
        }
        else throw new \Exception('Unknown document type!');
    }
}