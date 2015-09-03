<?php

namespace Vindication\Payment\DirectDebit\IO;

use Vindication\Payment\DirectDebit\Iterator\IteratorAbstract;
use Vindication\Payment\DirectDebit\Entity\EntityAbstract;

abstract class AbstractWriter
{
    abstract protected function init();

    final public function __construct()
    {
        $this->init();
    }

    const TYPE_PLD = 'PLD';
    const TYPE_MNB = 'MNB';

    /**
     * @return array
     */
    public static function getAvailableWriterTypes() {
        return array(
            self::TYPE_PLD, self::TYPE_MNB
        );
    }

    private $entity = null;

    /**
     * @param EntityAbstract $entity
     * @return $this
     */
    protected function setEntity(EntityAbstract $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @var Template\AbstractTemplate
     */
    private $template = null;

    /**
     * @param Template\AbstractTemplate $template
     * @return $this
     */
    protected function setTemplate(Template\AbstractTemplate $template)
    {
        $this->template = $template;
        return $this;
    }


    private $iterator = null;

    /**
     * @param IteratorAbstract $iterator
     * @return $this
     */
    protected function setIterator(IteratorAbstract $iterator)
    {
        $this->iterator = $iterator;
        return $this;
    }

    /**
     * @return IteratorAbstract
     * @throws \Exception
     */
    protected function getIterator()
    {
        if( null === $this->iterator ) {
            throw new \Exception('No ' . IteratorAbstract::class . ' set!');
        }
        return $this->iterator;
    }

    private $writerFile = null;

    /**
     * @return WriterFile
     */
    public function getFile()
    {
        if( null === $this->writerFile ) {
            $this->writerFile = new WriterFile();
        }
        return $this->writerFile;
    }


    /**
     * @param array $data
     * @throws \Exception
     * @return $this
     */
    public function addItem(array $data)
    {
        if( null === $this->entity ) {
            throw new \Exception('No ' . EntityAbstract::class . ' set!');
        }

        $entity = clone $this->entity;
        $entity->set($data);
        $this->getIterator()->append($entity);

        return $this;
    }

    /**
     * waliduje i zapisuje plik
     * @throws \Exception
     * @return $this
     */
    public function save()
    {
        foreach($this->getIterator() as $entity) {
            /* @var $entity EntityAbstract */
            $entity->getValidator()->validate();
        }

        if( null === $this->template ) {
            throw new \Exception('No template set!');
        }

        $this->template->setIterator( $this->getIterator() );
        $this->getFile()->putContent($this->template);

        return $this;
    }

    /**
     * php://output
     *
     * @param $fileName
     * @throws \Exception
     */
    protected function output($fileName)
    {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment;filename="' . $fileName);
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        file_put_contents('php://output', $this->getFile()->getContent());
    }
}