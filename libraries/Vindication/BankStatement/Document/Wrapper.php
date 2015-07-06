<?php

namespace Vindication\BankStatement\Document;

class Wrapper
{
    private $document = null;
    private $payment_amount;
    private $document_id;
    private $document_type;

    /**
     * 
     * @param \stdClass $data
     */
    public function __construct(\stdClass $data)
    {
        $this->document_id   = $data->document_id;
        $this->document_type = $data->document_type;

        $this->payment_amount = (float) $data->payment_amount;
    }

    /**
     * 
     * @return float
     */
    public function getPaymentAmount()
    {
        return (float) $this->payment_amount;
    }

    /**
     * 
     * @return int
     */
    public function getDocumentId()
    {
        return $this->document_id;
    }

    /**
     * 
     * @return string
     */
    public function getDocumentType()
    {
        return $this->document_type;
    }

    /**
     * 
     * @param \Vindication\BankStatement\Document\SettlementInterface $document
     * @return \Vindication\BankStatement\Document\Container
     */
    public function setDocument(SettlementInterface $document)
    {
        $this->document = $document;
        return $this;
    }

    /**
     * 
     * @return SettlementInterface
     */
    public function getDocument()
    {
        if (null === $this->document) {
            throw new \Exception('SettlementInterface document is NULL');
        }
        return $this->document;
    }
}