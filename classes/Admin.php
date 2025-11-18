<?php

require_once 'User.php';

/**
 * Classe que representa um Administrador no sistema
 * Herda de User e adiciona funcionalidades específicas de administradores
 */
class Admin extends User {
    
    /**
     * Construtor da classe Admin
     * @param array $dados Dados do administrador vindos do banco de dados
     */
    public function __construct(array $dados = []) {
        parent::__construct($dados);
        $this->tipo = 'admin';
    }

    /**
     * Verifica se o usuário tem permissões de administrador
     * @return bool
     */
    public function isAdmin() {
        return $this->tipo === 'admin' && $this->isAuthenticated();
    }

    /**
     * Retorna todos os usuários do sistema
     * @param object $usuarioDAO Instância de UsuarioDAO
     * @return array Array de objetos User
     */
    public function listarUsuarios($usuarioDAO) {
        if (!$this->isAdmin()) {
            return [];
        }
        
        $usuariosData = $usuarioDAO->listarTodos();
        $usuarios = [];
        
        foreach ($usuariosData as $dados) {
            $usuarios[] = UserFactory::criar($dados);
        }
        
        return $usuarios;
    }

    /**
     * Busca um usuário por ID
     * @param int $usuarioId ID do usuário
     * @param object $usuarioDAO Instância de UsuarioDAO
     * @return User|null
     */
    public function buscarUsuario($usuarioId, $usuarioDAO) {
        if (!$this->isAdmin()) {
            return null;
        }
        
        $dados = $usuarioDAO->buscarPorIdCompleto($usuarioId);
        
        if (!$dados) {
            return null;
        }
        
        return UserFactory::criar($dados);
    }

    /**
     * Atualiza um usuário
     * @param int $usuarioId ID do usuário
     * @param array $dados Dados para atualizar
     * @param object $usuarioDAO Instância de UsuarioDAO
     * @return bool
     */
    public function atualizarUsuario($usuarioId, array $dados, $usuarioDAO) {
        if (!$this->isAdmin()) {
            return false;
        }
        
        return $usuarioDAO->atualizar($usuarioId, $dados);
    }

    /**
     * Deleta um usuário
     * @param int $usuarioId ID do usuário
     * @param object $usuarioDAO Instância de UsuarioDAO
     * @return bool
     */
    public function deletarUsuario($usuarioId, $usuarioDAO) {
        if (!$this->isAdmin()) {
            return false;
        }
        
        // Não permite deletar a si mesmo
        if ($usuarioId == $this->id) {
            return false;
        }
        
        return $usuarioDAO->deletar($usuarioId);
    }

    /**
     * Cria um novo usuário
     * @param array $dados Dados do novo usuário
     * @param object $usuarioDAO Instância de UsuarioDAO
     * @return bool|int ID do usuário criado ou false em caso de erro
     */
    public function criarUsuario(array $dados, $usuarioDAO) {
        if (!$this->isAdmin()) {
            return false;
        }
        
        // Validação básica
        if (empty($dados['nome']) || empty($dados['email']) || empty($dados['tipo'])) {
            return false;
        }
        
        // Hash da senha se fornecida
        if (!empty($dados['senha'])) {
            $dados['senha'] = md5($dados['senha']);
        }
        
        return $usuarioDAO->criar($dados);
    }

    /**
     * Verifica se o administrador pode acessar o painel admin
     * @return bool
     */
    public function podeAcessarPainelAdmin() {
        return $this->isAdmin();
    }
}

