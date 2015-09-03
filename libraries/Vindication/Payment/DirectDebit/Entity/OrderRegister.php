<?php

namespace Vindication\Payment\DirectDebit\Entity;

/**
 * Export
 * rejestr zlecen
 */
class OrderRegister extends EntityAbstract
{
    public $status = null;
    public $nrKonta = null;
    public $nazwaDluznika = null;
    public $data = null;
    public $dataZlecenia = null;
    public $kwota = null;
    public $opis = null;
}