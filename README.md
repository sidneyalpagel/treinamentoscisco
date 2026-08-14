# Plataforma de Treinamentos

Plataforma web para gestão de treinamentos: cadastro, agenda pública, inscrições,
listas de presença e emissão de certificados.

> Identidade visual institucional (azul), inspirada no portal CISCOPAR.

## Tecnologias

- **PHP 8.3** + **Laravel 13**
- **MySQL 8.4**
- **Tailwind CSS v4** + **Vite** (compilação de assets)
- Ambiente local via **Laragon**

## Como rodar (desenvolvimento)

1. **Banco de dados** — o MySQL do Laragon precisa estar ativo. O script abaixo
   sobe o MySQL automaticamente se necessário.

2. **Iniciar o servidor**:
   ```powershell
   ./iniciar-servidor.ps1
   ```
   Ou manualmente:
   ```powershell
   php artisan serve --host=127.0.0.1 --port=8000
   ```

3. Acessar:
   - Site público / agenda: <http://127.0.0.1:8000>
   - Painel administrativo: <http://127.0.0.1:8000/login>

### Papéis e acesso

A plataforma tem dois papéis:

- **Administrador Geral** (`admin`) — dashboard em `/gestao`: cadastra **usuários** (gestores), **áreas/setores** e redefine senhas. Não cadastra treinamentos.
- **Gestor de Treinamentos** (`gestor`) — painel em `/admin`, vinculado a uma área, vê e gerencia **apenas os treinamentos que ele mesmo criou**.

O login (`/login`) redireciona automaticamente conforme o papel. Usuários desativados não conseguem entrar.

### Credenciais (ambiente de desenvolvimento)

| Papel | E-mail | Senha | Área |
|---|---|---|---|
| Administrador Geral | `admin@treinamentos.gov.br` | `admin123` | — |
| Gestor | `gestor.saude@treinamentos.gov.br` | `gestor123` | Atenção à Saúde |
| Gestor | `gestor.adm@treinamentos.gov.br` | `gestor123` | Administrativo |

> ⚠️ Troque as senhas antes de colocar em produção.

## Comandos úteis

```powershell
php artisan migrate            # aplica as migrations
php artisan db:seed            # popula admin + treinamentos de exemplo
php artisan migrate:fresh --seed   # recria o banco do zero e popula
npm run build                  # compila os assets (produção)
npm run dev                    # assets em modo desenvolvimento (hot reload)
```

## Deploy em produção (HestiaCP)

Pré-requisitos (uma vez, pelo painel Hestia): usuário do painel, domínio web, versão do PHP do domínio e um banco MySQL/MariaDB criado.

> ⚠️ **Hestia + `open_basedir`:** o PHP só roda dentro do diretório do domínio. Por isso a app fica em `<domínio>/private/laravel` — clonar fora dali gera erro 500 (`vendor/autoload.php Operation not permitted`). O `deploy.sh` realoca sozinho se você clonar em outro lugar, mas o ideal é já clonar no lugar certo:

```bash
USER=sistemas; DOMINIO=treinamentos.ciscopar.com.br
mkdir -p /home/$USER/web/$DOMINIO/private
git clone https://github.com/sidneyalpagel/treinamentoscisco.git /home/$USER/web/$DOMINIO/private/laravel
cd /home/$USER/web/$DOMINIO/private/laravel
sudo HESTIA_USER=$USER DOMAIN=$DOMINIO \
  DB_DATABASE=sistemas_laravel DB_USERNAME=sistemas_lara DB_PASSWORD='...' ./deploy.sh
```

O [`deploy.sh`](deploy.sh) é idempotente e cuida de tudo: instala dependências que faltarem (composer, Node.js), configura o `.env` (pede as credenciais do banco na 1ª vez), instala pacotes PHP, compila os assets, roda migrations, recria caches, ajusta permissões, aponta o `public_html` para `public/` e recarrega o PHP-FPM. Para atualizações, basta rodar `./deploy.sh` de novo. Configuração opcional via `deploy.conf` (veja `deploy.conf.example`).

> **SSL:** com o domínio no Cloudflare em modo *proxied*, use o certificado wildcard no origin (`v-add-web-domain-ssl`) + Cloudflare em **Full (strict)**. O admin inicial (sem SMTP ainda) é criado manualmente: `sudo -u USER php artisan tinker --execute="App\Models\User::create(['name'=>'Admin','email'=>'...','password'=>'...','role'=>'admin','ativo'=>true]);"` — depois ele cadastra o SMTP e convida os gestores pelo painel.

## Estrutura (base atual)

| Área | Descrição |
|------|-----------|
| `app/Models/Treinamento.php` | Model do treinamento (status, modalidade, slug, escopos) |
| `app/Http/Controllers/Admin/` | Painel: dashboard e CRUD de treinamentos |
| `app/Http/Controllers/PublicController.php` | Site público: home, agenda e página do treinamento |
| `app/Http/Controllers/Auth/LoginController.php` | Autenticação do administrador |
| `resources/views/layouts/` | Layouts público e administrativo (tema CISCOPAR) |

## Roteiro (próximos módulos)

- [x] Base: ambiente, banco, autenticação, tema visual
- [x] Cadastro e gestão de **Treinamentos** (CRUD)
- [x] **Agenda** pública de treinamentos
- [x] **Inscrições** (formulário público + gestão das listas + exportação CSV)
- [x] **Lista de presença** por sessão (auto check-in público + gestão + CSV)
- [x] **Certificados** por participante (emissão + impressão/PDF + validação pública)

## Observações de ambiente

- O projeto **não deve** ficar dentro do Google Drive: a sincronização trava o
  `npm install` (erros `EBADF`) e não permite `junction`. Mantê-lo em disco local
  (`C:\dev\PlataformaTreinamento`).
- `vendor/` e `node_modules/` não vão para o Git (ver `.gitignore`); no servidor,
  rode `composer install` e `npm ci && npm run build`.
