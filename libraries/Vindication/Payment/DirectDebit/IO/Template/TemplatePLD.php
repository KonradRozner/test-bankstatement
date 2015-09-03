<?php
namespace Vindication\Payment\DirectDebit\IO\Template;

use Vindication\Payment\DirectDebit\Entity\DirectDebit;

/**
 * szablon pliku pld
 */
class TemplatePLD extends AbstractTemplate
{
    protected function generate()
    {
        $lines = [];
        foreach($this->getIterator() as $entity) {
            /* @var $entity DirectDebit */

            $line = array
            (
                $entity->typTransakcji,
                $entity->dataWykonaniaPlatności,
                $entity->kwota,
                $entity->numerRozliczeniowyBanku,
                0,
                '"' . $entity->numerRachunkuWierzyciela . '"',
                '"' . $entity->numerRachunkuDluznika . '"',
                '"' . $entity->nazwaWierzyciela . '"',
                '"' . $entity->nazwaDluznika . '"',
                0,
                '"' . $entity->numerRozliczeniowyBankuDluznika . '"',
                '"' . $entity->opisPlatnosci . '"',
                '""',
                '""',
                '"' . $entity->klasyfikacjaDyspozycji . '"',
            );

            /* opcjonalne pole */
            if( !empty($entity->numerReferencyjnyTransakcji) ) {
                $line[] = '"' . $entity->numerReferencyjnyTransakcji . '"';
            }

            $lines[] = implode(',', $line);
        }

        $this->content = implode("\n", $lines);
    }
}