        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0">&copy; 2024 AproximaTI - Painel Administrativo. Todos os direitos reservados.</p>
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

        const urlParams = new URLSearchParams(window.location.search);
        const sucesso = urlParams.get('sucesso');
        const erro = urlParams.get('erro');

        const mensagens = {
            sucesso: {
                edicao: 'Usuário atualizado com sucesso!',
                deletar: 'Usuário excluído com sucesso!',
                criar: 'Usuário criado com sucesso!'
            },
            erro: {
                acesso_negado: 'Você não tem permissão para acessar esta página.',
                login_necessario: 'É necessário fazer login para acessar esta página.',
                edicao: 'Erro ao atualizar o usuário.',
                deletar: 'Erro ao excluir o usuário.',
                criar: 'Erro ao criar o usuário.',
                usuario_nao_encontrado: 'Usuário não encontrado.',
                nao_pode_deletar: 'Não é possível excluir este usuário.'
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

