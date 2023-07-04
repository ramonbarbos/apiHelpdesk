<?php

namespace Service;

use Exception;
use InvalidArgumentException;
use Repository\ClienteRepository;
use Util\ConstantesGenericasUtil;

class ClienteService
{


    public const TABELA = 'cliente';
    public const RECURSOS_GET = ['listar', 'entidade'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar', 'upimagem'];
    public const RECURSOS_LOGIN = ['login'];

    private array $dados;

    private array $dadosCorpoRequest;

    private object $ClienteRepository;

    public function __construct($dados = [])
    {
        $this->dados = $dados;
        $this->ClienteRepository = new ClienteRepository();
    }

    public function validarGet(){
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_GET, true)) {

            if ($recurso === 'entidade') {
                $retorno = $this->getEntidade();
            
            }else  {
                $retorno = $this->dados['id'] > 0 ? $this->getOneByKey() : $this->$recurso();

            }
           

        } else{
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);


        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

   
    public function validarDelete(){
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_DELETE, true)) {
            if($this->dados['id'] > 0){
                $retorno = $this->$recurso();
            }
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

    public function validarPost(){
        $retorno = null;
        $recurso = $this->dados['recurso'];

        if (in_array($recurso, self::RECURSOS_POST, true)) {
            $retorno = $this->$recurso();
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

    public function validarPut()
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_PUT, true)) {
            if ($this->dados['id'] > 0) {
               
                    
                        $retorno = $this->$recurso();
                    
            } else {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
            }



        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

    public function setDadosCorpoRequest($dadosCorpoRequest){
        $this->dadosCorpoRequest = $dadosCorpoRequest;
    }

    private function listar(){
        return $this->ClienteRepository->getMySQL()->getAll(self::TABELA);
    }

    
    private function getOneByKey()
    {
        return $this->ClienteRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    private function deletar(){
        return $this->ClienteRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    private function cadastrar() {
        $cpf = $this->dadosCorpoRequest['cpf'];
    
        if ($cpf) {

            $cpf = $this->dadosCorpoRequest['cpf'];


            if ($this->ClienteRepository->checkExistingUser($cpf)) {
                return ['Existente'];
            }
    
                if ($this->ClienteRepository->insertUser($this->dadosCorpoRequest) > 0) {
                    $idInserido = $this->ClienteRepository->getMySQL()->getDb()->lastInsertId();
                    $this->ClienteRepository->getMySQL()->getDb()->commit();
                    return ['id_inserido' => $idInserido];
                }
    
                $this->ClienteRepository->getMySQL()->getDb()->rollback();
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
           
        }
    
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
    }
    

    private function atualizar()
    {
        $cpf = $this->dadosCorpoRequest['cpf'];
    
        // Verificar se o usuário já existe
        $clienteExistente = $this->ClienteRepository->checkExistingUserUp($cpf);
       
        switch (true) {
            case $clienteExistente['cpf'] === $cpf :

                $data = [
                    'cpf' => $this->dadosCorpoRequest['cpf'],
                    'nome' => $this->dadosCorpoRequest['nome'],
                    'sobrenome' => $this->dadosCorpoRequest['sobrenome'],
                    'entidade' => $this->dadosCorpoRequest['entidade'],
                ];

                if (!$this->ClienteRepository->getMySQL()->getDb()->inTransaction()) {
                    if ($this->ClienteRepository->updateUser($this->dados['id'],  $data) > 0) {
                        $this->ClienteRepository->getMySQL()->getDb()->commit();
                        return  ['CPF ja existente!'];
                    }
                }
                break;    
            default:
                if ($this->ClienteRepository->updateUser($this->dados['id'], $this->dadosCorpoRequest) > 0) {
                    $this->ClienteRepository->getMySQL()->getDb()->commit();
                    return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
                }
        
                break;
        }
        

      
        $this->ClienteRepository->getMySQL()->getDb()->rollBack();
        return ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO;
    }

        public function getEntidade()
        {
            $id = $this->dados['id'];

            if ($id) {
                $consulEntidade = $this->ClienteRepository->consulEntidade($id);

                if ($consulEntidade) {
                    return ['entidade' => $consulEntidade['entidade']];
                    

                } else {
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_INVALIDO);
                }
            } else {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
            }
        }
        
    

      
        
        

}