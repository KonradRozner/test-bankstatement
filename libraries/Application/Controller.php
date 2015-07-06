<?php

use Vindication\Auth;

/* autoload inicjowany dopiero w init() */
require_once 'Vindication\Application\Traits\ServiceInterface.php';
require_once 'Vindication\Application\Traits\Service.php';

use Vindication\Application\Traits\ServiceInterface;
use Vindication\Application\EntityManager;
use Vindication\Application\Utils;
use Vindication\Application\Logger;
use Vindication\Application\Exception AS ApplicationException;

class Vindication_Application_Controller extends Zend_Controller_Action implements ServiceInterface
{

    use Vindication\Application\Traits\Service;

    public function init()
    {
        Client_AutoLoader::addNamespace('Vindication\\');

        if (!$this->getAuth()->hasIdentity()) {
            /* dla ajaxowych request'ow zwraca jsona z info o autentykacji */
            if ($this->getRequest()->isXmlHTTPRequest()) {
                $this->getResponse()->setHeader('Content-type', 'application/json');
                exit(json_encode(array(
                    'hasIdentity' => false
                )));
            } else {
                $this->redirect('/');
            }
        }

        $this->getFrontController()->throwExceptions(true);

        set_exception_handler(array(
            $this, 'error'
        ));
    }

    /**
     * 
     * @return \Vindication\Auth
     */
    protected function getAuth()
    {
        return Auth::getInstance();
    }

    /**
     *  
     */
    public function error(Exception $e)
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');

        if( APPLICATION_ENV == 'production' )
        {
            echo json_encode(array(
                    'success' => 0, 'error' => 1,
                    'message' => ( $e instanceof ApplicationException ) ? $e->getMessage() : 'Wystąpił błąd. Odśwież formatke i spróboj ponownie.',
                ));
            Logger::getInstance()->addException($e);
        }
        else {
            echo json_encode(array(
                    'success' => 0, 'error' => 1, 'message' => $e->getMessage(),
                ));
        }
    }

    /**
     * 
     * ustawia layout dla modulow "\Vindication\*"
     */
    public function layout()
    {
        \Zend_Layout::getMvcInstance()
            ->setLayoutPath(realpath(
                    $this->getFrontController()->getModuleDirectory().'/../Vindication/views'
            ))
            ->setLayout('layout')
        ;
    }

    /**
     * @param $entityName
     * @return EntityManager
     * @throws Exception
     */
    protected function getEntityManager($entityName)
    {
        $classPaths = Utils\Reflection::getClassPathsByEntityName($entityName);

        $entity = $classPaths->getEntityPath();
        $mapper = $classPaths->getMapperPath();

        return new EntityManager( new $entity(), new $mapper );
    }
}