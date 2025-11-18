<?php

require_once 'UserFactory.php';

class UsuarioDAO {

    private $pdo;

    // O construtor continua recebendo a conexão PDO.
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Insere um novo usuário no banco de dados.
     * @param array $dados Os dados do usuário vindos do formulário.
     * @return bool Retorna true se o usuário foi criado com sucesso, false caso contrário.
     */
    public function criar(array $dados) {
        try {
            // A senha NUNCA é salva diretamente. Eu crio um "hash".
            $senhaHash = md5($dados['senha']);

            // O SQL para inserir os dados. Uso placeholders (?) para segurança.
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo, telefone, cidade, estado, especialidade) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);

            // Executo a query, passando os dados na ordem dos placeholders.
            return $stmt->execute([
                $dados['nome'],
                $dados['email'],
                $senhaHash,
                $dados['tipo'],
                $dados['telefone'] ?? null,
                $dados['cidade'] ?? null,
                $dados['estado'] ?? null,
                $dados['especialidade'] ?? null // Específico para técnicos
            ]);

        } catch (PDOException $e) {
            // Se o e-mail já existir, o banco de dados vai gerar um erro.
            // Posso tratar erros específicos aqui no futuro.
            // Por enquanto, apenas retorno false.
            return false;
        }
    }

    /**
     * Verifica as credenciais de um usuário.
     * @param string $email
     * @param string $senha
     * @return array|false Retorna os dados do usuário ou false.
     */
    public function verificarLogin($email, $senha) {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                if (md5($senha) === $usuario['senha']) {
                    return $usuario;
                }
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }


    /**
     * Verifica as credenciais e retorna um objeto User
     * @param string $email
     * @param string $senha
     * @return User|false Retorna um objeto User ou false
     */
    public function verificarLoginObjeto($email, $senha) {
        $dados = $this->verificarLogin($email, $senha);
        if ($dados) {
            return UserFactory::criar($dados);
        }
        return false;
    }

        /**
     * Busca um usuário específico pelo seu ID.
     * @param int $id O ID do usuário a ser buscado.
     * @return array|false Retorna os dados do usuário ou false se não encontrar.
     */
    public function buscarPorId($id) {
        try {
            $sql = "SELECT id, nome, email, telefone, cidade, estado, especialidade, foto_perfil FROM usuarios WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Atualiza os dados de um usuário existente no banco de dados.
     * @param int $id O ID do usuário a ser atualizado.
     * @param array $dados Os novos dados do usuário.
     * @return bool Retorna true se a atualização foi bem-sucedida, false caso contrário.
     */
    public function atualizar($id, array $dados) {
        try {
            // Começa com campos sempre atualizados
            $sql = "UPDATE usuarios SET 
                        nome = ?, 
                        email = ?,  
                        telefone = ?, 
                        cidade = ?, 
                        estado = ?";

            $params = [
                $dados['nome'],
                $dados['email'],
                $dados['telefone'],
                $dados['cidade'],
                $dados['estado']
            ];

            //  Atualiza especialidade APENAS se o tipo for técnico
            if ($dados['tipo'] === 'tecnico') {
                $sql .= ", especialidade = ?";
                $params[] = $dados['especialidade'];
            }

            //  Atualiza senha somente se veio preenchida
            if (!empty($dados['senha'])) {
                $sql .= ", senha = ?";
                $params[] = md5($dados['senha']); // mantendo seu padrão MD5
            }

            //  Atualiza foto quando enviada (mantive seu código)
            if (isset($dados['foto_perfil'])) {
                $sql .= ", foto_perfil = ?";
                $params[] = $dados['foto_perfil'];
            }

            // Fecha a query
            $sql .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);

        } catch (PDOException $e) {
            return false;
        }
    }


    /**
     * Lista todos os usuários do banco de dados.
     * @return array|false Retorna um array com todos os usuários ou false em caso de erro.
     */
    public function listarTodos() {
        try {
            $sql = "SELECT id, nome, email, tipo, telefone, cidade, estado, data_cadastro FROM usuarios ORDER BY data_cadastro DESC";
            $stmt = $this->pdo->prepare($sql);  
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Deleta um usuário do banco de dados.
     * @param int $id O ID do usuário a ser deletado.
     * @return bool Retorna true se a exclusão foi bem-sucedida, false caso contrário.
     */
    public function deletar($id) {
        try {
            $sql = "DELETE FROM usuarios WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Busca um usuário completo pelo ID (incluindo todos os campos).
     * @param int $id O ID do usuário a ser buscado.
     * @return array|false Retorna os dados completos do usuário ou false se não encontrar.
     */
    public function buscarPorIdCompleto($id) {
        try {
            $sql = "SELECT * FROM usuarios WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Busca um usuário completo pelo ID e retorna como objeto User
     * @param int $id O ID do usuário a ser buscado.
     * @return User|false Retorna um objeto User ou false se não encontrar.
     */
    public function buscarPorIdComoObjeto($id) {
        $dados = $this->buscarPorIdCompleto($id);
        if ($dados) {
            return UserFactory::criar($dados);
        }
        return false;
    }

    /**
     * Busca um usuário pelo ID e retorna como objeto User (apenas dados básicos)
     * @param int $id O ID do usuário a ser buscado.
     * @return User|false Retorna um objeto User ou false se não encontrar.
     */
    public function buscarPorIdObjeto($id) {
        $dados = $this->buscarPorId($id);
        if ($dados) {
            // Busca dados completos para criar o objeto
            $dadosCompletos = $this->buscarPorIdCompleto($id);
            if ($dadosCompletos) {
                return UserFactory::criar($dadosCompletos);
            }
        }
        return false;
    }

    /**
     * Lista todos os usuários e retorna como array de objetos User
     * @return array Array de objetos User
     */
    public function listarTodosComoObjetos() {
        $dados = $this->listarTodos();
        if (!$dados) {
            return [];
        }
        
        $usuarios = [];
        foreach ($dados as $dado) {
            // Busca dados completos para criar o objeto
            $dadosCompletos = $this->buscarPorIdCompleto($dado['id']);
            if ($dadosCompletos) {
                $usuarios[] = UserFactory::criar($dadosCompletos);
            }
        }
        
        return $usuarios;
    }



}
?>