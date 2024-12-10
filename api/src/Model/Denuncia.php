<?php

namespace Model;

class Denuncia implements \JsonSerializable {
    private ?int $id_denuncias;
    private string $titulo;
    private string $descricao;
    private string $categoria; // Enum: 'agua', 'saneamento', 'obras', 'outros'
    private ?string $imagem;
    private ?string $localizacao;
    private string $status; // Enum: 'pendente', 'em andamento', 'resolvido'
    private bool $anonimo;
    private ?string $data_criacao; 
    private int $Usuarios_id_usuario;

    // Construtor
    public function __construct(
        string $titulo,
        string $descricao,
        string $categoria,
        string $status,
        int $Usuarios_id_usuario,
        ?bool $anonimo = false,
        ?string $imagem = null,
        ?string $localizacao = null,
        ?int $id_denuncias = null,
        ?string $data_criacao = null
    ) {
        $this->id_denuncias = $id_denuncias;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->categoria = $categoria;
        $this->status = $status;
        $this->Usuarios_id_usuario = $Usuarios_id_usuario;
        $this->anonimo = $anonimo ?? false;
        $this->imagem = $imagem;
        $this->localizacao = $localizacao;
        $this->data_criacao = $data_criacao;
    }

    // Métodos GET
    public function getIdDenuncias(): ?int {
        return $this->id_denuncias;
    }

    public function getTitulo(): string {
        return $this->titulo;
    }

    public function getDescricao(): string {
        return $this->descricao;
    }

    public function getCategoria(): string {
        return $this->categoria;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getUsuariosIdUsuario(): int {
        return $this->Usuarios_id_usuario;
    }

    public function isAnonimo(): bool {
        return $this->anonimo;
    }

    public function getImagem(): ?string {
        return $this->imagem;
    }

    public function getLocalizacao(): ?string {
        return $this->localizacao;
    }

    public function getDataCriacao(): ?string {
        return $this->data_criacao;
    }

    // Métodos SET
    public function setIdDenuncias(?int $id_denuncias): void {
        $this->id_denuncias = $id_denuncias;
    }

    public function setTitulo(string $titulo): void {
        $this->titulo = $titulo;
    }

    public function setDescricao(string $descricao): void {
        $this->descricao = $descricao;
    }

    public function setCategoria(string $categoria): void {
        $this->categoria = $categoria;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
    }

    public function setUsuariosIdUsuario(int $Usuarios_id_usuario): void {
        $this->Usuarios_id_usuario = $Usuarios_id_usuario;
    }

    public function setAnonimo(bool $anonimo): void {
        $this->anonimo = $anonimo;
    }

    public function setImagem(?string $imagem): void {
        $this->imagem = $imagem;
    }

    public function setLocalizacao(?string $localizacao): void {
        $this->localizacao = $localizacao;
    }

    public function setDataCriacao(?string $data_criacao): void {
        $this->data_criacao = $data_criacao;
    }

    // Implementação da interface JsonSerializable
    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}
