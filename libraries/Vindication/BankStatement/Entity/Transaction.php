<?php

namespace Vindication\BankStatement\Entity;

use Vindication\Abstracts;

class Transaction extends Abstracts\Entity
{
    const SETTLED_AUTO      = 1;
    const SETTLED_DEBET     = 2;
    const SETTLED_MANUALLY  = 3;
    const SETTLED_YES       = 4;
    const SETTLED_NO        = 5;

    protected $id                         = NULL;
    protected $saldo_id                   = NULL;
    protected $numer_pozycji              = NULL;
    protected $waluta                     = NULL;
    protected $data_waluty                = NULL;
    protected $data_operacji              = NULL;
    protected $znak_operacji              = NULL;
    protected $kwota_operacji             = NULL;
    protected $stala_operacji             = NULL;
    protected $referencja                 = NULL;
    protected $numer_operacji             = NULL;
    protected $rodzaj_operacji            = NULL;
    protected $tytul_operacji             = NULL;
    protected $kontrahent_nr_banku        = NULL;
    protected $kontrahent_nr_konta        = NULL;
    protected $kontrahent_nr_subkonta     = NULL;
    protected $kontrahent_nazwa_adres     = NULL;
    protected $kontrahent_iban            = NULL;
    protected $data_dokumentu             = NULL;
    protected $swrk                       = NULL;
    protected $typ_operacji               = NULL;
    protected $rozliczane_automatycznie   = 0;
    protected $kontrahent                 = NULL;
    protected $kontrahent_id              = NULL;
    protected $konto_ksiegowe             = NULL;

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->tytul_operacji;
    }

    /**
     *
     * @return float
     */
    public function getAmount()
    {
        return floatval($this->kwota_operacji);
    }

    /**
     *
     * @return string
     */
    public function getAccountNo()
    {
        return $this->kontrahent_nr_konta;
    }

    /**
     *
     * @return string
     */
    public function getSubAccountNo()
    {
        return $this->kontrahent_nr_subkonta;
    }

    public function getID() {
        return $this->id;
    }

    /**
     * @return string data w formacie Y-m-d
     * @throws \Exception
     */
    public function getTransactionDate() {
        return substr($this->get('data_operacji'), 0, 10);
    }

    protected $contractor = null;

    /**
     * 
     * @return \Vindication\Contractor\Entity\Contractor
     * @return NULL
     */
    public function getContractor()
    {
        return $this->contractor;
    }

    /**
     * 
     * @param Contractor $contractor
     * @return \Vindication\BankStatement\Entity\Transaction
     */
    public function setContractor(Contractor $contractor)
    {
        $this->contractor = $contractor;
        return $this;
    }
    
    private $statement = null;

    /**
     *
     * @return \Vindication\BankStatement\Entity\Statement | NULL
     */
    public function getStatement()
    {
        if (null === $this->statement) {
            $this->statement = $this->getMapper()->getStatementById($this->get('saldo_id'));
        }
        return $this->statement;
    }
}