<?php

namespace Vindication\BankStatement\Parser\PZI;

use Vindication\BankStatement\Parser\Abstracts;

class Reader extends Abstracts\Reader
{
    protected $availableParsers = array(
        'ing' => '\Vindication\BankStatement\Parser\PZI\Parser\Ing',
    );

}