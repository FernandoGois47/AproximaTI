<?php

class PortfolioDAO {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function buscarPorTecnicoId($tecnicoId) {
        $sql = "SELECT * FROM portfolio WHERE tecnico_id = ? ORDER BY data_publicacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tecnicoId]);
        return $stmt->fetchAll();
    }

    public function adicionar(array $dados) {
        $sql = "INSERT INTO portfolio (tecnico_id, titulo, descricao, imagem_url) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['tecnico_id'],
            $dados['titulo'],
            $dados['descricao'],
            $dados['imagem_url']
        ]);
    }

    public function deletar($id, $tecnicoId) {
        // A verificação do tecnicoId garante que um técnico só possa deletar seus próprios itens.
        $sql = "DELETE FROM portfolio WHERE id = ? AND tecnico_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id, $tecnicoId]);
    }

    public function buscarPorId($id, $tecnicoId) {
        $sql = "SELECT * FROM portfolio WHERE id = ? AND tecnico_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $tecnicoId]);
        return $stmt->fetch();
    }
}
?>