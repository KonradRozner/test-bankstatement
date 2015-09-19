<?php

namespace Vindication\Application\Traits;

final class ApplicationServices
{
    private static $instance = null;

    private function __construct() {
        
    }

    /**
     * 
     * @return ApplicationServices
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new ApplicationServices();
        }
        return self::$instance;
    }

    private $avaiableService = array(
        'StatementManager'          => '\Vindication\BankStatement\Manager',
        'StatementMapper'           => '\Vindication\BankStatement\Mapper',
        'StatementDocumentManager'  => '\Vindication\BankStatement\Document\Manager',
        'StatementDocumentMapper'   => '\Vindication\BankStatement\Document\Mapper',
        'ContractorMapper'          => '\Vindication\Contractor\Mapper',
        'ContractorManager'         => '\Vindication\Contractor\Manager',
        'InvoiceMapper'             => '\Vindication\Invoice\Mapper',
        'PaymentMapper'             => '\Vindication\Payment\Mapper',
        'InstallmentMapper'         => '\Vindication\Installment\Mapper',
        'NoteMapper'                => '\Vindication\Note\Mapper',
        'EmployeeManager'           => '\Vindication\Employee\Manager',
        'ReportsManager'            => '\Vindication\Reports\Manager',
        'StatusesManager'           => '\Vindication\Status\Manager',
        'ApplicationMapper'         => '\Vindication\Application\Mapper',
        'CacheManager'              => '\Vindication\Application\CacheManager',
        'DirectDebitManager'        => '\Vindication\Payment\DirectDebit\Manager',
    );

    private $service = array();

    /**
     * 
     * @param string $name
     * @return type
     * @throws \Exception
     */
    public function getService($name)
    {
        if (isset($this->service[$name])) {
            return $this->service[$name];
        }
        if ( !isset($this->avaiableService[$name]) ) {

            if(($package = explode('Mapper', $name)[0]) !== $name) {
                $classPath = "\\Vindication\\{$package}\\Mapper";
            }
            else if(($package = explode('Manager', $name)[0]) !== $name) {
                $classPath = "\\Vindication\\{$package}\\Manager";
            }
            if( isset($classPath) && class_exists($classPath)) {
                return $this->service[$name] = new $classPath();
            }

            throw new \Exception('Nie odnaleziono klasy: '.$name);
        }
        $this->service[$name] = new $this->avaiableService[$name];
        return $this->getService($name);
    }
}

trait Service
{

    /**
     * 
     * @param string $name
     * @return type
     * @throws Exception
     */
    public function getService($name)
    {
        return ApplicationServices::getInstance()->getService($name);
    }
}