<?php

class MensagemDAO {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Envia uma nova mensagem no chat do atendimento
     */
    public function enviarMensagem($atendimentoId, $remetenteId, $mensagem) {
        $sql = "INSERT INTO mensagens (atendimento_id, remetente_id, mensagem) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$atendimentoId, $remetenteId, $mensagem]);
    }

    /**
     * Busca todas as mensagens de um atendimento específico
     * Retorna com informações do remetente
     */
    public function buscarMensagensPorAtendimento($atendimentoId) {
        $sql = "SELECT 
                    m.id,
                    m.mensagem,
                    m.data_envio,
                    m.remetente_id,
                    m.lida,
                    u.nome AS remetente_nome,
                    u.foto_perfil AS remetente_foto,
                    u.tipo AS remetente_tipo
                FROM mensagens AS m
                JOIN usuarios AS u ON m.remetente_id = u.id
                WHERE m.atendimento_id = ?
                ORDER BY m.data_envio ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$atendimentoId]);
        return $stmt->fetchAll();
    }

    /**
     * Marca mensagens como lidas para um usuário específico
     * (mensagens que não foram enviadas por ele)
     */
    public function marcarComoLidas($atendimentoId, $usuarioId) {
        $sql = "UPDATE mensagens 
                SET lida = 1 
                WHERE atendimento_id = ? 
                AND remetente_id != ? 
                AND lida = 0";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$atendimentoId, $usuarioId]);
    }

    /**
     * Conta quantas mensagens não lidas existem para um usuário em um atendimento
     */
    public function contarNaoLidas($atendimentoId, $usuarioId) {
        $sql = "SELECT COUNT(*) as total
                FROM mensagens 
                WHERE atendimento_id = ? 
                AND remetente_id != ? 
                AND lida = 0";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$atendimentoId, $usuarioId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Verifica se o usuário tem permissão para acessar o chat do atendimento
     * (precisa ser o cliente ou técnico do atendimento)
     */
    public function verificarPermissaoChat($atendimentoId, $usuarioId) {
        $sql = "SELECT id FROM atendimentos 
                WHERE id = ? 
                AND (cliente_id = ? OR tecnico_id = ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$atendimentoId, $usuarioId, $usuarioId]);
        return $stmt->fetch() !== false;
    }
}
?>

