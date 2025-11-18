<?php

class AtendimentoDAO {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    
    // Cria um novo registro de atendimento no banco.O status inicial será sempre 'pendente'.
    
    public function solicitar(array $dados) {
        $sql = "INSERT INTO atendimentos (cliente_id, tecnico_id, servico_id, status) 
                VALUES (?, ?, ?, 'pendente')";
        
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $dados['cliente_id'],
            $dados['tecnico_id'],
            $dados['servico_id']
        ]);
    }

    // Verifica se um cliente já solicitou um serviço específico de um técnico.
    
    public function verificarSolicitacaoExistente($clienteId, $servicoId) {
        $sql = "SELECT id FROM atendimentos WHERE cliente_id = ? AND servico_id = ? AND status IN ('pendente', 'em_andamento')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId, $servicoId]);
        return $stmt->fetch() !== false;
    }

   /**
     * Busca todos os atendimentos direcionados a um técnico específico.
     * Junta com as tabelas de usuarios (cliente) e servicos para obter mais detalhes.
     */
    public function buscarPorTecnicoId($tecnicoId) {
        $sql = "SELECT 
                    a.id, 
                    a.status, 
                    a.data_atendimento, 
                    u.nome AS cliente_nome, 
                    s.titulo AS servico_titulo
                FROM atendimentos AS a
                JOIN usuarios AS u ON a.cliente_id = u.id
                JOIN servicos AS s ON a.servico_id = s.id
                WHERE a.tecnico_id = ? 
                ORDER BY a.data_atendimento DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tecnicoId]);
        return $stmt->fetchAll();
    }

    /**
     * Busca todos os atendimentos de um cliente específico.
     * Junta com as tabelas de usuarios (técnico) e servicos para obter mais detalhes.
     */
    public function buscarPorClienteId($clienteId) {
        $sql = "SELECT 
                    a.id, 
                    a.status, 
                    a.data_atendimento,
                    a.tecnico_id,
                    u.nome AS tecnico_nome, 
                    s.titulo AS servico_titulo
                FROM atendimentos AS a
                JOIN usuarios AS u ON a.tecnico_id = u.id
                JOIN servicos AS s ON a.servico_id = s.id
                WHERE a.cliente_id = ? 
                ORDER BY a.data_atendimento DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll();
    }

    /**
     * Busca um atendimento específico por ID com informações completas
     */
    public function buscarPorId($atendimentoId) {
        $sql = "SELECT 
                    a.id, 
                    a.status, 
                    a.data_atendimento,
                    a.cliente_id,
                    a.tecnico_id,
                    a.servico_id,
                    u1.nome AS cliente_nome,
                    u2.nome AS tecnico_nome,
                    s.titulo AS servico_titulo
                FROM atendimentos AS a
                JOIN usuarios AS u1 ON a.cliente_id = u1.id
                JOIN usuarios AS u2 ON a.tecnico_id = u2.id
                JOIN servicos AS s ON a.servico_id = s.id
                WHERE a.id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$atendimentoId]);
        return $stmt->fetch();
    }

    /**
     * Atualiza o status de um atendimento específico.
     * Garante que o técnico só possa atualizar atendimentos direcionados a ele.
     */
    public function atualizarStatus($atendimentoId, $tecnicoId, $novoStatus) {
        // Lista de status válidos para segurança
        $statusPermitidos = ['pendente', 'em_andamento', 'concluido', 'cancelado'];
        if (!in_array($novoStatus, $statusPermitidos)) {
            return false; // Status inválido
        }

        $sql = "UPDATE atendimentos SET status = ? WHERE id = ? AND tecnico_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$novoStatus, $atendimentoId, $tecnicoId]);
    }
}
?>