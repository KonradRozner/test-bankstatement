<?php

namespace Vindication\BankStatement\Entity;

interface StatementEntityInterface
{
	/**
     * 
     * @return \Vindication\BankStatement\Iterator\Transactions 
     */
	public function getTransactions();
}