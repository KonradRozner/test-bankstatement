<?php

namespace Vindication\BankStatement\Iterator;

use Vindication\BankStatement\Entity;
use Vindication\Abstracts\Iterator;

class Statements extends Iterator
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 
     * @param \Vindication\BankStatement\Entity\Statement
     */
    public function append($entity)
    {
        if ($entity instanceof Entity\Statement) {
            parent::append($entity);
        }
    }
}