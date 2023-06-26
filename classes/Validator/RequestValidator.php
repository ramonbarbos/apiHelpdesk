<?php

namespace Validator;

use InvalidArgumentException;
use Repository\TokensAutorizadosRepository;
use Service\CategoriaService;
use Service\ClienteService;
use Service\EntidadeService;
use Service\UsuarioService;
use Util\ConstantesGenericasUtil;
use Util\JsonUtil;

class RequestValidator
{
    private array $request;
    private array $dadosRequest = [];
    private object $TokensAutorizadosRepository;

    const GET = 'GET';
    const DELETE = 'DELETE';
    const USUARIOS = 'USUARIOS';
    const ENTIDADE = 'ENTIDADE';
    const CATEGORIA = 'CATEGORIA';
    const CLIENTE = 'CLIENTE';

    public function __construct($request = [])
    {
        // Primeira coisa que irá fazer, vai ser o roteamento
        $this->request = $request;
        $this->TokensAutorizadosRepository = new TokensAutorizadosRepository();
    }

    public function processarRequest()
    {
        // Aqui iremos direcionar as requisições, caso seja GET, PUT, DELETE, POST
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);

        if (in_array($this->request['metodo'], ConstantesGenericasUtil::TIPO_REQUEST, true)) {
            $retorno = $this->direcionarRequest();
        }

        // Definir cabeçalhos CORS permitindo todas as origens
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
       

        return $retorno;
    }

    private function direcionarRequest()
    {
        // Validando o método requisitado
        // Caso não seja GET e DELETE, quer dizer que tem um BODY, ou seja, um POST ou PUT
        if ($this->request['metodo'] !== self::GET && $this->request['metodo'] !== self::DELETE) {
            $this->dadosRequest = JsonUtil::tratarCorpoRequisicaoJson();
        }

        // Responsável pelo TOKEN
        //$this->TokensAutorizadosRepository->validarToken(getallheaders()['Authorization']);
        $metodo = $this->request['metodo'];
        return $this->$metodo(); // Direcionando
    }

    private function get()
    {
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_GET)) {
            switch ($this->request['rota']) {
                case self::USUARIOS:
                    $UsuariosService = new UsuarioService($this->request);
                    $retorno = $UsuariosService->validarGet();
                    break;
                case self::ENTIDADE:
                    $EntidadeService = new EntidadeService($this->request);
                    $retorno = $EntidadeService->validarGet();
                    break;
                case self::CATEGORIA:
                    $CategoriaService = new CategoriaService($this->request);
                    $retorno = $CategoriaService->validarGet();
                    break; 
            case self::CLIENTE:
                    $ClienteService = new ClienteService($this->request);
                    $retorno = $ClienteService->validarGet();
                    break;   
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $retorno;
    }

    private function delete()
    {
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_DELETE)) {
            switch ($this->request['rota']) {
                case self::USUARIOS:
                    $UsuariosService = new UsuarioService($this->request);
                    $retorno = $UsuariosService->validarDelete();
                    break;
                case self::ENTIDADE:
                    $EntidadeService = new EntidadeService($this->request);
                    $retorno = $EntidadeService->validarDelete();
                    break;
                case self::CATEGORIA:
                    $CategoriaService = new CategoriaService($this->request);
                    $retorno = $CategoriaService->validarDelete();
                    break;
                case self::CLIENTE:
                    $ClienteService = new ClienteService($this->request);
                    $retorno = $ClienteService->validarDelete();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $retorno;
    }

    private function post()
    {
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_POST)) {
            switch ($this->request['rota']) {
                case self::USUARIOS:
                    $UsuariosService = new UsuarioService($this->request);
                    $UsuariosService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $UsuariosService->validarPost();
                    break;
                case self::ENTIDADE:
                    $EntidadeService = new EntidadeService($this->request);
                    $EntidadeService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $EntidadeService->validarPost();
                    break;
                case self::CATEGORIA:
                    $CategoriaService = new CategoriaService($this->request);
                    $CategoriaService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $CategoriaService->validarPost();
                    break;
                case self::CLIENTE:
                    $ClienteService = new ClienteService($this->request);
                    $ClienteService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $ClienteService->validarPost();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $retorno;
    }

    private function put()
    {
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_PUT)) {
            switch ($this->request['rota']) {
                case self::USUARIOS:
                    $UsuariosService = new UsuarioService($this->request);
                    $UsuariosService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $UsuariosService->validarPut();
                    break;
                case self::ENTIDADE:
                    $EntidadeService = new EntidadeService($this->request);
                    $EntidadeService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $EntidadeService->validarPut();
                    break;
                case self::CATEGORIA:
                    $CategoriaService = new CategoriaService($this->request);
                    $CategoriaService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $CategoriaService->validarPut();
                    break;
                 case self::CLIENTE:
                    $ClienteService = new ClienteService($this->request);
                    $ClienteService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $ClienteService->validarPut();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $retorno;
    }
}
