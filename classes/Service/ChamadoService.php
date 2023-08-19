<?php

namespace Service;

use Exception;
use InvalidArgumentException;
use Repository\ChamadoRepository;
use Util\ConstantesGenericasUtil;

class ChamadoService
{


    public const TABELA = 'chamado';
    public const RECURSOS_GET = ['listar', 'aberto', 'fechado'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar', 'upstatus'];

    private array $dados;

    private array $dadosCorpoRequest;

    private object $ChamadoRepository;

    public function __construct($dados = [])
    {
        $this->dados = $dados;
        $this->ChamadoRepository = new ChamadoRepository();
    }

    public function validarGet(){
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_GET, true)) {

       
            if ($recurso === 'aberto') {
                $retorno = $this->getAberto();
            
            }else if($recurso === 'fechado') {
                $retorno = $this->getFechado();
                
            }else {
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

                if ($recurso === 'upstatus') {
                    $retorno = $this->putStatus();
                
                }else{
                    $retorno = $this->$recurso();

                }
                    
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
        return $this->ChamadoRepository->getMySQL()->getAll(self::TABELA);
    }
    private function getAberto(){
        return  $this->ChamadoRepository->selectAberto();
    }
    private function getFechado(){
        return  $this->ChamadoRepository->selectFechado();
    }
    
    private function getOneByKey()
    {
        return $this->ChamadoRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    private function deletar(){
        return $this->ChamadoRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    private function cadastrar() {

        $titulo = $this->dadosCorpoRequest['titulo'];

        $data = [
            'categoria' => $this->dadosCorpoRequest['categoria'],
            'id_usuario' => $this->dadosCorpoRequest['id_usuario'],
            'nome_usuario' => $this->dadosCorpoRequest['nome_usuario'],
            'id_cliente' => $this->dadosCorpoRequest['id_cliente'],
            'nome_cliente' => $this->dadosCorpoRequest['nome_cliente'],
            'status' => $this->dadosCorpoRequest['status'],
            'entidade' => $this->dadosCorpoRequest['entidade'],
            'titulo' => $this->dadosCorpoRequest['titulo'],
            'descricao' => $this->dadosCorpoRequest['descricao'],
            'acompanhamento' => $this->dadosCorpoRequest['acompanhamento'],
        ];

        if($titulo){
            if (!$this->ChamadoRepository->getMySQL()->getDb()->inTransaction()) {
               
                if ($this->ChamadoRepository->insertUser( $data) > 0) {
                    $idInserido = $this->ChamadoRepository->getMySQL()->getDb()->lastInsertId();
                    $this->ChamadoRepository->getMySQL()->getDb()->commit();
                    return ['id_inserido' => $idInserido];
                }
            }
        }
      
        $this->ChamadoRepository->getMySQL()->getDb()->rollBack();
        return ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO;

    }
    

    private function atualizar()
    {
        $titulo = $this->dadosCorpoRequest['titulo'];

        $data = [
            'categoria' => $this->dadosCorpoRequest['categoria'],
            'id_usuario' => $this->dadosCorpoRequest['id_usuario'],
            'nome_usuario' => $this->dadosCorpoRequest['nome_usuario'],
            'id_cliente' => $this->dadosCorpoRequest['id_cliente'],
            'nome_cliente' => $this->dadosCorpoRequest['nome_cliente'],
            'status' => $this->dadosCorpoRequest['status'],
            'entidade' => $this->dadosCorpoRequest['entidade'],
            'titulo' => $this->dadosCorpoRequest['titulo'],
            'descricao' => $this->dadosCorpoRequest['descricao'],
            'acompanhamento' => $this->dadosCorpoRequest['acompanhamento'],
        ];

        if($titulo){
            if (!$this->ChamadoRepository->getMySQL()->getDb()->inTransaction()) {
                if ($this->ChamadoRepository->updateUser($this->dados['id'],  $data) > 0) {
                    $this->ChamadoRepository->getMySQL()->getDb()->commit();
                    return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;

                }
            }
        }

        
        $this->ChamadoRepository->getMySQL()->getDb()->rollBack();
        return ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO;
    }
    
    private function putStatus()
    {
        $status = $this->dadosCorpoRequest['status'];

       

        $data = [
            'status' => $this->dadosCorpoRequest['status'],
        ];

        if($status){
            if (!$this->ChamadoRepository->getMySQL()->getDb()->inTransaction()) {
                if ($this->ChamadoRepository->updateStatus($this->dados['id'],  $data) > 0) {
                    $this->ChamadoRepository->getMySQL()->getDb()->commit();
                    return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;

                }
            }
        }

        
        $this->ChamadoRepository->getMySQL()->getDb()->rollBack();
        return ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO;
    }
 
      
        
        

}