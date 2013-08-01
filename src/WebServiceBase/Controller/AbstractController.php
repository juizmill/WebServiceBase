<?php
namespace WebServiceBase\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;


/**
 * Class AlunoController
 * @package Aluno\Controller
 */
abstract class AbstractController extends AbstractRestfulController
{

    /**
     * @var
     */
    protected $table;
    /**
     * @var string
     */
    protected $tableModel;

    public function __construct(){
        if (is_null($this->tableModel))
            throw new \InvalidArgumentException('Class mapping not defined');
    }


    /**
     * Lista todos os registro por paginação GET
     * @return JsonModel
     */
    public function getList()
    {
        $data = $this->getTable()->fetchAllPaginator(3, 6);

        return new JsonModel(array('data' => $data));
    }


    /**
     * Lista um registro especifico GET
     * @param mixed $cod
     * @return JsonModel
     */
    public function get($cod)
    {
        $data = $this->getTable()->findBy(array('COD' => $cod));

        return new JsonModel(array('data' => $data));
    }


    /**
     * Cadastra novo registro POST
     * @param mixed $data
     * @return JsonModel
     */
    public function create($data)
    {

        if ($data) {

            $insert = new $this->tableModel();
            $insert->exchangeArray($data);

            $arquivo = $this->getTable()->save($insert);

            if ($arquivo) {
                return new JsonModel(array('data' => array('success' => true)));
            } else {
                return new JsonModel(array('data' => array('success' => false)));
            }

        } else {
            return new JsonModel(array('data' => array('success' => false)));
        }

    }


    /**
     * Altera um registro PUT
     * @param mixed $cod
     * @param mixed $data
     * @return JsonModel
     */
    public function update($cod, $data)
    {
        $data['COD'] = $cod;

        if ($data) {
            $update = new $this->tableModel();
            $update->exchangeArray($data);

            $arquivo = $this->getTable()->save($update);

            if ($arquivo) {
                return new JsonModel(array('data' => array('success' => true)));
            } else {
                return new JsonModel(array('data' => array('success' => false)));
            }

        } else {
            return new JsonModel(array('data' => array('success' => false)));
        }

    }


    /**
     * Deleta um registro DELETE
     * @param mixed $cod
     * @return JsonModel
     */
    public function delete($cod)
    {
        if ($cod) {

            $cod = (int)$cod;

            $arquivo = $this->getTable()->delete(array('COD' => $cod));

            if ($arquivo) {
                return new JsonModel(array('data' => array('success' => true)));
            } else {
                return new JsonModel(array('data' => array('success' => false)));
            }

        } else {
            return new JsonModel(array('data' => array('success' => false)));
        }

    }


    /**
     * Retorna a tabela instanciada
     * @return array|object
     */
    public function getTable()
    {
        if (!$this->table) {
            $sm          = $this->getServiceLocator();
            $this->table = $sm->get($this->tableModel);
        }

        return $this->table;
    }

}