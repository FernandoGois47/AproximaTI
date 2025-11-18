<?php

/**
 * Classe base abstrata para representar um Usuário no sistema
 */
abstract class User {
    protected $id;
    protected $nome;
    protected $email;
    protected $senha;
    protected $tipo;
    protected $telefone;
    protected $cidade;
    protected $estado;
    protected $foto_perfil;
    protected $data_cadastro;

    /**
     * Construtor da classe User
     * @param array $dados Dados do usuário vindos do banco de dados
     */
    public function __construct(array $dados = []) {
        if (!empty($dados)) {
            $this->id = $dados['id'] ?? null;
            $this->nome = $dados['nome'] ?? '';
            $this->email = $dados['email'] ?? '';
            $this->senha = $dados['senha'] ?? '';
            $this->tipo = $dados['tipo'] ?? '';
            $this->telefone = $dados['telefone'] ?? null;
            $this->cidade = $dados['cidade'] ?? null;
            $this->estado = $dados['estado'] ?? null;
            $this->foto_perfil = $dados['foto_perfil'] ?? null;
            $this->data_cadastro = $dados['data_cadastro'] ?? null;
        }
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getFotoPerfil() {
        return $this->foto_perfil;
    }

    public function getDataCadastro() {
        return $this->data_cadastro;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setNome($nome) {
        $this->nome = $nome;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
        return $this;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
        return $this;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
        return $this;
    }

    public function setCidade($cidade) {
        $this->cidade = $cidade;
        return $this;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
        return $this;
    }

    public function setFotoPerfil($foto_perfil) {
        $this->foto_perfil = $foto_perfil;
        return $this;
    }

    public function setDataCadastro($data_cadastro) {
        $this->data_cadastro = $data_cadastro;
        return $this;
    }

    /**
     * Retorna os dados do usuário como array
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha,
            'tipo' => $this->tipo,
            'telefone' => $this->telefone,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'foto_perfil' => $this->foto_perfil,
            'data_cadastro' => $this->data_cadastro
        ];
    }

    /**
     * Verifica se o usuário está autenticado
     * @return bool
     */
    public function isAuthenticated() {
        return !empty($this->id);
    }

    /**
     * Retorna o nome completo ou primeiro nome
     * @param bool $primeiroNome Se true, retorna apenas o primeiro nome
     * @return string
     */
    public function getNomeFormatado($primeiroNome = false) {
        if ($primeiroNome) {
            return strtok($this->nome, ' ');
        }
        return $this->nome;
    }

    /**
     * Retorna a localização formatada (Cidade - Estado)
     * @return string
     */
    public function getLocalizacao() {
        if ($this->cidade && $this->estado) {
            return $this->cidade . ' - ' . $this->estado;
        }
        return $this->cidade ?? $this->estado ?? 'Não informado';
    }
}

