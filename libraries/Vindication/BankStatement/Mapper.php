<?php

namespace Vindication\BankStatement;

use Vindication\Abstracts\Mapper as AbstractsMapper;
use Vindication\BankStatement\Iterator;
use Vindication\BankStatement\Entity;
use Vindication\Contractor\Entity\Contractor;
use Vindication\Payment\Entity\Payment;
use Vindication\Payment\Mapper as PaymentMapper;
use Vindication\Auth;

class Mapper extends AbstractsMapper
{

    use MapperSettlement;

    /**
     * @entityRepository(class=\Vindication\BankStatement\Entity\Statement)
     * 
     * @param int $id
     * @param bool $fetchAllTransactions
     * @return \Vindication\BankStatement\Entity\Statement
     */
    public function getStatementById($id)
    {
        $select = $this->getAdapter()->select()
            ->from(array('s' => 'wyciag_bankowy_saldo', 's.*'))
            ->join(array('p' => 'wyciag_bankowy_plik'), 's.id = p.id', array('p.*'))
            ->where('s.id =?', (int) $id)
            ->limit(1)
        ;

        $statement = new Entity\Statement(
            (array) $this->getAdapter()->fetchRow($select)
        );
        if ($fetchAllTransactions) {
            $statement->setTransactions(
                $this->getTransactions($statement)
            );
        }

        return $statement;
    }

    /**
     * zwraca obiekt wyciagu z transakcjami 
     * UWAGA pomija transakcje z saldem debetowym (wyplaty z konta)
     * 
     * @param int $id 
     * @return \Vindication\BankStatement\Entity\Statement
     */
    public function getStatementToSettleById($id)
    {
        $select = $this->getAdapter()->select()
            ->from(array('s' => 'wyciag_bankowy_saldo', 's.*'))
            ->join(array('p' => 'wyciag_bankowy_plik'), 's.id = p.id', array('*'))
            ->where('s.id =?', (int) $id)
            ->limit(1)
        ;

        $statement = new Entity\Statement(
            (array) $this->getAdapter()->fetchRow($select)
        );
        $statement->setTransactions(
            $this->getTransactions($statement, false, function(\Zend_Db_Select $select) {
                    $select->where('rozliczane_automatycznie != 2 OR rozliczane_automatycznie IS NULL');
                })
            );
        return $statement;
    }

    /**
     *
     * @return Iterator\Statements
     */
    public function getStatements()
    {
        $select = $this->getAdapter()->select()
            ->from(array('s' => 'wyciag_bankowy_saldo', 's.*'))
            ->join(array('p' => 'wyciag_bankowy_plik'), 's.id = p.id', array('*'))
            ->join(array('pp' => 'lt_wyciag_bankowy_plik_parser'), 'p.parser = pp.PK_parser', array('bank' => 'nazwa'))
            ->where('p.FK_Cedenci =?', Auth::getInstance()->getIdentity()->getOwnerId())
        ;

        $this->addFilters($select, array(
                'nazwa' => 'p.nazwa',
                'bank' => 'pp.nazwa',
            ));

        if (!$this->getRequestData()->getSort()) {
            $select->order('p.id DESC');
        }

        $iterator  = new Iterator\Statements();
        $iterator->setPaginator($paginator = $this->getPaginator($select));

        foreach ($paginator as $result) {
            $iterator->append($entity = new Entity\Statement($result));
            $entity->setFile(
                    new Entity\File($result)
                );
        }

        return $iterator;
    }

    /**
     * 
     * @param \Vindication\BankStatement\Entity\Statement $statement
     * @param closure $closure
     * @return \Vindication\BankStatement\Iterator\Transactions
     */
    public function getTransactions(Entity\Statement $statement, $closure = null)
    {
        $select = $this->getAdapter()->select()
            ->from(array('o' => 'wyciag_bankowy_operacja', 's.*'))
            ->joinLeft(array('d' => 'dluznicy'), 'd.PK_dluznicy = o.kontrahent_id', array('d.*'))
            ->where('s.id =?', $statement->getID())

        ;
        $this->addFilters($select);

        if( null === $this->getRequestData()->getSort() ) {
            $select->order(array('o.id ASC'));
        }

        if (is_callable($closure)) {
            $closure($select);
        }

        $iterator = new Iterator\Transactions();
        $iterator->setPaginator($paginator = $this->getPaginator($select));
        
        foreach ($paginator as $result) {
            $iterator->append(
                $transaction = new Entity\Transaction($result)
            );
            if ($transaction->get('kontrahent_id')) {
                $transaction->setContractor(new Contractor($result));
            }
        }

        return $iterator;
    }
}