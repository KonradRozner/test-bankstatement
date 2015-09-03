<?php

namespace Vindication\Payment\DirectDebit\Entity\Validator;

use Vindication\Payment\DirectDebit\Entity;
use Vindication\Payment\DirectDebit\Exception as DirectDebitException;

class DirectDebit extends AbstractValidator
{
    /**
     * @throw DirectDebitException
     */
    public function validate()
    {
        $entity = $this->entity;
        /* @var $entity Entity\DirectDebit */

        if( !preg_match("/^[0-9]{4}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])$/", $entity->dataWykonaniaPlatności) ) {
            throw new DirectDebitException('Błędna data wykonania płatności. Wymagany format: RRRRMMDD');
        }

        parent::validate();
    }
}