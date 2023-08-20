<?php

namespace Service;

use Exception;
use InvalidArgumentException;
use Repository\AcompanhamentoRepository;
use Util\ConstantesGenericasUtil;

class AcompanhamentoService
{


    public const TABELA = 'acompanhamento';
    public const RECURSOS_GET = ['listar', 'mensagem'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar'];

    private array $dados;

    private array $dadosCorpoRequest;

    private object $AcompanhamentoRepository;

    public function __construct($dados = [])
    {
        $this->dados = $dados;
        $this->AcompanhamentoRepository = new AcompanhamentoRepository();
    }

    public function validarGet(){
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_GET, true)) {

            if ($recurso === 'mensagem') {
                $retorno = $this->getMensagem();
            
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
        return $this->AcompanhamentoRepository->getMySQL()->getAll(self::TABELA);
    }
 
    
    private function getOneByKey()
    {
        return $this->AcompanhamentoRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    private function deletar(){
        return $this->AcompanhamentoRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    private function cadastrar() {

        $mensagem = $this->dadosCorpoRequest['mensagem'];

        $data = [
            'usuario_id' => $this->dadosCorpoRequest['usuario_id'],
            'usuario_acompanhamento' => $this->dadosCorpoRequest['usuario_acompanhamento'],
            'mensagem' => $this->dadosCorpoRequest['mensagem'],
            'chamado_id' => $this->dadosCorpoRequest['chamado_id'],
          
        ];

        if($mensagem){
            if (!$this->AcompanhamentoRepository->getMySQL()->getDb()->inTransaction()) {
               
                if ($this->AcompanhamentoRepository->insertUser( $data) > 0) {
                    $idInserido = $this->AcompanhamentoRepository->getMySQL()->getDb()->lastInsertId();
                    $this->AcompanhamentoRepository->getMySQL()->getDb()->commit();
                    return ['id_inserido' => $idInserido];
                }
            }
        }
      
        $this->AcompanhamentoRepository->getMySQL()->getDb()->rollBack();
        return ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO;

    }
    

    private function atualizar()
    {
        $mensagem = $this->dadosCorpoRequest['mensagem'];

        $data = [
            'usuario_acompanhamento' => $this->dadosCorpoRequest['usuario_acompanhamento'],
            'mensagem' => $this->dadosCorpoRequest['mensagem'],
            'chamado_id' => $this->dadosCorpoRequest['chamado_id'],
        ];

        if($mensagem){
            if (!$this->AcompanhamentoRepository->getMySQL()->getDb()->inTransaction()) {
                if ($this->AcompanhamentoRepository->updateUser($this->dados['id'],  $data) > 0) {
                    $this->AcompanhamentoRepository->getMySQL()->getDb()->commit();
                    return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;

                }
            }
        }

        
        $this->AcompanhamentoRepository->getMySQL()->getDb()->rollBack();
        return ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO;
    }
    
    public function getMensagem()
    {
        $id = $this->dados['id'];

        if ($id) {
            $consulMensagem = $this->AcompanhamentoRepository->consulMensagem($id);

            if ($consulMensagem) {
                return $consulMensagem;
                

            } else {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_INVALIDO);
            }
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
        }
    }
 
      
        
        

}