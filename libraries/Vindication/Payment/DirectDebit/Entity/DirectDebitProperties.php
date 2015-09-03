<?php

namespace Vindication\Payment\DirectDebit\Entity;

/**
 * wlasnosci dla encji polecenie zapłaty
 */
trait DirectDebitProperties
{
    public $typTransakcji = 210;
    /** w importowanym pliku kwota jest w groszach (bez przecinków) */
    public $kwota = null;
    /** NRB strony zlecającej (Numer Rozliczeniowy Banku) */
    public $numerRozliczeniowyBanku = null;
    public $numerRachunkuWierzyciela = null;
    public $numerRachunkuDluznika = null;
    public $nazwaWierzyciela = null;
    public $nazwaDluznika = null;
    public $numerRozliczeniowyBankuDluznika = null;
    /** Klasyfikacja dyspozycji "01" dla dyspozycji PZ lub ”07” dla GOBI*/
    public $klasyfikacjaDyspozycji = '01';
    /** Numer referencyjny transakcji nadany przez klienta */
    public $numerReferencyjnyTransakcji = null;

}