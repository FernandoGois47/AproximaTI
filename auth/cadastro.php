<?php 
include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="card-title text-center mb-4">Crie sua Conta</h1>

                    <form action="../processa/processaCadastro.php" method="post">
                        
                        <div class="mb-4 text-center">
                            <label class="form-label">Qual tipo de conta você deseja criar?</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoCliente" value="cliente" checked>
                                <label class="form-check-label" for="tipoCliente">Sou Cliente</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoTecnico" value="tecnico">
                                <label class="form-check-label" for="tipoTecnico">Sou Técnico</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>

                        <div class="mb-3" id="campoEspecialidade" style="display: none;">
                            <label for="especialidade" class="form-label">Especialidade Principal</label>
                            <input type="text" class="form-control" id="especialidade" name="especialidade" placeholder="Ex: Manutenção de Notebooks">
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Cadastrar</button>
                        </div>
                    </form>
                    
                </div>
                <div class="card-footer text-center">
                    <small>Já tem uma conta? <a href="login.php">Faça o login</a></small>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    // Script para mostrar/ocultar o campo de especialidade
    const tipoCliente = document.getElementById('tipoCliente');
    const tipoTecnico = document.getElementById('tipoTecnico');
    const campoEspecialidade = document.getElementById('campoEspecialidade');
    const inputEspecialidade = document.getElementById('especialidade');

    function toggleEspecialidade() {
        if (tipoTecnico.checked) {
            campoEspecialidade.style.display = 'block';
            inputEspecialidade.required = true;
        } else {
            campoEspecialidade.style.display = 'none';
            inputEspecialidade.required = false;
        }
    }

    tipoCliente.addEventListener('change', toggleEspecialidade);
    tipoTecnico.addEventListener('change', toggleEspecialidade);
</script>

<?php 
include '../includes/footer.php';
?>

