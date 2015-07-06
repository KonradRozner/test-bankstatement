<?php

class BankStatement_Bootstrap extends Zend_Application_Module_Bootstrap
{

    public function _initRoute()
    {
        Zend_Controller_Front::getInstance()->getRouter()
            ->addRoute('BankStatement', new Zend_Controller_Router_Route('/vindication/bankStatement', array(
                    'module' => 'BankStatement', 'controller' => 'index', 'action' => 'index'
                )))
        ;
    }
}