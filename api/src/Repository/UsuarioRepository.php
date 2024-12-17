<?php

namespace Repository;

use Database\Database;
use Error\APIException;
USE \PDO;
use Model\Usuario;

class UsuarioRepository {
    private $connection;

    public function __construct() {
        $this->connection = Database::getConnection();
    }

    public function findAll(): array {
        $stmt = $this->connection->prepare("SELECT * FROM usuarios");
        $stmt->execute();

        $usuarios = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $usuario = new Usuario(
                Usuario_id_usuario: $row['id_usuario'],
                cpf_usuario: $row['cpf_usuario'],
                nome_usuario: $row['nome_usuario'],
                telefone: $row['telefone'],
                senha: $row['senha'],
                data_cadastro: $row['data_cadastro'],
                tipo_usuario: $row['tipo_usuario']
            );
            $usuarios[] = $usuario;
        }

        return $usuarios;
    }

    public function findById(int $id_usuario): ?Usuario {
        $stmt = $this->connection->prepare("SELECT * FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->bindValue(':id_usuario', $id_usuario, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Usuario(
            Usuario_id_usuario: $row['id_usuario'],
            cpf_usuario: $row['cpf_usuario'],
            nome_usuario: $row['nome_usuario'],
            telefone: $row['telefone'],
            senha: $row['senha'],
            data_cadastro: $row['data_cadastro'],
            tipo_usuario: $row['tipo_usuario']
        );
    }

    public function findByNome(string $nome): array {
        $stmt = $this->connection->prepare("SELECT * FROM usuarios WHERE nome_usuario LIKE :nome");
        $stmt->bindValue(':nome', '%' . $nome . '%', \PDO::PARAM_STR);
        $stmt->execute();
    
        $usuarios = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $usuario = new Usuario(
                Usuario_id_usuario: $row['id_usuario'],
                cpf_usuario: $row['cpf_usuario'],
                nome_usuario: $row['nome_usuario'],
                telefone: $row['telefone'],
                senha: $row['senha'],
                data_cadastro: $row['data_cadastro'],
                tipo_usuario: $row['tipo_usuario']
            );
            $usuarios[] = $usuario;
        }
    
        return $usuarios;
    }

    public function findByCpf(string $cpf_usuario): ?Usuario {
        $stmt = $this->connection->prepare("SELECT * FROM usuarios WHERE cpf_usuario = :cpf_usuario");
        $stmt->bindValue(':cpf_usuario', $cpf_usuario, \PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Usuario(
            Usuario_id_usuario: $row['id_usuario'],
            cpf_usuario: $row['cpf_usuario'],
            nome_usuario: $row['nome_usuario'],
            telefone: $row['telefone'],
            senha: $row['senha'],
            data_cadastro: $row['data_cadastro'],
            tipo_usuario: $row['tipo_usuario']
        );
    }

    public function create(Usuario $usuario): Usuario {
        $stmt = $this->connection->prepare(
            "INSERT INTO usuarios (cpf_usuario, nome_usuario, telefone, senha, tipo_usuario) 
                    VALUES (:cpf_usuario, :nome_usuario, :telefone, :senha, :tipo_usuario)"
        );
        $stmt->bindValue(':cpf_usuario', $usuario->getCpfUsuario(), \PDO::PARAM_STR);
        $stmt->bindValue(':nome_usuario', $usuario->getNomeUsuario(), \PDO::PARAM_STR);
        $stmt->bindValue(':telefone', $usuario->getTelefone(), \PDO::PARAM_STR);
        $stmt->bindValue(':senha', $usuario->getSenha(), \PDO::PARAM_STR);
        $stmt->bindValue(':tipo_usuario', $usuario->getTipoUsuario(), \PDO::PARAM_STR);
        $stmt->execute();

        $usuario->setIdUsuario((int) $this->connection->lastInsertId());
        return $usuario;
    }

    public function update(Usuario $usuario): void {
        $stmt = $this->connection->prepare("UPDATE usuarios SET 
                                            cpf_usuario = :cpf_usuario,
                                            nome_usuario = :nome_usuario,
                                            telefone = :telefone,
                                            senha = :senha,
                                            tipo_usuario = :tipo_usuario
                                            WHERE id_usuario = :id_usuario"
        );
        $stmt->bindValue(':id_usuario', $usuario->getIdUsuario(), PDO::PARAM_INT);
        $stmt->bindValue(':cpf_usuario', $usuario->getCpfUsuario(), PDO::PARAM_STR);
        $stmt->bindValue(':nome_usuario', $usuario->getNomeUsuario(), PDO::PARAM_STR);
        $stmt->bindValue(':telefone', $usuario->getTelefone(), PDO::PARAM_STR);
        $stmt->bindValue(':senha', $usuario->getSenha(), PDO::PARAM_STR);
        $stmt->bindValue(':tipo_usuario', $usuario->getTipoUsuario(), PDO::PARAM_STR);
        $stmt->execute();
    }

    public function delete(int $id_usuario): void {
        $stmt = $this->connection->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->bindValue(':id_usuario', $id_usuario, \PDO::PARAM_INT);
        $stmt->execute();
    }
}
