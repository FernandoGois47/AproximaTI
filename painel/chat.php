<?php
session_start();

// Segurança: Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php?erro=login_necessario');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/AtendimentoDAO.php';
require_once '../classes/MensagemDAO.php';

// Verifica se foi passado o ID do atendimento
if (!isset($_GET['atendimento_id'])) {
    header('Location: ' . ($_SESSION['usuario_tipo'] === 'tecnico' ? 'gerenciarAtendimentos.php' : 'meusPedidos.php') . '?erro=atendimento_invalido');
    exit;
}

$atendimentoId = (int)$_GET['atendimento_id'];
$usuarioId = $_SESSION['usuario_id'];

$atendimentoDAO = new AtendimentoDAO($pdo);
$mensagemDAO = new MensagemDAO($pdo);

// Busca informações do atendimento
$atendimento = $atendimentoDAO->buscarPorId($atendimentoId);

if (!$atendimento) {
    header('Location: ' . ($_SESSION['usuario_tipo'] === 'tecnico' ? 'gerenciarAtendimentos.php' : 'meusPedidos.php') . '?erro=atendimento_nao_encontrado');
    exit;
}

// Verifica permissão: só cliente ou técnico do atendimento podem acessar
if ($atendimento['cliente_id'] != $usuarioId && $atendimento['tecnico_id'] != $usuarioId) {
    header('Location: ' . ($_SESSION['usuario_tipo'] === 'tecnico' ? 'gerenciarAtendimentos.php' : 'meusPedidos.php') . '?erro=acesso_negado');
    exit;
}

// Busca todas as mensagens do atendimento
$mensagens = $mensagemDAO->buscarMensagensPorAtendimento($atendimentoId);

// Marca mensagens como lidas
$mensagemDAO->marcarComoLidas($atendimentoId, $usuarioId);

// Define informações do outro participante
$outroParticipante = null;
if ($_SESSION['usuario_tipo'] === 'tecnico') {
    $outroParticipante = [
        'nome' => $atendimento['cliente_nome'],
        'id' => $atendimento['cliente_id']
    ];
} else {
    $outroParticipante = [
        'nome' => $atendimento['tecnico_nome'],
        'id' => $atendimento['tecnico_id']
    ];
}

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = $_SESSION['usuario_tipo'] === 'tecnico' ? 'atendimentos' : 'pedidos';
                include '../includes/' . ($_SESSION['usuario_tipo'] === 'tecnico' ? 'menuTecnico.php' : 'menuCliente.php'); 
            ?>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots"></i> Chat - <?php echo htmlspecialchars($atendimento['servico_titulo']); ?>
                    </h5>
                    <small>Conversando com: <?php echo htmlspecialchars($outroParticipante['nome']); ?></small>
                </div>
                
                <div class="card-body p-0" style="height: 500px; overflow-y: auto;" id="chatMessages">
                    <?php if (empty($mensagens)): ?>
                        <div class="text-center text-muted p-4">
                            <i class="bi bi-chat-left-text" style="font-size: 3rem;"></i>
                            <p class="mt-3">Nenhuma mensagem ainda. Comece a conversa!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($mensagens as $mensagem): ?>
                            <?php
                            $isMine = ($mensagem['remetente_id'] == $usuarioId);
                            $avatarPath = '../assets/img/' . ($mensagem['remetente_foto'] ?? 'avatar_default.png');
                            ?>
                            <div class="d-flex mb-3 p-3 <?php echo $isMine ? 'justify-content-end' : 'justify-content-start'; ?>">
                                <div class="d-flex <?php echo $isMine ? 'flex-row-reverse' : 'flex-row'; ?>" style="max-width: 70%;">
                                    <?php if (!$isMine): ?>
                                        <img src="<?php echo $avatarPath; ?>" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="<?php echo $isMine ? 'me-2' : ''; ?>">
                                        <div class="card <?php echo $isMine ? 'bg-primary text-white' : 'bg-light'; ?>">
                                            <div class="card-body p-2">
                                                <p class="mb-1"><?php echo nl2br(htmlspecialchars($mensagem['mensagem'])); ?></p>
                                                <small class="<?php echo $isMine ? 'text-white-50' : 'text-muted'; ?>" style="font-size: 0.7rem;">
                                                    <?php echo date('d/m/Y H:i', strtotime($mensagem['data_envio'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($isMine): ?>
                                        <img src="<?php echo '../assets/img/' . ($_SESSION['usuario_foto'] ?? 'avatar_default.png'); ?>" alt="Avatar" class="rounded-circle ms-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <form id="formMensagem" class="d-flex">
                        <input type="hidden" name="atendimento_id" value="<?php echo $atendimentoId; ?>">
                        <input type="text" name="mensagem" id="inputMensagem" class="form-control me-2" placeholder="Digite sua mensagem..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Enviar
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-3">
                <a href="<?php echo $_SESSION['usuario_tipo'] === 'tecnico' ? 'gerenciarAtendimentos.php' : 'meusPedidos.php'; ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</main>

<script>
// Auto-scroll para a última mensagem
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

scrollToBottom();

// Enviar mensagem via AJAX
document.getElementById('formMensagem').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const inputMensagem = document.getElementById('inputMensagem');
    const mensagemTexto = inputMensagem.value.trim();
    
    if (!mensagemTexto) {
        return;
    }
    
    // Desabilita o botão enquanto envia
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
    
    fetch('../processa/processaEnviarMensagem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            inputMensagem.value = '';
            // Recarrega as mensagens
            carregarMensagens();
        } else {
            alert('Erro ao enviar mensagem: ' + (data.erro || 'Erro desconhecido'));
        }
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send"></i> Enviar';
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao enviar mensagem. Tente novamente.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send"></i> Enviar';
    });
});

