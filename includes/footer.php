<footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0">&copy; 2024 AproximaTI. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle"></strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastBody">
        </div>
    </div>
</div>


<script>

    const toastLiveExample = document.getElementById('liveToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastBody = document.getElementById('toastBody');

    // Pega os parâmetros da URL (ex: ?sucesso=cadastro)
    const urlParams = new URLSearchParams(window.location.search);
    const sucesso = urlParams.get('sucesso');
    const erro = urlParams.get('erro');

    const mensagens = {
        sucesso: {
            cadastro: 'Cadastro realizado com sucesso! Faça o login.',
            edicao: 'Perfil atualizado com sucesso!',
            adicionar: 'Item adicionado ao portfólio!',
            deletar: 'Item excluído do portfólio!',
            avaliacao_enviada: 'Avaliação enviada com sucesso!',
            solicitacao_enviada: 'Solicitação de serviço enviada com sucesso!'
        },
        erro: {
            acesso_negado: 'Você não tem permissão para acessar esta página.',
            login_invalido: 'Email ou senha incorretos.',
            campos_vazios: 'Por favor, preencha todos os campos.',
            email_existente: 'Este email já está cadastrado.',
            edicao: 'Erro ao salvar as alterações.',
            adicionar: 'Erro ao adicionar o item.',
            deletar: 'Erro ao excluir o item.',
            avaliacao_falhou: 'Erro ao enviar a avaliação. Tente novamente.',
            ja_avaliou: 'Você já avaliou este atendimento.',
            nota_invalida: 'Nota inválida. Selecione uma nota entre 1 e 5 estrelas.',
            atendimento_invalido: 'Não foi possível avaliar. Verifique se o atendimento foi concluído.',
            servico_invalido: 'Dados do serviço inválidos. Tente novamente.',
            solicitacao_existente: 'Você já possui uma solicitação pendente ou em andamento para este serviço.',
            erro_ao_solicitar: 'Erro ao enviar solicitação. Tente novamente mais tarde.'
        }
    };

    if (sucesso && mensagens.sucesso[sucesso]) {
        toastTitle.innerText = 'Sucesso!';
        toastBody.innerText = mensagens.sucesso[sucesso];
        toastLiveExample.classList.add('bg-success-subtle');
        const toast = new bootstrap.Toast(toastLiveExample);
        toast.show();
    }

    if (erro && mensagens.erro[erro]) {
        toastTitle.innerText = 'Erro!';
        toastBody.innerText = mensagens.erro[erro];
        toastLiveExample.classList.add('bg-danger-subtle');
        const toast = new bootstrap.Toast(toastLiveExample);
        toast.show();
    }
</script>

</body>
</html>
</body>
</html>