<?php

class Bankstatement_IndexController extends Vindication_Application_Controller
{

    public function init()
    {
        parent::init();
    }

    /**
     * renderuje glowna formatke
     */
    public function indexAction()
    {
        parent::layout();
    }
}