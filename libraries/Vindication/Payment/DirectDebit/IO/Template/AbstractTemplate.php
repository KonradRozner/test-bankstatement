<?php
namespace Vindication\Payment\DirectDebit\IO\Template;

use Vindication\Payment\DirectDebit\Entity;
use Vindication\Payment\DirectDebit\Iterator;

abstract class AbstractTemplate
{
    abstract protected function generate();

    protected $content = '';
    private $iterator;

    /**
     * @param Iterator\IteratorAbstract $iterator
     * @return $this
     */
    public function setIterator(Iterator\IteratorAbstract $iterator)
    {
        $this->iterator = $iterator;
        return $this;
    }

    /**
     * @throws \Exception
     * @return Iterator\IteratorAbstract $iterator
     */
    public function getIterator()
    {
        if( null === $this->iterator ){
            throw new \Exception('No ' . Iterator::class . ' set!');
        }
        return $this->iterator;
    }

    public function __toString()
    {
        $this->generate();
        
        return $this->content;
    }
}