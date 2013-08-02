<?php

namespace WebServiceBase\Model;

use Zend\Db\TableGateway\Exception\InvalidArgumentException;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;


/**
 * Class ArquivoTable
 * @package Aluno\Model
 */
abstract class AbstractTable extends AbstractTableGateway
{
    /**
     * Nome da Tabela
     * @var string
     */
    protected $table;
    /**
     * Classe mapeada
     * @var string
     */
    protected $classTable;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        if (is_null($this->table))
            throw new InvalidArgumentException('Table name is not defined');

        if (is_null($this->classTable))
            throw new InvalidArgumentException('Class mapping not defined');


        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new $this->classTable());
        $this->initialize();
    }

    /**
     * Retorna todos os registros
     * @param null $order
     * @return array
     */
    public function fetchAll($order = null)
    {
        if (!is_null($order))
            $order = "ORDER BY {$order}";

        $sql = "SELECT {$this->table}.*  FROM {$this->table} {$order}";

        $statement = $this->adapter->createStatement($sql);
        $resultSet = new ResultSet;
        $result = $resultSet->initialize($statement->execute());

        //Converte os Valores para UTF-8
        $encodedArray = array();
        foreach ($result->toArray() as $value) {
            $encodedArray[] = array_map('utf8_encode', $value);
        }

        return $encodedArray;
    }

    /**
     * Retorna registro conforme especificação de limite e por pagina
     * @param int $limit
     * @param int $countPerPage
     * @param null $order
     * @return array
     * @throws \Zend\Db\TableGateway\Exception\InvalidArgumentException
     */
    public function fetchAllPaginator($limit = 1, $countPerPage = 2, $order = null){

        if (! is_numeric($limit))
            throw new InvalidArgumentException('Invalid argument in $limit, expected an numeric');

        if (! is_numeric($countPerPage))
            throw new InvalidArgumentException('Invalid argument in $countPerPage, expected an numeric');

        if (!is_null($order))
            $order = "ORDER BY {$order}";

        $sql = "SELECT FIRST {$limit} SKIP {$countPerPage} {$this->table}.*  FROM {$this->table} {$order}";

        $statement = $this->adapter->createStatement($sql);
        $resultSet = new ResultSet;
        $result = $resultSet->initialize($statement->execute());

        //Converte os Valores para UTF-8
        $encodedArray = array();
        foreach ($result->toArray() as $value) {
            $encodedArray[] = array_map('utf8_encode', $value);
        }

        return $encodedArray;
    }

    /**
     * Retorna um registro especifico
     * @param array $param
     * @return array
     * @throws \Zend\Db\TableGateway\Exception\InvalidArgumentException
     */
    public function findBy(Array $param = array())
    {
        if (! is_array($param))
            throw new InvalidArgumentException("Invalid argument, expected an array");

        $key = array_keys($param);
        $val = array_values($param);

        $sql = "SELECT {$this->table}.* FROM {$this->table} WHERE {$this->table}.{$key[0]} = {$val[0]}";

        $statement = $this->adapter->createStatement($sql);
        $resultSet = new ResultSet;
        $result = $resultSet->initialize($statement->execute());

        //Converte os Valores para UTF-8
        $encodedArray = array();
        foreach ($result->toArray() as $value) {
            $encodedArray[] = array_map('utf8_encode', $value);
        }

        return $encodedArray;
    }

    /**
     * Salva ou altera registro, se for passado o código do registro se faz um UPDATE caso contrário se faz um INSERT
     * @param $class object
     * @return bool|int
     */
    abstract public function save($class);

    /**
     * Deleta um registro
     * @param array $param
     * @return int
     * @throws \Zend\Db\TableGateway\Exception\InvalidArgumentException
     */
    public function remove(Array $param = array())
    {
        if (! is_array($param))
            throw new InvalidArgumentException("Invalid argument, expected an array");

        return $this->delete($param);
    }

}