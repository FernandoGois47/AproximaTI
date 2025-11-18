<?php
// Não preciso mais do require aqui, pois quem usar a classe vai me fornecer a conexão.

class TecnicoDAO {

    private $pdo;

    /**
     * Ao criar um objeto TecnicoDAO, eu preciso receber a conexão com o banco.
     * Isso se chama Injeção de Dependência.
     * @param PDO $pdo A conexão com o banco de dados.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function buscarTodos() {
        // Esta query agora junta a tabela de usuários com a de avaliações para já calcular a média.
        $sql = "SELECT u.id, u.nome, u.cidade, u.estado, u.especialidade, u.foto_perfil, AVG(a.nota) as media_avaliacoes
                FROM usuarios u
                LEFT JOIN avaliacoes a ON u.id = a.tecnico_id
                WHERE u.tipo = 'tecnico'
                GROUP BY u.id
                ORDER BY u.nome";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function buscarPorTermo($termo) {
        $termoBusca = '%' . $termo . '%';
        // Busca por nome do técnico ou cidade (região)
        $sql = "SELECT u.id, u.nome, u.cidade, u.estado, u.especialidade, u.foto_perfil, AVG(a.nota) as media_avaliacoes
                FROM usuarios u
                LEFT JOIN avaliacoes a ON u.id = a.tecnico_id
                WHERE u.tipo = 'tecnico'
                AND (u.nome LIKE ? OR u.cidade LIKE ?)
                GROUP BY u.id
                ORDER BY u.nome";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$termoBusca, $termoBusca]);
        return $stmt->fetchAll();
    }
}
?>