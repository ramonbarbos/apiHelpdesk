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
    
    public function checkExistingEnti( $ibge, $nome) {
        $consultaExistente = 'SELECT ibge, nome FROM ' . self::TABELA . ' WHERE ibge = :ibge OR nome = :nome';
        $stmtExistente = $this->MySQL->getDb()->prepare($consultaExistente);
        $stmtExistente->bindParam(':ibge', $ibge);
        $stmtExistente->bindParam(':nome', $nome);
        $stmtExistente->execute();

        $usuarioExistente = $stmtExistente->fetch(PDO::FETCH_ASSOC);
    
        return $usuarioExistente;
    }

    public function getMySQL()
    {
        return $this->MySQL;
    }
}