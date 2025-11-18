<?php

class ServicoDAO {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function buscarPorTecnicoId($tecnicoId) {
        $sql = "SELECT * FROM servicos WHERE tecnico_id = ? ORDER BY titulo ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tecnicoId]);
        return $stmt->fetchAll();
    }

    public function adicionar(array $dados) {
        $sql = "INSERT INTO servicos (tecnico_id, titulo, descricao, preco) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['tecnico_id'],
            $dados['titulo'],
            $dados['descricao'],
            $dados['preco']
        ]);
    }

    /**
     * Busca um serviço específico pelo seu ID, garantindo que ele pertence ao técnico logado.
     */
    public function buscarPorId($id, $tecnicoId) {
        $sql = "SELECT * FROM servicos WHERE id = ? AND tecnico_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $tecnicoId]);
        return $stmt->fetch();
    }

    /**
     * Atualiza os dados de um serviço existente.
     */
    public function atualizar(array $dados) {
        $sql = "UPDATE servicos SET titulo = ?, descricao = ?, preco = ? WHERE id = ? AND tecnico_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['titulo'],
            $dados['descricao'],
            $dados['preco'],
            $dados['id'],
            $dados['tecnico_id']
        ]);
    }

    public function deletar($id, $tecnicoId) {
        $sql = "DELETE FROM servicos WHERE id = ? AND tecnico_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id, $tecnicoId]);
    }
}
?>