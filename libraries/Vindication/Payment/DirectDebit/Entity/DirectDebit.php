<?php

namespace Vindication\Payment\DirectDebit\Entity;

/**
 * Export
 * Polecenie zapłaty - plik generowany/exportowany z fakturynki
 * format/plik PLD
 */
class DirectDebit extends EntityAbstract
{
    use DirectDebitProperties;

    /** format RRRRMMDD */
    public $dataWykonaniaPlatności = null;
    public $opisPlatnosci = null;

}