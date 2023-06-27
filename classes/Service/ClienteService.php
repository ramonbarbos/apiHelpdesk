<?php

namespace Service;

use Exception;
use InvalidArgumentException;
use Repository\ClienteRepository;
use Util\ConstantesGenericasUtil;

class ClienteService
{


    public const TABELA = 'cliente';
    public const RECURSOS_GET = ['listar', 'fotoperfil'];
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

            if ($recurso === 'fotoperfil') {
                $retorno = $this->getImage();
            
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

        if (in_array($recurso, self::RECURSOS_LOGIN, true) ) {
            $retorno = $this->validarLogin();

        } elseif (in_array($recurso, self::RECURSOS_POST, true)) {
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
               
                    if ($recurso === 'upimagem') {
                        $retorno = $this->upImage();
                    
                    }else  {
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
        if ($clienteExistente['cpf'] === $cpf  ) {


            if ($this->ClienteRepository->updateUserNoCpf($this->dados['id'], $this->dadosCorpoRequest) > 0) {
                $this->ClienteRepository->getMySQL()->getDb()->commit();
                return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
            }
        }

        if ($this->ClienteRepository->updateUser($this->dados['id'], $this->dadosCorpoRequest) > 0) {
            $this->ClienteRepository->getMySQL()->getDb()->commit();
            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }
        $this->ClienteRepository->getMySQL()->getDb()->rollBack();
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }

    public function validarLogin()
     {
            $login = $this->dadosCorpoRequest['login'];
            $senha = $this->dadosCorpoRequest['senha'];

            if ($login && $senha) {
                $usuario = $this->ClienteRepository->loginUser($login, $senha);

                if ($usuario) {
                    // Login válido, prosseguir com o restante do código ou retornar uma resposta adequada
                    $idIserido = $this->ClienteRepository->getMySQL()->getDb()->lastInsertId();
                    return ['mensagem' => 'Login válido','logado' => $usuario['id']];
                } else {
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_INVALIDO);
                }
            } else {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
            }
        }

        public function getImage()
        {
            $id = $this->dados['id'];
        
            if ($id) {
                $usuario = $this->ClienteRepository->getMySQL()->getOneByKey(self::TABELA, $id);
        
                if ($usuario && $usuario['imagem']) {
                    // Defina o caminho completo do diretório de uploads
                    $diretorioUpload = './classes/Uploads/';
        
                    // Verifique se o diretório de uploads existe e tem permissões de leitura
                    if (!is_dir($diretorioUpload) || !is_readable($diretorioUpload)) {
                        throw new Exception('O diretório de uploads não existe ou não tem permissões de leitura.');
                    }
        
                    $caminhoImagem = $diretorioUpload . $usuario['imagem'];
        
                    if (file_exists($caminhoImagem)) {
                        // Defina os cabeçalhos CORS adequados
                        header('Access-Control-Allow-Origin: *');
                        header('Content-Length: ' . filesize($caminhoImagem));
        
                        // Retorne o conteúdo binário da imagem
                        readfile($caminhoImagem);
                        exit;
                    } else {
                        throw new InvalidArgumentException('A imagem não foi encontrada.');
                    }
                } else {
                    throw new InvalidArgumentException('O usuário não foi encontrado ou não possui uma imagem.');
                }
            } else {
                throw new InvalidArgumentException('O ID do usuário é obrigatório.');
            }
        }
        
    

        private function upImage()
        {
            $id = $this->dados['id'];
            $imagem = $this->dadosCorpoRequest['imagem'];
        
            if ($id && $imagem) {
                // Decodifique a string base64 para obter o conteúdo binário da imagem
                $imageContent = base64_decode($imagem);
        
                // Gere um nome único para o arquivo
                $nomeArquivo = uniqid() . '.jpg';
        
                // Defina o caminho completo do diretório de uploads
                $diretorioUpload = './classes/Uploads/';
        
                // Verifique se o diretório de uploads existe e tem permissões de escrita
                if (!is_dir($diretorioUpload) || !is_writable($diretorioUpload)) {
                    throw new Exception('O diretório de uploads não existe ou não tem permissões de escrita.');
                }
        
                // Salve o conteúdo binário da imagem no arquivo
                if (file_put_contents($diretorioUpload . $nomeArquivo, $imageContent) !== false) {
                    // Aqui você pode salvar os detalhes da imagem no banco de dados
        
                    if ($this->ClienteRepository->updateImage($id, $nomeArquivo) > 0) {
                        $this->ClienteRepository->getMySQL()->getDb()->commit();
                        return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
                    } else {
                        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
                    }
                } else {
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
                }
            }
        }
        
        

}