// Carregar mensagens via AJAX
function carregarMensagens() {
    fetch('../processa/buscarMensagens.php?atendimento_id=<?php echo $atendimentoId; ?>')
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            atualizarChat(data.mensagens);
        }
    })
    .catch(error => {
        console.error('Erro ao carregar mensagens:', error);
    });
}

// Atualizar interface do chat
function atualizarChat(mensagens) {
    const chatMessages = document.getElementById('chatMessages');
    const usuarioId = <?php echo $usuarioId; ?>;
    const minhaFoto = '../assets/img/<?php echo htmlspecialchars($_SESSION['usuario_foto'] ?? 'avatar_default.png', ENT_QUOTES); ?>';
    
    if (mensagens.length === 0) {
        chatMessages.innerHTML = `
            <div class="text-center text-muted p-4">
                <i class="bi bi-chat-left-text" style="font-size: 3rem;"></i>
                <p class="mt-3">Nenhuma mensagem ainda. Comece a conversa!</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    mensagens.forEach(mensagem => {
        const isMine = (mensagem.remetente_id == usuarioId);
        const avatarPath = '../assets/img/' + (mensagem.remetente_foto || 'avatar_default.png');
        const dataFormatada = new Date(mensagem.data_envio).toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Escapa HTML mas mantém quebras de linha
        const mensagemTexto = mensagem.mensagem
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/\n/g, '<br>');
        
        html += `
            <div class="d-flex mb-3 p-3 ${isMine ? 'justify-content-end' : 'justify-content-start'}">
                <div class="d-flex ${isMine ? 'flex-row-reverse' : 'flex-row'}" style="max-width: 70%;">
                    ${!isMine ? `<img src="${avatarPath}" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">` : ''}
                    <div class="${isMine ? 'me-2' : ''}">
                        <div class="card ${isMine ? 'bg-primary text-white' : 'bg-light'}">
                            <div class="card-body p-2">
                                <p class="mb-1">${mensagemTexto}</p>
                                <small class="${isMine ? 'text-white-50' : 'text-muted'}" style="font-size: 0.7rem;">
                                    ${dataFormatada}
                                </small>
                            </div>
                        </div>
                    </div>
                    ${isMine ? `<img src="${minhaFoto}" alt="Avatar" class="rounded-circle ms-2" style="width: 40px; height: 40px; object-fit: cover;">` : ''}
                </div>
            </div>
        `;
    });
    
    chatMessages.innerHTML = html;
    scrollToBottom();
}

// Atualiza mensagens a cada 3 segundos
setInterval(carregarMensagens, 3000);
</script>

<?php
include '../includes/footer.php';
?>

