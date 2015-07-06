<?php

namespace Vindication\BankStatement\Document;

use Vindication\Abstracts;

class Mapper extends Abstracts\Mapper
{

    /**
     * 
     * @param array $documents Lista obiektow stdClass zawierajaca dane z grida "dokumenty do rozliczenia"
     * @return \Vindication\BankStatement\Document\Iterator
     */
    public function getDocumentsToSettle(array $documents)
    {
        $get = function($data) {
            if (in_array($data->document_type, array('note', 'invoice', 'installment'))) 
            {
                $entity = $this->getRepository( $entityName = ucfirst($data->document_type) )->find($data->document_id);

                if (null === $entity) {
                    throw new \Exception("Entity not found: {$entityName} Id:{$data->document_id}");
                }

                return (new Wrapper($data))->setDocument($entity);
            } else {
                throw new \Exception('Unknown document type: '.$data->document_type);
            }
        };

        $iterator = new Iterator();

        foreach ($documents as $data) {
            $iterator->append($get($data));
        }

        return $iterator;
    }
}