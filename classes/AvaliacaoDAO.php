<?php

class AvaliacaoDAO {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Busca todas as avaliações de um técnico, incluindo o nome do cliente.
     */
    public function buscarPorTecnicoId($tecnicoId) {
        $sql = "SELECT a.id, a.nota, a.comentario, a.resposta_tecnico, u.nome AS cliente_nome 
                FROM avaliacoes AS a
                JOIN usuarios AS u ON a.cliente_id = u.id
                WHERE a.tecnico_id = ? 
                ORDER BY a.data_avaliacao DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tecnicoId]);
        return $stmt->fetchAll();
    }

    /**
     * Calcula a média de notas e o total de avaliações de um técnico.
     */
    public function obterEstatisticasPorTecnicoId($tecnicoId) {
        $sql = "SELECT AVG(nota) AS media, COUNT(id) AS total 
                FROM avaliacoes 
                WHERE tecnico_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tecnicoId]);
        return $stmt->fetch();
    }

    /**
     * Salva a resposta de um técnico a uma avaliação específica.
     */
    public function responder($avaliacaoId, $tecnicoId, $resposta) {
        // A condição `tecnico_id = ?` é uma segurança para garantir que
        // um técnico só possa responder a uma avaliação direcionada a ele.
        $sql = "UPDATE avaliacoes SET resposta_tecnico = ? WHERE id = ? AND tecnico_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$resposta, $avaliacaoId, $tecnicoId]);
    }

    /**
     * Cria uma nova avaliação de um cliente para um técnico.
     * @param array $dados Array com cliente_id, tecnico_id, atendimento_id, nota e comentario
     * @return bool Retorna true se a avaliação foi criada com sucesso
     */
    public function criar(array $dados) {
        try {
            // Verifica se já existe uma avaliação para este atendimento
            $sqlCheck = "SELECT id FROM avaliacoes WHERE atendimento_id = ?";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([$dados['atendimento_id']]);
            if ($stmtCheck->fetch()) {
                return false; // Já existe avaliação para este atendimento
            }

            $sql = "INSERT INTO avaliacoes (cliente_id, tecnico_id, atendimento_id, nota, comentario) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $dados['cliente_id'],
                $dados['tecnico_id'],
                $dados['atendimento_id'],
                $dados['nota'],
                $dados['comentario'] ?? null
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verifica se um cliente já avaliou um técnico específico (para um atendimento específico).
     */
    public function verificarSeJaAvaliou($clienteId, $tecnicoId, $atendimentoId = null) {
        if ($atendimentoId) {
            $sql = "SELECT id FROM avaliacoes WHERE cliente_id = ? AND tecnico_id = ? AND atendimento_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$clienteId, $tecnicoId, $atendimentoId]);
        } else {
            $sql = "SELECT id FROM avaliacoes WHERE cliente_id = ? AND tecnico_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$clienteId, $tecnicoId]);
        }
        return $stmt->fetch() !== false;
    }
}
?>