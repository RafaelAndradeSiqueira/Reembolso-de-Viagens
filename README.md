# 🚗 Reembolso de Viagens

Sistema para calcular quanto a empresa deve reembolsar por mês pelas viagens de carro.
Cada pessoa cria a própria conta, define o valor de cada dia da semana, marca folgas/feriados
no calendário e a IA (modelo gratuito do **OpenRouter**) calcula o total. O backend confere a conta da IA.

```
backend/   Laravel 12 (API + Services + SQLite)
frontend/  Vue 2.7 + BootstrapVue (Vite)
```

## Como funciona

```
Vue (componentes)  ──HTTP──>  Controllers  ──>  Services                  ──>  Models (SQLite)
                                                 ├─ UsuarioService             Usuario
                                                 ├─ TokenService               ValorDiaSemana
                                                 ├─ ValorDiaSemanaService
                                                 ├─ DiasUteisService
                                                 ├─ OpenRouterService          (chamada à IA)
                                                 ├─ ReembolsoService           (prompt + conferência)
                                                 └─ BancoDadosService          (cria o banco sozinho)
```

| Rota | O que faz |
|---|---|
| `POST /api/cadastrar` | cria conta (nome, email, senha, senha_confirmation) e já devolve o token |
| `POST /api/entrar` | login (email, senha) |
| `GET /api/eu` | usuário logado |
| `GET/PUT /api/valores` | valores por dia da semana (1 = segunda ... 5 = sexta) |
| `POST /api/reembolsos/gerar` | ano, mes, datas_excluidas → cálculo da IA + conferência |

- **OpenRouter**: `OpenRouterService::enviarMensagens()` chama `POST /chat/completions` com uma lista de modelos
  (`OPENROUTER_MODEL` + `OPENROUTER_FALLBACK_MODELS`). Se um modelo gratuito estiver sem limite,
  o OpenRouter tenta o próximo. O prompt fica em `ReembolsoService::montarMensagens()`.
- **Conferência**: modelo gratuito às vezes erra conta. O backend soma também; se a IA errar,
  a tela mostra o valor correto e avisa.
- **Segurança**: senha com hash bcrypt, banco fora da pasta `public`, token criptografado com a `APP_KEY`
  (expira em 7 dias), limite de tentativas (login 5/min, cadastro 3/min e 10/hora, IA 6/min).

> Os nomes que o Laravel/Vue exigem continuam em inglês (`handle`, `rules`, `casts`, `data`, `computed`...),
> assim como os campos da API do OpenRouter (`model`, `messages`) e as variáveis de ambiente.

## Rodando local

```bash
cd backend
composer install
cp .env.example .env        # se ainda não existir
php artisan key:generate    # só na primeira vez
php artisan migrate         # cria database/database.sqlite
php artisan serve
```

```bash
cd frontend
npm install
cp .env.example .env
npm run dev                 # http://localhost:5173 → "Criar conta"
```

> Se o PHP local der `SSL certificate problem` ao chamar o OpenRouter (comum no WAMP),
> preencha `OPENROUTER_CA_BUNDLE` no `.env` com um bundle de certificados
> (ex.: `C:/Program Files/Git/mingw64/etc/ssl/certs/ca-bundle.crt`).

## Deploy na Vercel (2 projetos gratuitos)

### 0. Subir o código para o GitHub

```bash
git init
git add .
git commit -m "Reembolso de Viagens"
gh repo create reembolso-viagens --private --source=. --push
```

### 1. Backend
1. *New Project* → repositório → **Root Directory: `backend`**.
2. Em *Environment Variables*:

   | Variável | Valor |
   |---|---|
   | `APP_KEY` | gere com `php artisan key:generate --show` |
   | `OPENROUTER_API_KEY` | sua chave do OpenRouter |
   | `FRONTEND_URL` | URL do front, ex.: `https://reembolso.vercel.app` |

   O resto (SQLite em `/tmp`, criação automática do banco) já está em `backend/vercel.json`.

### 2. Frontend
1. *New Project* → repositório → **Root Directory: `frontend`** (framework: Vite).
2. Variável `VITE_API_URL` = URL do backend (ex.: `https://reembolso-api.vercel.app`).
3. Confira se o `FRONTEND_URL` do backend é exatamente a URL do front e faça redeploy do backend.

## ⚠️ SQLite na Vercel

A Vercel só deixa gravar em `/tmp`, e essa pasta **é apagada** quando a função fica parada
ou é reiniciada (e cada instância tem a sua). Na prática, com SQLite na Vercel **as contas e os
valores somem de tempos em tempos** e a pessoa precisa se cadastrar de novo.
Localmente (ou num servidor com disco próprio) o SQLite funciona normalmente.

Para ter dados permanentes **sem mudar nenhuma linha de código**, use um Postgres gratuito
(ex.: Neon ou Supabase, ambos disponíveis no Marketplace da Vercel):

1. Crie o banco e copie a connection string.
2. No `backend/vercel.json`, troque `"DB_CONNECTION": "sqlite"` por `"DB_CONNECTION": "pgsql"`.
3. Na Vercel, adicione `DB_URL` = a connection string.
4. Redeploy. As tabelas são criadas sozinhas no primeiro acesso (`DB_MIGRAR_AUTOMATICAMENTE`).
