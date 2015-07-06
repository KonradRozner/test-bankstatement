<?php

use Vindication\BankStatement\Parser;
use Vindication\BankStatement\Messages;
use Vindication\Application\Response;
use Vindication\Application\Exception AS ApplicationException;

class Bankstatement_StatementController extends Vindication_BankStatement_Abstracts_Controller
{

    public function init()
    {
        parent::init();

        $this->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        //nie mozna ustawiac content-type
    }

    public function getstatementsAction()
    {
        $statements = $this->getService('StatementMapper')->getStatements();
        /* @var \Vindication\BankStatement\Mapper */

        $this->getResponse()->setHeader('Content-type', 'application/json');
        echo new Response\Iterator($statements);
    }

    /**
     * usuwa wyciag ze wszystkimi transakcjami
     * 
     */
    public function removeAction()
    {
        /* @var \Vindication\BankStatement\Mapper */
        $result = $this->getService('StatementMapper')->remove( $this->getStatement() );

        $this->getResponse()->setHeader('Content-type', 'application/json');
        echo json_encode(array(
            'success' => ($result === true) ? 1 : 0,
            'message' => $result,
        ));
    }

    /**
     * przetwarza/zapisuje importowany plik, sprawdza czy istnieje juz w bazie
     * 
     */
    public function addstatementAction()
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $post = (object) $this->getRequest()->getPost();

        $repository = $this->getService('StatementMapper')->getRepository('\Vindication\BankStatement\Entity\File');
        /* @var $repository \Vindication\Application\EntityRepository */

        $findBy = array(
                'nazwa' => basename($post->file),
                'FK_Cedenci' => $this->getAuth()->getIdentity()->getOwnerId(),
            );

        /* sprawdza czy plik juz zostal wczesniej zapisany */
        if (null !== $repository->findOneBy($findBy)) {
            throw new ApplicationException( sprintf( Messages::IMPORT_FILE_DUPLICATE, basename($post->file) ) );
        }


        /* walidacja pliku w parserze */
        $reader = (new Parser\Factory(
                new Parser\File($post->file, $post->format, $post->parser)
            ))->getReader();

        $manager = $this->getService('StatementManager');
        /* @var $manager \Vindication\BankStatement\Manager */
        $manager->checkIfOwnerHasAccount( $statement = $reader->getStatement() );
        $manager->saveStatement( $statement );

        echo json_encode(array(
                'success' => 1, 'statement_id' => $statement->getID()
            ));
    }

    /**
     * zapisuje transakcje z wczesniej zapisanego pliku (zapis i sprawdzanie pliku jest osobnym zadaniu HTTP -> addAction() )
     *
     */
    public function addtransactionsAction()
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $post = (object) $this->getRequest()->getPost();

        $reader = (new Parser\Factory(
                new Parser\File($post->file, $post->format, $post->parser)
            ))->getReader();

        $statement = $this->getStatement();
        $statement->setTransactions(
                $reader->getStatement()->getTransactions()
            );

        $this->getService('StatementManager')->saveTransactions($statement);

        echo json_encode(array(
                'success' => 1, 'statement_id' => $statement->getID()
            ));
    }

    /**
     * upload pliku
     * naglowek content-type musi byc textowy!
     */
    public function importAction()
    {
        $upload = new Zend_File_Transfer_Adapter_Http();
        $upload->receive();

        echo json_encode(array(
            'success' => 1,
            'file' => $upload->getFileName(),
        ));
    }
}