<?php 
include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="card-title text-center mb-4">Login</h1>

                    <form action="../processa/processaLogin.php" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Entrar</button>
                        </div>
                    </form>
                    
                </div>
                <div class="card-footer text-center">
                    <small>Ainda não tem uma conta? <a href="cadastro.php">Cadastre-se</a></small>
                </div>
            </div>

        </div>
    </div>
</main>

<?php 
include '../includes/footer.php';
?>

