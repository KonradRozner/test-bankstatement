<?php

namespace Vindication\Payment\DirectDebit\Entity;

/**
 * IMPORT
 * służy do importowania pliku ze zgodami dla Dystrybucji Formularzy Zgody
 * format/plik MNB
 */
class Agreement extends EntityAbstract
{
    use DirectDebitProperties;

    /** format RRRRMMDD */
    public $dataZgody   = null;
    public $opisZgody   = null;
}