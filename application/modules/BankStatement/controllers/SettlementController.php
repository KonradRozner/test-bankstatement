<?php

use Vindication\BankStatement\Messages;
use Vindication\SettlementPeriod\Exception\PeriodIsClosed;

class Bankstatement_SettlementController extends Vindication_BankStatement_Abstracts_Controller
{

    public function init()
    {
        parent::init();

        $this->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getResponse()->setHeader('Content-type', 'application/json');
    }

    private function getRequestData()
    {
        $data = json_decode($this->getRequest()->getPost('data', '[]'));
        foreach ($data as $index => $item) {
            if ($item->document_type == 'overpayment') {
                unset($data[$index]);
            }
        }
        return $data;
    }

    /**
     * przelicza kwoty dla dokumentow ksiegowych
     * 
     */
    public function simulateAction()
    {
        $documents = $this->getService('StatementDocumentMapper')->getDocumentsToSettle(
            $this->getRequestData()
        );

        $payments = $this->getService('StatementDocumentManager')
            ->SimulateTransactionSettle($this->getTransaction(), $documents)
        ;

        $results = array();
        foreach ($payments as $payment) {
            /* @var $payment \Vindication\Payment\Entity\Payment */
            $results[] = array(
                'amount' => $payment->getAmount(),
            );
        }

        echo json_encode(array(
            'payments' => $results
        ));
    }


    /**
     * rozlicza pojedyncza transakcje (reczne rozliczenie)
     * @TODO walidacja!
     */
    public function transactionAction()
    {
        $request     = $this->getRequest();
        $transaction = $this->getTransaction();
        /* @var $transaction \Vindication\BankStatement\Entity\Transaction */

        /** spawdza czy miesiac rozliczeniowy jest otwarty */
        if( false === $this->getService('SettlementPeriodManager')->isPeriodOpen($transaction->getTransactionDate()) ) {
            throw new PeriodIsClosed(Messages::SETTLEMENT_BLOCKED);
        }

        $documents = $this->getService('StatementDocumentMapper')->getDocumentsToSettle(
                $this->getRequestData()
            );
        /* @var $documents \Vindication\BankStatement\Document\Iterator */

        if ($documents->count() == 0 && false === $request->getParam('overpayment', false)) {
            /* sprawdza czy sa jakies dokumenty, jezeli nie to wyswietla komunikat "czy rozliczyc na nadplate?" */
            echo json_encode(array(
                    'success' => 0, 'status' => 2,
                    'message' => Messages::NO_INVOICES_OVERPAYMENT
                ));
            return true;
        }

        /* @var $docManager \Vindication\BankStatement\Document\Manager */
        $this->getService('StatementDocumentManager')
                ->TransactionSettle($transaction, $documents)
            ;

        echo json_encode(array(
                'success' => 1,
                'settled_status' => $transaction->getStatement()->get('rozliczane_automatycznie'),
                'message' => Messages::TRANSACTION_SETTLED
            ));
    }

    /**
     * rozlicza wszystkie transakcje wyciagu, podzielone na strony request zawiera zmienna 'page'
     * 
     */
    public function statementAction()
    {
        $statementManager = $this->getService('StatementManager');
        /* @var $statementManager \Vindication\BankStatement\Manager */

        $adapter = $statementManager->getMapper()->getAdapter();

        try
        {
            $adapter->beginTransaction();

            $statement = $statementManager->getMapper()->getStatementToSettleById(
                    (int) $this->getRequest()->getPost('statement_id', 0)
                );
            /* @var $statement \Vindication\BankStatement\Entity\Statement */

            $statementManager->executeSettlement($statement);

            $paginator = $statement->getTransactions()->getPaginator();
            $page      = $paginator->getCurrentPageNumber();

            if ($page < ($last = $paginator->getPages()->last))
            {
                $percentage = round(100 / $last * $page, 0);

                echo json_encode(array(
                    'requestParams' => array(
                        'page' => ++$page,
                        'message' => sprintf(Messages::ONGOING_SETTLEMENT, $percentage)
                    )
                ));

            } else {
                echo json_encode(array(
                        'success' => (count($statementManager->getWarnings()) == 0) ? 1 : 2,
                        'message' => $statementManager->getMessage()
                    ), JSON_PRETTY_PRINT);
            }

            $adapter->commit();
        }
        catch (Zend_Db_Exception $e)
        {
            $adapter->rollBack();
            echo json_encode(array(
                    'success' => 0, 'message' => $e->getMessage()
                ));
        }
    }
}