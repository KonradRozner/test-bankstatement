<?php

namespace Vindication\Payment\DirectDebit\Entity;

use Vindication\Payment\DirectDebit\Entity\Validator\ValidatorInterface;

abstract class EntityAbstract
{
    private $validator = null;

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        if( null === $this->validator )
        {
            $validatorName = @end(explode('\\', get_class($this)));
            $validatorClassName = "\\Vindication\\Payment\\DirectDebit\\Entity\\Validator\\{$validatorName}";
            $this->validator = (new $validatorClassName($this));
        }

        return $this->validator;
    }

    /**
     * @param string|array $property
     * @param null $value
     * @throws \Exception
     * @return $this
     */
    public function set($property, $value = null)
    {
        if( is_array($property) )
        {
            foreach($property as $propertyName => $value) {
                $this->set($propertyName, $value);
            }
        }
        else
        {
            if( !property_exists($this, $property) ) {
                throw new \Exception("Property {$property} doesn't exists! Class::" . get_class($this));
            }
            $this->{$property} = $value;
        }

        return $this;
    }

}