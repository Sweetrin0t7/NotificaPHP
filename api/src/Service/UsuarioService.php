<?php

namespace Service;

use Error\APIException;
use Model\Usuario;
use Repository\UsuarioRepository;

class UsuarioService
{
    private UsuarioRepository $repository;

    function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

// Método na Service
function getUsuarios(?string $nome_usuario): array
{
    if ($nome_usuario) {
        if ($nome_usuario === "") throw new APIException("Parametros de busca inválidos!", 400);
        $usuarios = $this->repository->findByNome($nome_usuario);
        return is_array($usuarios) ? $usuarios : [$usuarios]; 
    } else {
        return $this->repository->findAll();
    }
}


    function getUsuarioById(int $id): Usuario
    {
        $usuario = $this->repository->findById($id);
        if (!$usuario) throw new APIException("Usuario não encontrado!", 404);
        return $usuario;
    }

    function createNewUsuario(string $cpf_usuario, string $nome_usuario, string $telefone, string $senha): Usuario
    {
        $usuario = new Usuario(
            Usuario_id_usuario: null, // ID inicial ta null por enquanto
            cpf_usuario: $cpf_usuario,
            nome_usuario: trim($nome_usuario),
            telefone: $telefone,
            senha: password_hash(trim($senha), PASSWORD_BCRYPT),
            data_cadastro: null,
            tipo_usuario: 'comum' // Tipo padrão é o 'comun'
        );

        $this->validateUsuario($usuario);
        return $this->repository->create($usuario);
    }

    function updateUsuario(int $id, string $nome_usuario, ?string $senha = null, ?string $telefone = null): Usuario
    {
        $usuario = $this->getUsuarioById($id);
        $usuario->setNomeUsuario(trim($nome_usuario));
        if ($senha) {
            $usuario->setSenha(password_hash(trim($senha), PASSWORD_BCRYPT));
        }
        $usuario->setTelefone($telefone);

        $this->validateUsuario($usuario);
        $this->repository->update($usuario);
        return $usuario;
    }

    function deleteUsuario(int $id): void
    {
        $this->repository->delete($id);
    }

    private function validateUsuario(Usuario $usuario)
    {
        if (strlen($usuario->getNomeUsuario()) < 3) {
            throw new APIException("Nome deve ter no mínimo 3 caracteres!", 400);
        }
        if (!empty($usuario->getSenha()) && strlen($usuario->getSenha()) < 6) {
            throw new APIException("Senha deve ter no mínimo 6 caracteres!", 400);
        }
        if (!preg_match('/^\d{11}$/', $usuario->getCpfUsuario())) {
            throw new APIException("CPF deve ter 11 números!", 400);
        }
        if (!preg_match('/^\d{15}$/', $usuario->getTelefone())) {
            throw new APIException("Telefone deve ter 15 números!, incluindo o DDD", 400);
        }
        $tiposValidos = ['comum', 'admin'];
        if (!in_array($usuario->getTipoUsuario(), $tiposValidos)) {
            throw new APIException("Tipo de usuário inválido!", 400);
        }
    }
}
