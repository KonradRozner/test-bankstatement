<?php
namespace Vindication\Payment\DirectDebit\IO\Template;

use Vindication\Payment\DirectDebit\Entity\Agreement;

/**
 * szablon pliku *.MNB
 */
class TemplateMNB extends AbstractTemplate
{
    public function generate()
    {
        $lines = [];
        foreach($this->getIterator() as $entity) {
            /* @var $entity Agreement */

            $line = array
            (
                $entity->typTransakcji,
                $entity->dataZgody,
                $entity->kwota,
                $entity->numerRozliczeniowyBanku,
                0,
                '"' . $entity->numerRachunkuWierzyciela . '"',
                '"' . $entity->numerRachunkuDluznika . '"',
                '"' . $entity->nazwaWierzyciela . '"',
                '"' . $entity->nazwaDluznika . '"',
                0,
                '"' . $entity->numerRozliczeniowyBankuDluznika . '"',
                '"' . $entity->opisZgody . '"',
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