<?php

namespace Vindication\Application;

use Vindication\Abstracts;
use Vindication\Application\Utils;

class EntityRepository
{
    protected $mapper;
    protected $entity;

    /**
     *
     * @param \Vindication\Abstracts\Mapper $mapper
     * @param string $entityName
     * @throws \Exception
     */
    public function __construct(Abstracts\Mapper $mapper, $entityName)
    {
        if( is_string($entityName) )
        {
            $classPaths = Utils\Reflection::getClassPathsByEntityName($entityName);
            $entityClass = $classPaths->getEntityPath();

            if (false === ($entity = new $entityClass) instanceof Abstracts\Entity) {
                throw new \Exception('Class '.$entityClass.' must be instance of Abstracts\Entity!');
            }
        }
        else if( $entityName instanceof Abstracts\Entity ) {
            $entity = $entityName;
        }
        else {
            throw new \Exception('$entityName not valid!');
        }

        $this->entity = $entity;
        $this->mapper = $mapper;
    }

    /**
     * 
     * @return \Vindication\Application\Mapper
     */
    protected function getMapper()
    {
        return $this->mapper;
    }

    /**
     * 
     * @return \Vindication\Abstracts\Entity
     */
    protected function getEntity()
    {
        return $this->entity;
    }

    /**
     * @TODO
     * 
     * @param array $params
     * @return \Vindication\Abstracts\Entity | NULL
     */
    public function findOneBy(array $params)
    {
        $tableName = $this->getEntity()->getDbTable();
        $adapter   = $this->getMapper()->getAdapter();
        //$primary    = $this->getMapper()->getPrimaryKey($tableName);  /* mapper->getEntityById */
        $select    = $adapter->select()->from(str_replace('lt_', '', $tableName));

        foreach ($params as $fieldName => $value) {
            $select->where($fieldName.' =?', $value)->limit(1);
        }

        $this->getEntity()
            ->set((array) $adapter->fetchRow($select))
        ;

        if ($this->getEntity()->getID()) {
            return $this->getEntity();
        }
        return null;
    }

    /**
     * wykorzystuje klase Mapper i metode get{ENTITY_NAME}ById($id)
     * 
     * @param int $id
     * @return Abstracts\Entity | NULL
     * @throws \Exception
     */
    public function find($id)
    {
        $entityName   = explode('\\', get_class($this->getEntity()))[1];
        $mapperName   = "\\Vindication\\{$entityName}\\Mapper";
        $entityGetter = "get{$entityName}ById";

        if (false === class_exists($mapperName)) {
            throw new \Exception('Mapper not exists: '.$mapperName);
        }

        $mapper = strpos($mapperName, get_class($this->getMapper())) ? $this->getMapper()
                : new $mapperName()
        ;
        if (false === method_exists($mapper, $entityGetter)) {
            throw new \Exception('Mapper has no method: '.$entityGetter);
        }

        return $mapper->{$entityGetter}($id);
    }
}