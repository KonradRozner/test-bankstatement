<?php

namespace Vindication\BankStatement\Parser\Abstracts;

abstract class DataStorage
{
    protected $data = array();

    public function set($key, $value)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = array();
        }

        $this->data[$key][] = $value;
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return string | null
     */
    public function get($key)
    {
        return empty($this->data[$key]) ? null : trim(implode(" ", $this->data[$key]));
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this->data as $key => $values) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }
}