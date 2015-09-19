<?php

namespace Vindication\BankStatement;

use Vindication\Abstracts;
use Vindication\BankStatement\Entity\Statement;
use Vindication\BankStatement\Entity\Transaction;
use Vindication\BankStatement\Iterator\Transactions;
use Vindication\BankStatement\Settlement\Manager as SettlementManager;
use Vindication\BankStatement\Settlement\Warning;
use Vindication\BankStatement\Settlement\WarningManager;
use Vindication\Contractor\Mapper as ContractorMapper;

class Manager extends Abstracts\Manager
{

    private $settlementManager = null;

    /**
     * @return \Vindication\BankStatement\Settlement\Manager
     */
    public function getSettlementManager()
    {
        if( null === $this->settlementManager ) {
            $this->settlementManager = new SettlementManager($this);
        }
        return $this->settlementManager;
    }

    /**
     * @param Statement $statement
     * @return Validator
     */
    public function getValidator(Statement $statement)
    {
        return new Validator($statement, $this);
    }

    /**
     * @param Transactions $transactions
     * @param int $status
     * @throws \Exception
     */
    public function changeTransactionsStatus(Transactions $transactions, $status)
    {
        $adapter = $this->getMapper()->getAdapter();

        try
        {
            $adapter->beginTransaction();

            foreach ($transactions as $transaction) {
                /* @var $transaction \Vindication\BankStatement\Entity\Transaction */
                $transaction
                    ->setStatus($status)
                    ->getEntityManager()->save()
                ;
                $ids[] = $transaction->getID();

                if ($status == Transaction::SETTLED_NO) {
                    $this->getService('PaymentMapper')
                        ->removeTransactionPayments($transaction)
                    ;
                }
            }

            $this->getService('StatementMapper')->updateStatementStatus($transaction);
            /* @var \Vindication\BankStatement\Mapper */


            $adapter->commit();

        } catch (\Zend_Db_Exception $e)
        {
            $adapter->rollBack();
            throw $e;
        }
    }

    /**
     * zapisuje plik i naglowki wyciagu (saldo)
     *
     * @param Statement $statement
     * @throws \Exception
     * @return void
     */
    public function saveStatement(Statement $statement)
    {
        $adapter = $this->getMapper()->getAdapter();

        try
        {
            $adapter->beginTransaction();

            $statement->getFile()->getEntityManager()->save();

            $statement
                ->setFile($statement->getFile())
                ->getEntityManager()->save()
            ;

            $adapter->commit();

        }
        catch (\Zend_Db_Exception $e)
        {
            $adapter->rollBack();
            throw $e;
        }
    }

    /**
     * dopasowyje dluznika do transakcji wyciagu
     * 
     * @param Statement $statement
     * @param bool $replace TRUE jezeli zastopic przypisanych kontrahentow
     */
    public function mergeContractors(Statement $statement, $replace = false)
    {
        $paginator = $statement->getTransactions()->getPaginator();

        if ( $paginator->getCurrentPageNumber() == $paginator->getPages()->first ) {
            /* resetuje sesje dla pakietu żądań -> paginacji */
            $this->getWarningManager()->cleanWarnings();
        }

        $contractorMapper = new ContractorMapper();

        foreach ($statement->getTransactions() as $transaction) {
            /* @var $transaction \Vindication\BankStatement\Entity\Transaction */
            if (null !== $transaction->get('kontrahent_id') && !$replace)
            {
                $this->getWarningManager()->addWarning()->setType(Warning::TYPE_NO_CONTRACTOR);
                continue;
            }

            $accountNumber = $transaction->getSubAccountNo() ? : $transaction->getAccountNo();

            if (empty($accountNumber))
            {
                $this->getWarningManager()->addWarning()->setType(Warning::TYPE_NO_ACCOUNT);
                continue;
            }

            $contractor = $contractorMapper->findByBankAccountNumber($accountNumber, $statement);

            if (null !== $contractor)
            {
                /* @var $contractor \Vindication\Contractor\Entity\Contractor */
                $transaction
                    ->setContractor($contractor)
                    ->getEntityManager()->save();
            }
            else {
                $this->getWarningManager()->addWarning()->setType(Warning::TYPE_NO_CONTRACTOR);
            }
        }
    }

    private $warningManager = null;

    /**
     * @return WarningManager
     */
    public function getWarningManager()
    {
        if( null === $this->warningManager ) {
            $this->warningManager = new WarningManager();
        }

        return $this->warningManager;
    }

}