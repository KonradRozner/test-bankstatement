<?php

use Vindication\BankStatement\Messages;

class Bankstatement_DocumentController extends Vindication_BankStatement_Abstracts_Controller
{

    public function init()
    {
        parent::init();

        $this->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getResponse()->setHeader('Content-type', 'application/json');
    }

    /**
     * sprawdza czy transakcja ma przypisanego kontrahenta i czy kontrahent ma jakies otwarte dokumenty ksiegowe
     * noty, raty, fv...
     * 
     */
    public function hasdocumentsAction()
    {
        $transaction = $this->getTransaction();

        if (null === $transaction->getContractor()) {
            echo json_encode(array(
                'status' => 'nocontractor', 'message' => Messages::TRANSACTION_HAS_NO_CONTRACTOR
            ));
            return false;
        }

        $iterator = $this->getService('StatementDocumentManager')->getDocuments($transaction);

        if (0 === $iterator->count()) {
            echo json_encode(array(
                'status' => 'overpayment', 'message' => Messages::CONTRACTOR_HAS_NO_DOCUMENTS
            ));
            return false;
        }

        echo json_encode(array(
            'status' => 'hasdocuments',
            'rows' => $iterator->toArray()
            ), JSON_PRETTY_PRINT);
    }
}