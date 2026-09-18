# Gerenciador de Shows (projeto simples para estudante)

Projeto PHP simples, sem Composer, sem npm/package.json e sem upload de arquivos —
ideal para praticar PHP orientado a objetos com o padrão MVC (Model-View-Controller) básico.

## Estrutura

```
GerenciadorShows/
├── config/
│   └── database.php      -> conexão PDO com o MySQL
├── models/
│   ├── User.php          -> acesso à tabela "users"
│   └── Show.php          -> acesso à tabela "shows"
├── controllers/
│   ├── UserController.php -> regras de login/cadastro
│   └── ShowController.php -> regras de validação/CRUD de shows
├── views/
│   ├── register.php      -> tela de cadastro
│   ├── home.php          -> painel: cadastra e lista shows
│   └── logout.php        -> encerra a sessão
├── css/
│   └── style.css
├── index.php              -> tela de login
└── database.sql           -> script para criar o banco
```

## Como rodar (banco de dados: Supabase / PostgreSQL)

1. **Extensão do PHP**: confirme que a extensão `pdo_pgsql` está habilitada.
   - No `php.ini`, procure a linha `;extension=pdo_pgsql` e tire o `;` da frente.
   - Confira rodando `php -m` no terminal — `pdo_pgsql` precisa aparecer na lista.
2. **Criar as tabelas**: no painel do Supabase, vá em **SQL Editor** e rode o
   conteúdo de `database.sql`.
3. **Credenciais**: no painel do Supabase, vá em **Project Settings > Database >
   Connection string** e copie: Host, Port, Database, User e Password.
   Cole esses valores em `config/database.php` (nas constantes `DB_HOST`,
   `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`).
   - Use a porta **5432** (conexão direta) para começar; se tiver problema de
     limite de conexões, troque para **6543** (Connection Pooler) e ajuste o
     `DB_USER` para o formato `postgres.xxxxxxxx` que o Supabase mostrar.
4. **Servidor PHP**: dentro da pasta do projeto, rode:
   ```
   php -S localhost:8000
   ```
5. Acesse `http://localhost:8000` no navegador, clique em "Cadastre-se aqui",
   crie uma conta e faça login.

## O que estudar neste projeto

- **PDO + Prepared Statements**: veja como cada Model usa `bindParam` para evitar SQL Injection.
- **MVC sem framework**: Model cuida do banco, Controller cuida da regra de negócio,
  View cuida só do HTML.
- **Sessão (`$_SESSION`)**: como login/logout e proteção de páginas funcionam em PHP puro.
- **Hash de senha**: `password_hash()` / `password_verify()`, nunca salve senha em texto puro.
- **`require_once`**: como os arquivos se conectam sem Composer/autoload — repare que cada
  arquivo declara explicitamente do que ele precisa.

## Próximos passos sugeridos (para praticar mais)

- Adicionar edição de show (`update`).
- Adicionar paginação na lista de shows.
- Criar uma tabela de "inscrições" para participantes se inscreverem em um show.
