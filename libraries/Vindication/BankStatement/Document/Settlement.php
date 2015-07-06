<?php

namespace Vindication\BankStatement\Document;

use Vindication\Payment\Entity\Payment;
use Vindication\Payment\Mapper as PaymentMapper;
use Vindication\BankStatement\Entity\Transaction;
use Vindication\BankStatement\Mapper as BankStatementMapper;
use Vindication\Payment\Iterator\Payments;

class Settlement
{
    private $transaction;
    private $paymentMapper;
    private $statementMapper;

    /**
     * 
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;

        $this->paymentMapper   = new PaymentMapper();
        $this->statementMapper = new BankStatementMapper();
    }

    /**
     * rozlicza transakcje na dokumenty 
     * 
     * @param Iterator $iterator
     */
    public function Settle(Iterator $iterator)
    {
        $payments = $this->getPayments($iterator);

        foreach ($payments as $payment) 
        {
			$payment->setTransaction( $this->transaction );
			
			$this->paymentMapper->addStatementPayment($payment);
			$this->statementMapper->associateWithPayment($this->transaction, $payment);
        }
    }

    /**
     * lista wplat, uzywana takze do symulacji
     * 
     * @param Iterator $iterator
     * @return Payments
     */
    public function getPayments(Iterator $iterator)
    {
        $payments = new Payments();

        $amount = $this->transaction->getAmount();

        $overpayment = function($amount) use ($payments)
        {
            $payments->append(
                    new Payment(array('amount' => $amount, 'type' => Payment::TYPE_OVERPAYMENT))
                );
        };

        if ($iterator->count() == 0) {
            /* jezeli brak dokumentow jedna wplata z nadp³at¹ */
            $overpayment($amount);
            return $payments;
        }

        /* rozlicza kwote transakcji na dokumenty ksiegowe */
        foreach ($iterator as $document) 
        {
            /* @var $document SettlementInterface */
            if ($document instanceof Wrapper) {
                $due      = $document->getPaymentAmount(); /* kwota edytowana */
                $document = $document->getDocument();
                /* ograniczenia dla recznego rozliczenia */
                if ($due == 0 || $due > $document->getDocumentAmount()) {
                    $due = $document->getDocumentAmount();
                }
            } else {
                $due = $document->getDocumentAmount(); /* kwota z dokumentu Do_zaplaty/kwota_raty... */
            }

            if ($amount == 0) {
                break;
            }

            $money = 0;
            if ($amount > floatval($due)) 
            {
                $money  = round(floatval($due), 2);
                $amount -= $money;
                $amount = round($amount, 2);
            } 
            else {
                $money  = $amount;
                $amount = 0;
            }

            $payments->append(
                    (new Payment(array('amount' => $amount, 'type' => Payment::TYPE_TRANSFER)))
                        ->setSettlementDocument($document)
                );
        }

        if ($amount > 0) {
            /* jezeli jeszcze zostalo cos kasy (suma kwot faktur jest mniejsza niz kwota transakcji) to wstawia nadplate */
            $overpayment($amount);
        }

        return $payments;
    } 
}