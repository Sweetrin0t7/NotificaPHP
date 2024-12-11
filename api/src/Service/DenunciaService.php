<?php

namespace Service;

use Error\APIException;
use Model\Denuncia;
use Repository\DenunciaRepository;

class DenunciaService
{
    private DenunciaRepository $repository;

    function __construct()
    {
        $this->repository = new DenunciaRepository();
    }

    function getDenuncias(?string $titulo): array
    {
        if ($titulo) {
            if ($titulo === "") throw new APIException("Invalid search parameter!", 400);
            return $this->repository->findByTitulo($titulo);
        } else {
            return $this->repository->findAll();
        }
    }

    function getDenunciaById(int $id): Denuncia
    {
        $denuncia = $this->repository->findById($id);
        if (!$denuncia) throw new APIException("Denuncia not found!", 404);
        return $denuncia;
    }

    function createNewDenuncia(string $titulo, string $descricao, string $categoria, string $status, int $Usuarios_id_usuario, bool $anonimo = false, ?string $imagem = null, ?string $localizacao = null): Denuncia
    {
        $denuncia = new Denuncia(
            titulo: trim($titulo),
            descricao: trim($descricao),
            categoria: trim($categoria),
            status: trim($status),
            Usuarios_id_usuario: $Usuarios_id_usuario,
            anonimo: $anonimo,
            imagem: $imagem,
            localizacao: $localizacao
        );
        $this->validateDenuncia($denuncia);
        return $this->repository->create($denuncia);
    }

    function updateDenuncia(int $id, string $titulo, string $descricao, string $categoria, string $status, int $Usuarios_id_usuario, bool $anonimo = false, ?string $imagem = null, ?string $localizacao = null): Denuncia
    {
        $denuncia = $this->getDenunciaById($id);
        $denuncia->setTitulo($titulo);
        $denuncia->setDescricao($descricao);
        $denuncia->setCategoria($categoria);
        $denuncia->setStatus($status);
        $denuncia->setUsuariosIdUsuario($Usuarios_id_usuario);
        $denuncia->setAnonimo($anonimo);
        $denuncia->setImagem($imagem);
        $denuncia->setLocalizacao($localizacao);
        $this->validateDenuncia($denuncia);

        $this->repository->update($denuncia);
        return $denuncia;
    }

    function deleteDenuncia(int $id): void
    {
        $this->repository->delete($id);
    }

    private function validateDenuncia(Denuncia $denuncia)
    {
        if (strlen($denuncia->getTitulo()) < 5) throw new APIException("Título inválido!", 400);
        if (strlen($denuncia->getDescricao()) < 10) throw new APIException("Descricao deve ter no mínimo 10 caracteres!", 400);
        if (strlen($denuncia->getCategoria()) < 3) throw new APIException("Categoria deve ter no mínimo 3 caracteres!", 400);
        if (!in_array($denuncia->getStatus(), ["Pendente", "Em andamento", "Resolvido"])) {
            throw new APIException("Status inválido! Valores aceitos: 'Pendente', 'Em andamento', 'Resolvido'.", 400);
        }
    }
}
