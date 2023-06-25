<?php

namespace Service;

use Exception;
use InvalidArgumentException;
use Repository\EntidadeRepository;
use Util\ConstantesGenericasUtil;

class EntidadeService
{


    public const TABELA = 'entidade';
    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar'];
    public const RECURSOS_LOGIN = ['login'];

    private array $dados;

    private array $dadosCorpoRequest;

    private object $EntidadeRepository;

    public function __construct($dados = [])
    {
        $this->dados = $dados;
        $this->EntidadeRepository = new EntidadeRepository();
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
        return $this->EntidadeRepository->getMySQL()->getAll(self::TABELA);
    }

    
    private function getOneByKey()
    {
        return $this->EntidadeRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    private function deletar(){
        return $this->EntidadeRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    private function cadastrar() {
      
            if($this->EntidadeRepository->insertUser( $this->dadosCorpoRequest) > 0){

                $idIserido = $this->EntidadeRepository->getMySQL()->getDb()->lastInsertId();
                $this->EntidadeRepository->getMySQL()->getDb()->commit();
                return ['id_inserido' => $idIserido];

            }
            $this->EntidadeRepository->getMySQL()->getDb()->rollback();
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);


    }
    

    private function atualizar()
    {
        $payload = file_get_contents('php://input');
    
        // Decodifica o JSON para obter os dados
        $dados = json_decode($payload, true);
    
        // Verifica se o JSON foi decodificado com sucesso
        if ($dados === null) {
            // Erro na decodificação do JSON
            return ['Erro ao decodificar o JSON'];
        }
    
        // Acessa o valor do campo "ibge" no JSON
        $ibge = $dados['ibge'];
        $nome = $this->dadosCorpoRequest['nome'];
    
        // Verifica o tamanho do campo "ibge"
        if (strlen($ibge) > 7) {
            return ['Limite de dígitos do IBGE excedido'];
        }
    
        $entidadeExistente = $this->EntidadeRepository->checkExistingEnti($ibge);
        if ($entidadeExistente['ibge'] == $ibge || $entidadeExistente['nome'] == $nome) {
            return ['Nenhum registro afetado!'];
        }
    
        if ($this->EntidadeRepository->updateUser($this->dados['id'], $dados) > 0) {
            $this->EntidadeRepository->getMySQL()->getDb()->commit();
            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }
    
        $this->EntidadeRepository->getMySQL()->getDb()->rollBack();
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }
    

 
      
        
        

}