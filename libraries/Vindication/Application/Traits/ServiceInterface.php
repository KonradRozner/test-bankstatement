<?php

namespace Vindication\Application\Traits;

interface ServiceInterface
{

    /**
     * 
     * @param string $name
     * @return \Vindication\Abstracts\Manager
     * @return \Vindication\Abstracts\Mapper
     */
    public function getService($name);
}