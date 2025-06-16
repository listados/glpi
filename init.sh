#!/bin/bash
set -e

GLPI_PATH="/var/www/html"
GLPI_SRC="/opt/glpi-src"

# Se index.php não existir no volume montado, considera que está vazio
if [ ! -f "$GLPI_PATH/index.php" ]; then
  echo "[init] Instalando GLPI no volume em $GLPI_PATH..."
  cp -R "$GLPI_SRC/"* "$GLPI_PATH/"
  chown -R www-data:www-data "$GLPI_PATH"
else
  echo "[init] GLPI já existe em $GLPI_PATH, não copiando novamente."
fi

# Diretórios que precisam existir e ter permissão dentro do volume
DATA_DIR="$GLPI_PATH/files"

DIRS=(
  "_cache"
  "_cron"
  "_dumps"
  "_graphs"
  "_lock"
  "_pictures"
  "_plugins"
  "_rss"
  "_sessions"
  "_tmp"
  "_uploads"
)

for dir in "${DIRS[@]}"; do
  if [ ! -d "${DATA_DIR}/${dir}" ]; then
    mkdir -p "${DATA_DIR}/${dir}"
    echo "[init] Criado diretório: ${DATA_DIR}/${dir}"
  fi
done

# Diretório marketplace fora da pasta files
MARKETPLACE_DIR="$GLPI_PATH/marketplace"
if [ ! -d "$MARKETPLACE_DIR" ]; then
  mkdir -p "$MARKETPLACE_DIR"
  echo "[init] Criado diretório: $MARKETPLACE_DIR"
fi

# Ajustar dono e permissões para www-data
chown -R www-data:www-data "$DATA_DIR"
chmod -R 775 "$DATA_DIR"

chown -R www-data:www-data "$MARKETPLACE_DIR"
chmod -R 775 "$MARKETPLACE_DIR"

chmod -R 777 "$GLPI_PATH/config"
echo "[init] Permissões 777 aplicadas à pasta de configuração: $GLPI_PATH/config"

# Executa o Apache em foreground
exec apache2-foreground
