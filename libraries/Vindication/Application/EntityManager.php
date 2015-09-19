<?php

namespace Vindication\Application;

use Vindication\Abstracts;

class EntityManager
{
    protected $entity;
    protected $mapper;

    /**
     *
     * @param Abstracts\Entity $entity
     * @param Abstracts\Mapper|NULL $mapper
     */
    public function __construct(Abstracts\Entity $entity, Abstracts\Mapper $mapper = null)
    {
        $this->entity = $entity;
        $this->mapper = ($mapper === null) ? new Mapper() : $mapper;
    }

    /**
     * 
     * @return \Vindication\Application\Mapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * 
     * @return \Vindication\Abstracts\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    protected $primary;

    /**
     * @return string
     * @throws \Exception
     */
    protected function getPrimaryKey()
    {
        if (null === $this->primary) {
            $this->primary = $this->getMapper()->getPrimaryKey(
                $this->getEntity()->getDbTable()
            );
        }
        return $this->primary;
    }

    /**
     * zapisuje zmiany na encji
     * jezeli domyslna klasa Mappera dla encji posiada metode save(Abstracts\Entity $entity) zostanie wywolana zamiast save() na EntityManager
     *
     * @return void
     */
    public function save()
    {
        if (($mapper = $this->getEntity()->getMapper()) && method_exists($mapper, 'save')) {
            $mapper->save($this->getEntity());
            return true;
        }
        else
        {
            $tableName = str_replace('lt_', '', $this->getEntity()->getDbTable());
            $adapter   = $this->getMapper()->getAdapter();

            $bind = $this->getMapper()->getArrayData(
                    $this->getEntity(), $tableName
                );

            if ($id = $this->getEntity()->getID()) {
                $adapter->update($tableName, $bind, array( $this->getPrimaryKey() . ' =?' => $id ));
            } else {
                $adapter->insert($tableName, $bind);
                $this->getEntity()->set(
                        $this->getPrimaryKey(), $adapter->lastInsertId($tableName)
                    );
            }
        }
    }

    /**
     * @TODO
     */
    public function remove()
    {
        $tableName = $this->getEntity()->getDbTable();
        $adapter   = $this->getMapper()->getAdapter();

        $adapter->delete($tableName, array(
            $this->getPrimaryKey() . ' =?' => $this->getEntity()->getID()
        ));
    }

    private $_repository = null;

    public function getRepository()
    {
        if( null === $this->_repository ) {
            $this->_repository = new EntityRepository(
                    $this->getMapper(), $this->getEntity()
                );
        }

        return $this->_repository;
    }
}