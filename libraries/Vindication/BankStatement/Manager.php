<?php

namespace Vindication\BankStatement;

use Vindication\Abstracts;
use Vindication\BankStatement\Entity\Statement;
use Vindication\BankStatement\Entity\Transaction;
use Vindication\BankStatement\Entity\TransactionStatus;
use Vindication\BankStatement\Iterator\Transactions;
use Vindication\Contractor\Mapper as ContractorMapper;
use Vindication\BankStatement\Document\Settlement;
use Vindication\BankStatement\Settlement\Warning;
use Vindication\Application\Entity\AddressAccount;

class Manager extends Abstracts\Manager
{
    /**
     * 
     * @param Statement $statement
     * 
     * @return void
     */
    public function executeSettlement(Statement $statement)
    {
        $paginator = $statement->getTransactions()->getPaginator();

        /* resetuje sesje dla pakietu żądań */
        if ($paginator->getCurrentPageNumber() == $paginator->getPages()->first) {
            $this->cleanWarnings();
        }

        foreach ($statement->getTransactions() as $transaction) {
            /* @var $transaction \Vindication\BankStatement\Entity\Transaction */

            if (in_array($transaction->get('rozliczane_automatycznie'), array(
                    Transaction::SETTLED_AUTO, Transaction::SETTLED_MANUALLY, Transaction::SETTLED_YES)))
            {
                continue;
            }

            if (null === $transaction->getContractor()) {
                $this->addWarnings(
                    new Warning($transaction, Warning::TYPE_NO_CONTRACTOR)
                );
                $transaction->set('rozliczane_automatycznie', 0);
                $transaction->getEntityManager()->save();
                continue;
            }

            $documents = $this->getService('StatementDocumentManager')
                ->getDocuments($transaction)
            ;
            /* @var $documents \Vindication\BankStatement\Document\Iterator  */
            (new Settlement($transaction))->Settle($documents);

            $transaction->set('rozliczane_automatycznie', 1);
            $transaction->getEntityManager()->save();
        }

        /* aktualizuje info o wyciagu */
        if ($paginator->getCurrentPageNumber() == $paginator->getPages()->last) {
            /* 1 -> rozliczone calkowicie,  0 -> 1 lub wiecej transakcji nie zostala rozliczona */
            $count = count($this->getWarnings());
            if ($count == 0) {
                $statement->set('rozliczane_automatycznie', 1);
                $statement->getEntityManager()->save();
            } else if ($count != $paginator->getTotalItemCount()) {
                $statement->set('rozliczane_automatycznie', 0);
                $statement->getEntityManager()->save();
            }

            $this->getService('StatementMapper')->updateStatementStatus($statement);
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
        /* resetuje sesje dla pakietu żądań -> paginacji */
        if ($paginator->getCurrentPageNumber() == 1) {
            $this->cleanWarnings();
        }

        $contractorMapper = new ContractorMapper();

        foreach ($statement->getTransactions() as $transaction) {
            /* @var $transaction \Vindication\BankStatement\Entity\Transaction */
            if (null !== $transaction->get('kontrahent_id') && !$replace) {
                $this->addWarnings(new Warning($transaction, Warning::TYPE_NO_REPLACE));
                continue;
            }

            $accountNumber = $transaction->getSubAccountNo() ? : $transaction->getAccountNo();

            if (empty($accountNumber)) {
                $this->addWarnings(new Warning($transaction, Warning::TYPE_NO_ACCOUNT));
                continue;
            }

            $contractor = $contractorMapper->findByBankAccountNumber(
                    $accountNumber, $statement
                );

            if (null !== $contractor)
            {
                /* @var $contractor \Vindication\Contractor\Entity\Contractor */
                $transaction->set('kontrahent_id', $contractor->getID());
                $transaction->set('kontrahent', $contractor->getName());
                $transaction->getEntityManager()->save();
            }
            else {
                $this->addWarnings(new Warning($transaction, Warning::TYPE_NO_CONTRACTOR));
            }
        }
    }
}