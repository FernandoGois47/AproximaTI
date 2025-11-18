# AproximaTI - Sistema de Conectividade entre Técnicos e Clientes

Sistema web desenvolvido em PHP para conectar clientes que precisam de serviços técnicos com profissionais especializados.

## 📋 Requisitos do Sistema

Antes de começar, certifique-se de ter instalado:

- **PHP** 7.4 ou superior
- **MySQL** 5.7 ou superior (ou MariaDB 10.2+)
- **Servidor Web** (Apache, Nginx ou XAMPP)
- **XAMPP** (recomendado para desenvolvimento local - já inclui PHP, MySQL e Apache)

## 🚀 Instalação e Configuração

### 1. Clonar/Baixar o Projeto

Se você já tem o projeto, pule esta etapa. Caso contrário, baixe ou clone o repositório para a pasta do seu servidor web.

**Para XAMPP:**
- Coloque o projeto na pasta `C:\xampp\htdocs\AproximaTI\` (Windows)


### 2. Configurar o Banco de Dados

#### 2.1. Iniciar o MySQL

- Abra o **XAMPP Control Panel**
- Inicie o **Apache** e o **MySQL**
- Abra o **phpMyAdmin** (http://localhost/phpmyadmin)

#### 2.2. Criar o Banco de Dados

Você tem duas opções:

**Opção A: Usar o script completo (recomendado para primeira instalação)**

1. Abra o phpMyAdmin
2. Clique na aba "SQL"
3. Copie e cole todo o conteúdo do arquivo `sql/dados_iniciais.sql`
4. Clique em "Executar"
5. Isso criará o banco de dados `aproximati` com todas as tabelas e o usuário administrador inicial

**Opção B: Banco já existe e precisa adicionar apenas o chat**

1. Se você já tem o banco criado mas precisa adicionar a funcionalidade de chat:
2. Abra o phpMyAdmin
3. Selecione o banco `aproximati`
4. Clique na aba "SQL"
5. Copie e cole o conteúdo do arquivo `sql/adicionar_chat.sql`
6. Clique em "Executar"

#### 2.3. Verificar Configurações de Conexão

Edite o arquivo `includes/db.php` se necessário:

```php
$host = 'localhost';      // Host do MySQL (geralmente localhost)
$dbname = 'aproximati';  // Nome do banco de dados
$user = 'root';          // Usuário do MySQL (padrão XAMPP: root)
$pass = '';              // Senha do MySQL (padrão XAMPP: vazio)
```

**⚠️ Importante:** Se você configurou uma senha para o MySQL no XAMPP, altere o `$pass` no arquivo `includes/db.php`.

### 3. Configurar Permissões de Pastas

Certifique-se de que as pastas de upload têm permissão de escrita:

**Windows:**
- Geralmente não é necessário alterar permissões

**Linux/Mac:**
```bash
chmod -R 755 assets/img/
chmod -R 755 assets/img/portfolio/
```

### 4. Acessar o Sistema

Após concluir os passos acima:

1. Certifique-se de que o Apache e MySQL estão rodando no XAMPP
2. Abra seu navegador e acesse:
   ```
   http://localhost/AproximaTI/
   ```

## 👤 Usuário Administrador Padrão

O sistema vem com um usuário administrador pré-configurado:

- **Email:** `admin@aproximati.com`
- **Senha:** `admin`

**⚠️ IMPORTANTE:** Altere a senha do administrador após o primeiro acesso por questões de segurança!

## 📁 Estrutura do Projeto

```
AproximaTI/
├── adm/                    # Painel administrativo
│   ├── index.php          # Dashboard do admin
│   ├── listarUsuarios.php # Listagem de usuários
│   └── ...
├── assets/                 # Arquivos estáticos
│   ├── css/               # Estilos CSS
│   └── img/               # Imagens e uploads
├── auth/                  # Autenticação
│   ├── login.php          # Página de login
│   ├── cadastro.php       # Página de cadastro
│   └── logout.php         # Processamento de logout
├── classes/               # Classes DAO (Data Access Object)
│   ├── UsuarioDAO.php
│   ├── TecnicoDAO.php
│   ├── AtendimentoDAO.php
│   └── ...
├── includes/              # Arquivos incluídos
│   ├── db.php             # Conexão com banco de dados
│   ├── header.php         # Cabeçalho do site
│   ├── footer.php         # Rodapé do site
│   └── ...
├── painel/                # Painéis de usuários
│   ├── painelTecnico.php  # Dashboard do técnico
│   ├── painelCliente.php  # Dashboard do cliente
│   ├── chat.php           # Sistema de chat
│   └── ...
├── processa/              # Scripts de processamento
│   ├── processaLogin.php
│   ├── processaCadastro.php
│   └── ...
├── sql/                   # Scripts SQL
│   ├── dados_iniciais.sql # Script completo de criação
│   └── adicionar_chat.sql # Script para adicionar chat
├── index.php              # Página inicial
├── servico1.php           # Busca de técnicos
└── perfil.php             # Perfil do técnico
```

## 🔧 Funcionalidades Principais

### Para Clientes:
- ✅ Cadastro e login
- ✅ Busca de técnicos por nome ou cidade
- ✅ Visualização de perfis de técnicos
- ✅ Solicitação de serviços
- ✅ Acompanhamento de pedidos
- ✅ Chat em tempo real com técnicos
- ✅ Avaliação de serviços concluídos
- ✅ Edição de perfil

### Para Técnicos:
- ✅ Cadastro e login
- ✅ Gerenciamento de perfil
- ✅ Cadastro de serviços oferecidos
- ✅ Gerenciamento de portfólio
- ✅ Visualização e gerenciamento de solicitações
- ✅ Chat em tempo real com clientes
- ✅ Gerenciamento de avaliações recebidas

### Para Administradores:
- ✅ CRUD completo de usuários
- ✅ Visualização de todos os usuários do sistema
- ✅ Edição e exclusão de usuários

## 🐛 Solução de Problemas Comuns

### Erro: "Could not connect to database"

**Causa:** MySQL não está rodando ou credenciais incorretas.

**Solução:**
1. Verifique se o MySQL está iniciado no XAMPP Control Panel
2. Verifique as credenciais em `includes/db.php`
3. Teste a conexão no phpMyAdmin

### Erro: "Table 'aproximati.xxx' doesn't exist"

**Causa:** Banco de dados não foi criado ou tabelas não foram importadas.

**Solução:**
1. Execute o script `sql/dados_iniciais.sql` no phpMyAdmin
2. Verifique se o banco `aproximati` foi criado corretamente

### Erro ao fazer upload de imagens

**Causa:** Permissões de pasta ou limite de upload do PHP.

**Solução:**
1. Verifique permissões das pastas `assets/img/` e `assets/img/portfolio/`
2. No `php.ini`, verifique:
   - `upload_max_filesize = 10M`
   - `post_max_size = 10M`

### Página em branco após login

**Causa:** Erro de PHP não exibido ou problema de sessão.

**Solução:**
1. Verifique se o `display_errors` está ativado no `php.ini`
2. Verifique os logs de erro do Apache
3. Limpe o cache do navegador

### Chat não atualiza automaticamente

**Causa:** JavaScript desabilitado ou erro no console.

**Solução:**
1. Verifique se o JavaScript está habilitado no navegador
2. Abra o Console do Desenvolvedor (F12) e verifique erros
3. Verifique se os arquivos `processa/buscarMensagens.php` e `processa/processaEnviarMensagem.php` existem

## 🔐 Segurança

### Recomendações para Produção:

1. **Altere a senha do administrador** após o primeiro acesso
2. **Configure senha forte** para o MySQL
3. **Atualize** `includes/db.php` com credenciais seguras
4. **Configure HTTPS** para produção
5. **Desabilite** `display_errors` no `php.ini` em produção
6. **Configure** permissões adequadas de arquivos e pastas

## 📝 Notas Importantes

- O sistema usa **sessões PHP** para autenticação
- As senhas são armazenadas usando **bcrypt** (md5)
- O sistema é compatível com **MySQL 5.7+** e **MariaDB 10.2+**
- O chat atualiza automaticamente a cada 3 segundos
- Uploads de imagens são limitados pelo PHP (padrão: 2MB)

## 🆘 Suporte

Se encontrar problemas não listados aqui:

1. Verifique os logs de erro do Apache/PHP
2. Verifique o console do navegador (F12) para erros JavaScript
3. Verifique se todas as dependências estão instaladas
4. Certifique-se de que está usando a versão correta do PHP

## 📄 Licença

Este projeto é de uso interno/educacional.

---

**Desenvolvido para AproximaTI** - Conectando técnicos e clientes! 🚀

