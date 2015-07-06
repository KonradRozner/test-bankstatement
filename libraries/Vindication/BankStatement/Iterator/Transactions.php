<?php

namespace Vindication\BankStatement\Iterator;

use Vindication\BankStatement\Entity;
use Vindication\Abstracts\Iterator;

class Transactions extends Iterator
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 
     * @param \Vindication\BankStatement\Entity\Transaction
     */
    public function append($entity)
    {
        if ($entity instanceof Entity\Transaction) {
            parent::append($entity);
        }
    }
}