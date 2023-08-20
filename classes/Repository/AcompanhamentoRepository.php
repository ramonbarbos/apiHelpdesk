<?php

namespace Repository;

use DB\MySQL;
use PDO;
use PDOException;
use Util\ConstantesGenericasUtil;

class AcompanhamentoRepository
{
    private object $MySQL;
    public const TABELA = 'acompanhamento';

    /**
     * UsuariosRepository constructor.
     */
    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

    public function insertUser($dados){
        $campos = array_keys($dados);
        $valores = array();
    
        $consultaInsert = 'INSERT INTO ' . self::TABELA . ' (' . implode(',', $campos) . ') VALUES (:' . implode(',:', $campos) . ')';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);
    
        foreach ($campos as $campo) {
            $valores[':' . $campo] = $dados[$campo];
        }
    
        $stmt->execute($valores);
        return $stmt->rowCount();
    }
    
    public function updateUser($id, $dados)
    {
        $campos = array_keys($dados);
        $valores = array();
        $consultaUpdate = 'UPDATE ' . self::TABELA . ' SET ';
        
        foreach ($campos as $campo) {
            $consultaUpdate .= $campo . ' = :' . $campo . ', ';
            $valores[':' . $campo] = $dados[$campo];
        }
        
        $consultaUpdate = rtrim($consultaUpdate, ', ') . ' WHERE id = :id';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        
        foreach ($valores as $campo => $valor) {
            $stmt->bindValue($campo, $valor);
        }
        
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount();
    }
    
    public function consulMensagem($id){
        $consulta = 'SELECT * FROM ' . self::TABELA . ' WHERE chamado_id = :id ';
        $stmt = $this->MySQL->getDb()->prepare($consulta);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetchAll();

    }

  

    public function getMySQL()
    {
        return $this->MySQL;
    }
}