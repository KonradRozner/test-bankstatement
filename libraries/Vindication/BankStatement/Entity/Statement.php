<?php

namespace Vindication\BankStatement\Entity;

use Vindication\Abstracts;
use Vindication\BankStatement\Iterator\Transactions;

class Statement extends Abstracts\Entity implements StatementEntityInterface
{

	protected $id  						= NULL;
    protected $numer_referencyjny       = NULL;
    protected $identyfikator_rachunku   = NULL;
    protected $numer_wyciagu            = NULL;
    protected $saldo_poczatkowe_znak    = NULL;
    protected $saldo_poczatkowe_data    = NULL;
    protected $saldo_poczatkowe_waluta  = NULL;
    protected $saldo_poczatkowe_kwota   = NULL;
    protected $saldo_biezace_znak       = NULL;
    protected $saldo_biezace_data       = NULL;
    protected $saldo_biezace_waluta     = NULL;
    protected $saldo_biezace_kwota      = NULL;
    protected $saldo_koncowe_znak       = NULL;
    protected $saldo_koncowe_data       = NULL;
    protected $saldo_koncowe_waluta     = NULL;
    protected $saldo_koncowe_kwota      = NULL;
    protected $rozliczane_automatycznie = NULL;
    protected $konto_ksiegowe           = NULL;
    
    
    public function getID() {
        return $this->id;
    }

    /**
     * zwraca nr konta IBAN
     *
     * @return string
     * @throws \Exception
     */
    public function getAccountNo() {
        return $this->get('identyfikator_rachunku');
    }

    protected $transactionsIterator = null;

    /**
     * 
     * @return \Vindication\BankStatement\Iterator\Transactions 
     */
    public function getTransactions()
    {
        if (null === $this->transactionsIterator) {
            $this->transactionsIterator = $this->getMapper()->getTransactions($this);
        }
        return $this->transactionsIterator;
    }

    /**
     * 
     * @param Transactions $transactions
     * @return \Vindication\BankStatement\Entity\Statement
     */
    public function setTransactions(Transactions $transactions)
    {
        $this->transactionsIterator = $transactions;
        return $this;
    }

    /**
     *
     * @return string data w formacie Y-m-d
     */
    public function getStatementDate() {
        return $this->get('saldo_koncowe_data');
    }
}