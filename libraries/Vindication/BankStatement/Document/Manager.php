<?php

namespace Vindication\BankStatement\Document;

use Vindication\Abstracts;
use Vindication\BankStatement\Entity\Transaction;
use Vindication\BankStatement\Entity\Statement;

class Manager extends Abstracts\Manager
{

    /**
     *
     * @param Transaction $transaction
     * @param Iterator $iterator
     * @return \Vindication\Payment\Iterator\Payments
     * @internal param Iterator $documents
     */
    public function SimulateTransactionSettle(Transaction $transaction, Iterator $iterator)
    {
        return (new Settlement($transaction))
                ->getPayments($iterator)
        ;
    }

    /**
     *
     * @param Transaction $transaction
     * @param Iterator $iterator
     * @throws \Exception
     * @internal param Iterator $documents
     */
    public function TransactionSettle(Transaction $transaction, Iterator $iterator)
    {
        (new Settlement($transaction))
                ->Settle($iterator)
            ;

        $transaction->set('rozliczane_automatycznie', Transaction::SETTLED_MANUALLY);
        $transaction->getEntityManager()->save();

        $this->getService('StatementMapper')
            ->updateStatementStatus($transaction)
        ;
    }

    /**
     * @TODO przeniesc rozliczanie tutaj z Settlement\Manager
     * @param Statement $statement
     */
    public function StatementSettle(Statement $statement)
    {
        
    }

    /**
     * zwraca liste dokumentow ksiegowych
     *
     *
     * @param Transaction $transaction
     * @return \Vindication\BankStatement\Document\Iterator
     */
    public function getDocuments(Transaction $transaction)
    {
        /* przechowuje liste dokumentow ktore rozliczone maja zostac w 1-szej kolejnosci */
        $priority = new Iterator();

        /* przechowuje liste porostowanych dokumentow */
        $documents = new Iterator();

        /* faktury moga byc powiazane z notami Faktura zawiera note,
         * kwota noty w tym przypadku jest zawarta w fakturze - nie rozliczac noty jako kolejny dokument */
        $invoices = $this->getService('InvoiceMapper')->getStatementExecuteInvoices($transaction);
        /* @var $invoices \Vindication\Invoice\Iterator\Invoices */

        foreach ($invoices as $invoice) { /* wyszukuje faktury po numerze i dodaje je do listy jako piersze */
            /* @var $invoice \Vindication\Invoice\Entity\Invoice */
            if (false !== strpos($transaction->getTitle(), $invoice->getNumber())) {
                $priority->append($invoice);
                $invoices->remove($invoice);
            }
        }

        /* kazda rata ma fakture do wplat dodac oba klucze FK_ugody_raty i FK_Faktury */
        /* @var \Vindication\Installment\Mapper */
        $installments = $this->getService('InstallmentMapper')->getStatementExecuteInstallments($transaction);
        /* @var $installments \Vindication\Installment\Iterator\Installments */

        foreach($installments as $installment) {
            /* @var $installment \Vindication\Installment\Entity\Installment */
            if (false !== strpos($transaction->getTitle(), $installment->getInvoice()->getNumber() )) {
                $priority->append($installment);
                $installments->remove($installment);
            }
        }


        /* sortuje po datach faktury i raty */
        $sort = array();
        foreach ($invoices as $entity) {
            /* @var $entity \Vindication\Invoice\Entity\Invoice */
            $sort[] = array(substr($entity->get('DataFaktury'), 0, 10) => $entity);
        }
        foreach ($installments as $entity) {
            /* @var $entity \Vindication\Installment\Entity\Installment */
            $sort[] = array(substr($entity->get('data_raty'), 0, 10) => $entity);
        }
        uksort($sort, function($a, $b) use($sort) {
            return strcmp(key($sort[$a]), key($sort[$b]));
        });

        foreach ($sort as $array) {
            $documents->append($array[key($array)]);
        }

        /* @var \Vindication\Note\Mapper */
        $notes = $this->getService('NoteMapper')->getStatementExecuteNotes($transaction);
        /* @var $notes \Vindication\Note\Iterator\Notes */

        foreach($notes as $note) {
            /* @var $note \Vindication\Note\Entity\Note */
            if (false !== strpos($transaction->getTitle(), $note->getNumber())) {
                $priority->append($note);
                $notes->remove($note);
            }
        }

        foreach ($notes as $entity) {
            /* @var $entity \Vindication\Note\Entity\Note */
            if( $entity->getAmount() < 0 ) {
                /* nota odsetkowa jest pomijana ze wzgledu na ujemna wartosc */
                continue;
            }
            $documents->append($entity);
        }

        /* scala posortowane dokumenty */
        $results = new Iterator();
        foreach($priority as $item) {
            $results->append($item);
        }
        foreach($documents as $item) {
            $results->append($item);
        }

        return $results;
    }
}