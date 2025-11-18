<?php

require_once 'User.php';

/**
 * Classe que representa um Técnico no sistema
 * Herda de User e adiciona funcionalidades específicas de técnicos
 */
class Tecnico extends User {
    protected $especialidade;
    protected $media_avaliacoes;
    protected $total_avaliacoes;

    /**
     * Construtor da classe Tecnico
     * @param array $dados Dados do técnico vindos do banco de dados
     */
    public function __construct(array $dados = []) {
        parent::__construct($dados);
        $this->tipo = 'tecnico';
        $this->especialidade = $dados['especialidade'] ?? null;
        $this->media_avaliacoes = $dados['media_avaliacoes'] ?? null;
        $this->total_avaliacoes = $dados['total_avaliacoes'] ?? null;
    }

    // Getters específicos
    public function getEspecialidade() {
        return $this->especialidade;
    }

    public function getMediaAvaliacoes() {
        return $this->media_avaliacoes;
    }

    public function getTotalAvaliacoes() {
        return $this->total_avaliacoes;
    }

    // Setters específicos
    public function setEspecialidade($especialidade) {
        $this->especialidade = $especialidade;
        return $this;
    }

    public function setMediaAvaliacoes($media) {
        $this->media_avaliacoes = $media;
        return $this;
    }

    public function setTotalAvaliacoes($total) {
        $this->total_avaliacoes = $total;
        return $this;
    }

    /**
     * Retorna as estrelas formatadas para exibição
     * @return string HTML com as estrelas
     */
    public function getEstrelasFormatadas() {
        if (!$this->media_avaliacoes) {
            return '<small class="text-muted">Sem avaliações</small>';
        }

        $media = round($this->media_avaliacoes);
        $html = '';
        
        for ($i = 0; $i < $media; $i++) {
            $html .= '<i class="bi bi-star-fill"></i>';
        }
        
        for ($i = $media; $i < 5; $i++) {
            $html .= '<i class="bi bi-star"></i>';
        }
        
        $html .= '<span class="text-muted small">(' . number_format($this->media_avaliacoes, 1, ',', '.') . ')</span>';
        
        return $html;
    }

    /**
     * Retorna os serviços do técnico
     * @param object $servicoDAO Instância de ServicoDAO
     * @return array
     */
    public function getServicos($servicoDAO) {
        if (!$this->isAuthenticated()) {
            return [];
        }
        
        return $servicoDAO->buscarPorTecnicoId($this->id);
    }

    /**
     * Retorna os itens do portfólio do técnico
     * @param object $portfolioDAO Instância de PortfolioDAO
     * @return array
     */
    public function getPortfolio($portfolioDAO) {
        if (!$this->isAuthenticated()) {
            return [];
        }
        
        return $portfolioDAO->buscarPorTecnicoId($this->id);
    }

    /**
     * Retorna os atendimentos do técnico
     * @param object $atendimentoDAO Instância de AtendimentoDAO
     * @return array
     */
    public function getAtendimentos($atendimentoDAO) {
        if (!$this->isAuthenticated()) {
            return [];
        }
        
        return $atendimentoDAO->buscarPorTecnicoId($this->id);
    }

    /**
     * Retorna as avaliações recebidas pelo técnico
     * @param object $avaliacaoDAO Instância de AvaliacaoDAO
     * @return array
     */
    public function getAvaliacoes($avaliacaoDAO) {
        if (!$this->isAuthenticated()) {
            return [];
        }
        
        return $avaliacaoDAO->buscarPorTecnicoId($this->id);
    }

    /**
     * Retorna estatísticas de avaliações do técnico
     * @param object $avaliacaoDAO Instância de AvaliacaoDAO
     * @return array
     */
    public function getEstatisticasAvaliacoes($avaliacaoDAO) {
        if (!$this->isAuthenticated()) {
            return [
                'media' => 0,
                'total' => 0
            ];
        }
        
        return $avaliacaoDAO->obterEstatisticasPorTecnicoId($this->id);
    }

    /**
     * Verifica se o técnico pode aceitar um atendimento
     * @param int $atendimentoId ID do atendimento
     * @param object $atendimentoDAO Instância de AtendimentoDAO
     * @return bool
     */
    public function podeAceitarAtendimento($atendimentoId, $atendimentoDAO) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        // Busca o atendimento e verifica se pertence ao técnico
        $atendimento = $atendimentoDAO->buscarPorId($atendimentoId);
        return $atendimento && $atendimento['tecnico_id'] == $this->id;
    }

    /**
     * Atualiza o status de um atendimento
     * @param int $atendimentoId ID do atendimento
     * @param string $novoStatus Novo status
     * @param object $atendimentoDAO Instância de AtendimentoDAO
     * @return bool
     */
    public function atualizarStatusAtendimento($atendimentoId, $novoStatus, $atendimentoDAO) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        return $atendimentoDAO->atualizarStatus($atendimentoId, $this->id, $novoStatus);
    }

    /**
     * Retorna os dados do técnico como array (incluindo campos específicos)
     * @return array
     */
    public function toArray() {
        $array = parent::toArray();
        $array['especialidade'] = $this->especialidade;
        $array['media_avaliacoes'] = $this->media_avaliacoes;
        $array['total_avaliacoes'] = $this->total_avaliacoes;
        return $array;
    }
}

