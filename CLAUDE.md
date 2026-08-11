# CLAUDE.md

Este arquivo orienta o Claude Code (claude.ai/code) ao trabalhar com o código deste repositório.

## Visão Geral do Projeto

Esta é uma implantação customizada do **GLPI v10.0.18** (gerenciamento de serviços de TI open-source) para a organização Listados. O código-fonte do GLPI fica em `glpi/` e é servido diretamente via volume Docker, então edições em `glpi/` entram em vigor sem reconstruir a imagem.

## Executando o Projeto

```bash
cp .env.example .env   # apenas na primeira vez — preencha as credenciais do banco e o virtual host
docker compose up -d --build
```

A aplicação fica disponível em `http://localhost:8080`. Na primeira inicialização do container, o `init.sh` copia o código da imagem para o volume montado e cria os subdiretórios necessários em `glpi/files/`.

## Arquitetura

| Caminho | Finalidade |
|---|---|
| `glpi/` | Código-fonte completo do GLPI (PHP). Montado como volume no container em `/var/www/html`. |
| `glpi/src/` | Classes PSR-4 do GLPI (namespace `Glpi\*`). Lógica de negócio principal. |
| `glpi/front/` | Páginas PHP de entrada da UI (um arquivo por página). |
| `glpi/ajax/` | Scripts de endpoints AJAX. |
| `glpi/css_compiled/` | CSS minificado pré-compilado para todos os temas do GLPI (um arquivo por paleta). |
| `glpi/templates/` | Templates Twig. |
| `glpi/config/` | Configuração de runtime (`config_db.php`, `glpicrypt.key`). Ignorado pelo git; gerado pelo instalador. |
| `glpi/files/` | Dados de runtime (`_sessions`, `_cache`, `_log`, etc.). Ignorado pelo git. |
| `logos/` | Logos customizados da Listados. Copiados para `glpi/pics/logos/` durante o build da imagem Docker. |
| `config/000-default.conf` | Vhost do Apache — DocumentRoot em `glpi/public/`, todas as requisições redirecionadas para `index.php`. |
| `init.sh` | Entrypoint do container: cópia dos arquivos na primeira execução + criação de diretórios, depois `apache2-foreground`. |

## Customizações Aplicadas ao GLPI

Estas são as alterações intencionais feitas sobre o código upstream do GLPI:

### 1. Cores dos Status ITIL (`glpi/css_compiled/css_palettes_*.min.css`)

Todos os arquivos de paleta foram editados para sobrescrever as classes CSS `.itilstatus` e `.validationstatus`:

| Status | Cor |
|---|---|
| `new` | Amarelo `#FFFF00` |
| `assigned` | Vermelho `#FF0000` |
| `solved` | Verde `#008000` |
| `closed` | Cinza `#808080` |

Ao atualizar as cores dos status, **todos os arquivos de paleta** (`css_palettes_flood.min.css`, `css_palettes_auror.min.css`, etc.) devem ser atualizados de forma consistente.

### 2. Supressão de Notificações (`glpi/src/NotificationTargetCommonITILObject.php`)

O case de notificação `FOLLOWUP_AUTHOR` (~linha 965) está comentado para impedir que o autor do followup receba sua própria notificação.

## Notificações por E-mail (fluxo e troubleshooting)

Configuração validada em produção (helpdesk.nomades.listados.com.br) em 2026-07-06.

### Como funciona o envio

- O botão **"Enviar um e-mail de teste para o administrador"** (Configurar → Notificações) envia direto via SMTP, **sem passar pela fila** (`glpi/src/NotificationMailing.php`).
- Notificações de ticket (novo ticket, followup, etc.) entram na fila `glpi_queuednotifications` e só são enviadas quando a ação automática **`queuednotification`** roda (`glpi/src/QueuedNotification.php::cronQueuedNotification`).
- Cada notificação é segurada por **pelo menos 1 minuto** na fila antes do envio. Com o cron a cada 5 minutos, o atraso normal de entrega é de **até ~6 minutos** — não é defeito.

### Configuração atual em produção

- Ação automática `queuednotification`: modo **CLI**, frequência **5 minutos**, status Agendado.
- A coleta de e-mails (`mailgate`) e a fila dependem do cron CLI (`php /var/www/html/front/cron.php`) rodando no servidor.

### Checklist de troubleshooting (nesta ordem)

1. **Fila** (`/front/queuednotification.php`): se o e-mail **não aparece** na fila, o problema é na configuração da notificação ou do ator; se aparece e **fica preso**, o problema é no envio (cron/SMTP).
2. **Cron** (Configurar → Ações automáticas → `queuednotification`): verificar os logs de execução da ação.
3. **Global**: Configurar → Notificações → "Habilitar acompanhamento via e-mail" = Sim.
4. **Evento**: Configurar → Notificações → Notificações → "Ticket novo" ativa, com "Requerente" na aba Destinatários.
5. **Ator do ticket**: o requerente precisa estar com "Acompanhamento por e-mail" = Sim e e-mail preenchido (o padrão vem das preferências do usuário).

## Documentação de Referência

| Recurso | URL |
|---|---|
| Developer Documentation | https://glpi-developer-documentation.readthedocs.io/en/master/index.html |
| Advanced Help | https://help.glpi-project.org/documentation/advanced |
| REST API Reference | https://glpi-rest-api.netlify.app/ |

## Variáveis de Ambiente (`.env`)

```
DB_ROOT_PASSWORD=   # senha root do MySQL
DB_NAME=glpi
DB_USER=glpi
DB_PASSWORD=glpi
VIRTUAL_HOST=       # ex: suporte.listados.com.br
LETSENCRYPT_HOST=   # igual ao VIRTUAL_HOST
LETSENCRYPT_EMAIL=  # e-mail para renovação do certificado
```

A rede `nginx-proxy` está definida como interna (`external: false` por padrão). Mude para `external: true` ao usar um nginx-proxy externo com acme-companion para TLS.
