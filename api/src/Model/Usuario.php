<?php 
namespace Model;

class Usuario implements \JsonSerializable {

    private ?int $id_usuario;
    private string $cpf_usuario;

    private string $nome_usuario;
    private string $telefone;
    private string $senha;
    private ?string $data_cadastro;
    private string $tipo_usuario; // Enum: 'comum', 'admin'

    public function __construct(
        ?int $id_usuario = null,
        string $cpf_usuario,
        string $nome_usuario,
        string $telefone,
        string $senha,
        string $data_cadastro = null,
        string $tipo_usuario
    ){
        $this->id_usuario = $id_usuario;
        $this->cpf_usuario = $cpf_usuario;
        $this->nome_usuario = $nome_usuario;
        $this->telefone = $telefone;
        $this->senha = $senha;
        $this->data_cadastro = $data_cadastro;
        $this->tipo_usuario = $tipo_usuario;
    }

    public function getIdUsuario() : int { 
        return $this->id_usuario;
    }

    public function getCpfUsuario() : string {
        return $this->cpf_usuario;
    }

    public function getNomeUsuario() : string {
        return $this->nome_usuario;
    }

    public function getTelefone() : string {
        return $this->telefone;
    }

    public function getSenha() : string {
        return $this->senha;
    }

    public function getDataCadastro() : ?string {
        return $this->data_cadastro;
    }

    public function getTipoUsuario() : string {
        return $this->tipo_usuario;
    }

    public function setIdUsuario(int $id_usuario) : void {
        $this->id_usuario = $id_usuario;
    }

    public function setCpfUsuario(string $cpf_usuario) : void {
        $this->cpf_usuario = $cpf_usuario;
    }

    public function setNomeUsuario(string $nome_usuario) : void {
        $this->nome_usuario = $nome_usuario;
    }

    public function setTelefone(string $telefone) : void {
        $this->telefone = $telefone;
    }

    public function setSenha(string $senha) : void {
        $this->senha = $senha;
    }

    public function setDataCadastro(?string $data_cadastro) : void {
        $this->data_cadastro = $data_cadastro;
    }

    public function setTipoUsuario(string $tipo_usuario) : void {
        $this->tipo_usuario = $tipo_usuario;
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }

}