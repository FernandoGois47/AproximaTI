<?php

require_once 'User.php';
require_once 'Cliente.php';
require_once 'Tecnico.php';
require_once 'Admin.php';

/**
 * Factory para criar instâncias de User baseado no tipo
 */
class UserFactory {
    
    /**
     * Cria uma instância do tipo apropriado de User baseado nos dados
     * @param array $dados Dados do usuário vindos do banco de dados
     * @return User Instância de Cliente, Tecnico ou Admin
     * @throws Exception Se o tipo não for reconhecido
     */
    public static function criar(array $dados) {
        if (empty($dados)) {
            throw new Exception('Dados do usuário não fornecidos');
        }
        
        $tipo = $dados['tipo'] ?? '';
        
        switch ($tipo) {
            case 'cliente':
                return new Cliente($dados);
                
            case 'tecnico':
                return new Tecnico($dados);
                
            case 'admin':
                return new Admin($dados);
                
            default:
                throw new Exception("Tipo de usuário desconhecido: {$tipo}");
        }
    }
    
    /**
     * Cria uma instância de User baseado apenas no tipo
     * @param string $tipo Tipo do usuário (cliente, tecnico, admin)
     * @return User Instância vazia do tipo apropriado
     * @throws Exception Se o tipo não for reconhecido
     */
    public static function criarPorTipo($tipo) {
        $dados = ['tipo' => $tipo];
        return self::criar($dados);
    }
    
    /**
     * Cria um objeto User a partir dos dados da sessão
     * @param array $session Dados da sessão
     * @return User|null Instância do usuário ou null se não autenticado
     */
    public static function criarDaSessao($session) {
        if (empty($session['usuario_id']) || empty($session['usuario_tipo'])) {
            return null;
        }
        
        $dados = [
            'id' => $session['usuario_id'],
            'nome' => $session['usuario_nome'] ?? '',
            'email' => $session['usuario_email'] ?? '',
            'tipo' => $session['usuario_tipo'],
            'foto_perfil' => $session['usuario_foto'] ?? null
        ];
        
        return self::criar($dados);
    }
}

