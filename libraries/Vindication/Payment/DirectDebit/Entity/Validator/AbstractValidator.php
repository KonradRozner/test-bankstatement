<?php

namespace Vindication\Payment\DirectDebit\Entity\Validator;

use Vindication\Payment\DirectDebit\Entity\EntityAbstract;
use Vindication\Payment\DirectDebit\Exception as DirectDebitException;

abstract class AbstractValidator implements ValidatorInterface
{
    protected $entity;

    /**
     * @param $entity
     */
    public function __construct(EntityAbstract $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @throw DirectDebitException
     */
    public function validate()
    {
        $entity = $this->entity;
        /* @var $entity EntityAbstract */

        if( !is_integer($entity->kwota) ) {
            throw new DirectDebitException('Błędna kwota. Kwota musi byc podana w groszach (bez przecinków)');
        }

    }
}