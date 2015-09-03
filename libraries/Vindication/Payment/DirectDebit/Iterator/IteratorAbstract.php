<?php

namespace Vindication\Payment\DirectDebit\Iterator;

use Vindication\Payment\DirectDebit\Entity;

abstract class IteratorAbstract extends \ArrayIterator
{

    final public function __construct() {
        parent::__construct();
    }

    /**
     * @param Entity\EntityAbstract $entity
     */
    public function append($entity) {
        if( $entity instanceof Entity\EntityAbstract ) {
            parent::append($entity);
        }
    }

}