<?php

namespace Service;

use Exception;
use InvalidArgumentException;
use Repository\CategoriaRepository;
use Util\ConstantesGenericasUtil;

class CategoriaService
{


    public const TABELA = 'categoria';
    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar'];
    public const RECURSOS_LOGIN = ['login'];

    private array $dados;

    private array $dadosCorpoRequest;

    private object $CategoriaRepository;

    public function __construct($dados = [])
    {
        $this->dados = $dados;
        $this->CategoriaRepository = new CategoriaRepository();
    }

    public function validarGet(){
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_GET, true)) {

       
                $retorno = $this->dados['id'] > 0 ? $this->getOneByKey() : $this->$recurso();

           

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
        return $this->CategoriaRepository->getMySQL()->getAll(self::TABELA);
    }

    
    private function getOneByKey()
    {
        return $this->CategoriaRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    private function deletar(){
        return $this->CategoriaRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    private function cadastrar() {

        $nome = $this->dadosCorpoRequest['nome'];


        $entidadeExistente = $this->CategoriaRepository->checkExistingCat($nome);
        if ( $entidadeExistente['nome'] == $nome) {
            return ['Nenhum registro afetado!'];
        }

            if($this->CategoriaRepository->insertUser( $this->dadosCorpoRequest) > 0){

                $idIserido = $this->CategoriaRepository->getMySQL()->getDb()->lastInsertId();
                $this->CategoriaRepository->getMySQL()->getDb()->commit();
                return ['id_inserido' => $idIserido];

            }
            $this->CategoriaRepository->getMySQL()->getDb()->rollback();
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);


    }
    

    private function atualizar()
    {
       
    
        if ($this->CategoriaRepository->updateUser($this->dados['id'], $this->dadosCorpoRequest) > 0) {
            $this->CategoriaRepository->getMySQL()->getDb()->commit();
            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }
    
        $this->CategoriaRepository->getMySQL()->getDb()->rollBack();
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }
    

 
      
        
        

}