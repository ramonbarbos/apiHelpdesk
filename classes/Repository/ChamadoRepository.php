<?php

namespace Repository;

use DB\MySQL;
use PDO;

class ChamadoRepository
{
    private object $MySQL;
    public const TABELA = 'chamado';

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
    
  

    public function selectAberto() {
        $consulta = 'SELECT * FROM ' . self::TABELA . ' WHERE status = :status';
        $stmt = $this->MySQL->getDb()->prepare($consulta);
        $stmt->bindValue(':status', 'a');
    
        
            $stmt->execute();
            $chamadoAberto = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $chamadoAberto;
     
    }

    public function selectFechado() {
        $consulta = 'SELECT * FROM ' . self::TABELA . ' WHERE status = :status';
        $stmt = $this->MySQL->getDb()->prepare($consulta);
        $stmt->bindValue(':status', 'f');
    
        
            $stmt->execute();
            $chamadoAberto = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $chamadoAberto;
     
    }

    public function getMySQL()
    {
        return $this->MySQL;
    }
}