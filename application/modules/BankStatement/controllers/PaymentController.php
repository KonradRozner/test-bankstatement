<?php

use Vindication\BankStatement\Messages;

class Bankstatement_PaymentController extends Vindication_BankStatement_Abstracts_Controller
{

    public function init()
    {
        parent::init();

        $this->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getResponse()->setHeader('Content-type', 'application/json');
    }

    /**
     * 
     *  
     */
    public function getpaymentsAction()
    {
        /* @var \Vindication\Payment\Mapper */
        $iterator = $this->getService('PaymentMapper')->getBankTransactionPayments(
                $this->getTransaction()
            );
        /* @var $iterator \Vindication\Payment\Iterator\Payments */

        $results = [];
        foreach($iterator as $payment) {
            /* @var $payment \Vindication\Payment\Entity\Payment */

            $terminPlatnosci = null;
            if( null !== ($document = $payment->getSettlementDocument()) ) {
                $terminPlatnosci = $document->getDocumentPaymentDate();
            }

            $results[] = array_merge($payment->toArray(), array(
                    'TerminPlatnosci' => $terminPlatnosci,
                ));
        }


        echo json_encode(array(
                'rows' => $results,
                'total' => $iterator->getPaginator()->getTotalItemCount()
            ), JSON_PRETTY_PRINT);
    }
}