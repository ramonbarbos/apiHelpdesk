<?php

namespace Repository;

use DB\MySQL;

class UsuarioRepository
{
    private object $MySQL;
    public const TABELA = 'usuarios';

    /**
     * UsuariosRepository constructor.
     */
    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

    public function insertUser($dados){
        $consultaInsert = 'INSERT INTO ' . self::TABELA . ' (cpf, nome, sobrenome ,login,senha,ativo,cargo) VALUES (:cpf, :nome, :sobrenome, :login, :senha,:ativo,:cargo)';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);
        $stmt->bindParam(':cpf', $dados['cpf']);
        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':sobrenome', $dados['sobrenome']);
        $stmt->bindParam(':login', $dados['login']);
        $stmt->bindParam(':senha', $dados['senha']);
        $stmt->bindParam(':ativo', $dados['ativo']);
        $stmt->bindParam(':cargo', $dados['cargo']);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function updateUser($id, $dados){
        $consultaUpdate = 'UPDATE ' . self::TABELA . ' SET cpf = :cpf, nome = :nome, sobrenome = :sobrenome, login = :login ,senha = :senha,ativo = :ativo, cargo = :cargo WHERE id = :id';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':cpf', $dados['cpf']);
        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':sobrenome', $dados['sobrenome']);
        $stmt->bindParam(':login', $dados['login']);
        $stmt->bindParam(':senha', $dados['senha']);
        $stmt->bindParam(':ativo', $dados['ativo']);
        $stmt->bindParam(':cargo', $dados['cargo']);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function loginUser($login, $senha){
        $consulta = 'SELECT * FROM ' . self::TABELA . ' WHERE login = :login AND senha = :senha AND ativo = "s" ';
        $stmt = $this->MySQL->getDb()->prepare($consulta);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();
        
        return $stmt->fetch();

    }
    public function getMySQL()
    {
        return $this->MySQL;
    }
}