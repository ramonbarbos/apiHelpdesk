<?php

namespace Repository;

use DB\MySQL;
use PDO;

class ClienteRepository
{
    private object $MySQL;
    public const TABELA = 'cliente';

    /**
     * UsuariosRepository constructor.
     */
    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

    
    public function insertUser($dados){
        $campos = array('cpf', 'nome', 'sobrenome', 'entidade');
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

    public function checkExistingUser($cpf) {
        $consultaExistente = 'SELECT COUNT(*) FROM ' . self::TABELA . ' WHERE cpf = :cpf';
        $stmtExistente = $this->MySQL->getDb()->prepare($consultaExistente);
        $stmtExistente->bindParam(':cpf', $cpf);
        $stmtExistente->execute();
    
        $existe = $stmtExistente->fetchColumn();
    
        return $existe > 0;
    }

    
    public function updateUser($id, $dados)
    {
        $campos = array('cpf', 'nome', 'sobrenome', 'entidade');
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
    public function updateUserNoCpf($id, $dados)
    {
        $campos = array( 'nome', 'sobrenome', 'entidade');
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

    public function checkExistingUserUp($cpf) {
        $consultaExistente = 'SELECT cpf FROM ' . self::TABELA . ' WHERE cpf = :cpf';
        $stmtExistente = $this->MySQL->getDb()->prepare($consultaExistente);
        $stmtExistente->bindParam(':cpf', $cpf);
        $stmtExistente->execute();
    
        $clienteExistente = $stmtExistente->fetch(PDO::FETCH_ASSOC);
    
        return $clienteExistente;
    }

    
    
    public function consulEntidade($id){
        $consulta = 'SELECT entidade FROM ' . self::TABELA . ' WHERE id = :id ';
        $stmt = $this->MySQL->getDb()->prepare($consulta);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();

    }

    public function getMySQL()
    {
        return $this->MySQL;
    }
}