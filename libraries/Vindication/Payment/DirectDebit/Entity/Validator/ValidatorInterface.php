<?php

namespace Vindication\Payment\DirectDebit\Entity\Validator;

use Vindication\Payment\DirectDebit\Entity;

interface ValidatorInterface
{
    /**
     * @param Entity\EntityAbstract $entity
     */
    public function __construct(Entity\EntityAbstract $entity);

    /**
     * @throw \Vindication\Payment\DirectDebit\Exception
     */
    public function validate();
}