<?php

require_once 'User.php';

/**
 * Classe que representa um Cliente no sistema
 * Herda de User e adiciona funcionalidades específicas de clientes
 */
class Cliente extends User {
    
    /**
     * Construtor da classe Cliente
     * @param array $dados Dados do cliente vindos do banco de dados
     */
    public function __construct(array $dados = []) {
        parent::__construct($dados);
        $this->tipo = 'cliente';
    }

    /**
     * Verifica se o cliente pode solicitar um serviço
     * @param int $servicoId ID do serviço
     * @param object $atendimentoDAO Instância de AtendimentoDAO
     * @return bool
     */
    public function podeSolicitarServico($servicoId, $atendimentoDAO) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        // Verifica se já existe uma solicitação pendente ou em andamento
        return !$atendimentoDAO->verificarSolicitacaoExistente($this->id, $servicoId);
    }

    /**
     * Retorna a quantidade de pedidos do cliente
     * @param object $atendimentoDAO Instância de AtendimentoDAO
     * @return int
     */
    public function getQuantidadePedidos($atendimentoDAO) {
        if (!$this->isAuthenticated()) {
            return 0;
        }
        
        $pedidos = $atendimentoDAO->buscarPorClienteId($this->id);
        return count($pedidos);
    }

    /**
     * Retorna os pedidos do cliente
     * @param object $atendimentoDAO Instância de AtendimentoDAO
     * @return array
     */
    public function getPedidos($atendimentoDAO) {
        if (!$this->isAuthenticated()) {
            return [];
        }
        
        return $atendimentoDAO->buscarPorClienteId($this->id);
    }

    /**
     * Verifica se o cliente pode avaliar um atendimento
     * @param int $atendimentoId ID do atendimento
     * @param object $avaliacaoDAO Instância de AvaliacaoDAO
     * @return bool
     */
    public function podeAvaliar($atendimentoId, $avaliacaoDAO) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        // Verifica se já avaliou este atendimento
        return !$avaliacaoDAO->verificarAvaliacaoExistente($atendimentoId, $this->id);
    }
}

