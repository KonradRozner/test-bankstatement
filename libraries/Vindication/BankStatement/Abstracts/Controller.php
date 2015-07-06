<?php

use Vindication\BankStatement\Iterator\Transactions;
use Vindication\BankStatement\Messages;
use Vindication\BankStatement\Exception as BankStatementException;

abstract class Vindication_BankStatement_Abstracts_Controller extends Vindication_Application_Controller
{

    /**
     * zwraca obiekt Iterator\Transactions jezeli w requescie jest parametr transaction_id => id lub [id, id, ...]
     * 
     * @return Transactions
     */
    public function getTransactions()
    {
        $transaction_id = $this->getRequest()->getPost('transaction_id');

        $iterator = new Transactions();
        if (is_numeric($transaction_id)) 
        {
            $iterator->append($this->getTransaction());
        } 
        else if (count($data = json_decode($transaction_id, true))) {
            foreach ($data as $transaction_id) {
                $iterator->append(
                    $this->getService('StatementMapper')->getTransactionById($transaction_id)
                );
            }
        }

        return $iterator;
    }

    /**
     * zwraca obiekt kontrahenta jezeli w requescie jest parametr contractor_id
     * 
     * @return \Vindication\Contractor\Entity\Contractor
     * @throws Exception
     */
    protected function getContractor()
    {
        $request = $this->getRequest();

        $contractor = $this->getService('ContractorMapper')->getContractorById(
                $id = $request->getPost('contractor_id', $request->getParam('contractor_id', 0) )
            );

        if ( !$contractor->getID() ) {
            throw new BankStatementException( sprintf(Messages::CONTRACTOR_NOT_FOUND, $id) );
        }

        return $contractor;
    }

    /**
     * zwraca obiekt transakcji jezeli w requescie jest parametr transaction_id
     * 
     * @return \Vindication\BankStatement\Entity\Transaction
     * @throws Exception
     */
    protected function getTransaction()
    {
        $transaction = $this->getService('StatementMapper')->getTransactionById(
                $id = $this->getRequest()->getParam('transaction_id', 0)
            );

        if (!$transaction->getID()) {
            throw new BankStatementException( Messages::TRANSACTION_NOT_FOUND );
        }

        return $transaction;
    }

    /**
     * zwraca obiekt wyciagu bankowego jezeli w requescie jest parametr statement_id
     * 
     * @return \Vindication\BankStatement\Entity\Statement
     * @throws Exception
     */
    protected function getStatement()
    {
        $statement = $this->getService('StatementMapper')->getStatementById(
                $id = $this->getRequest()->getParam('statement_id', 0)
            );

        if (!$statement->getID()) {
            throw new BankStatementException( Messages::STATEMENT_NOT_FOUND );
        }

        return $statement;
    }
}