#!/bin/bash
# Usage: ./init-certbot.sh yourdomain.com
DOMAIN=$1
EMAIL=$2

if [ -z "$DOMAIN" ] || [ -z "$EMAIL" ]; then
  echo "Usage: $0 <domain> <email>"
  exit 1
fi

docker compose run --rm certbot certonly --webroot --webroot-path=/var/www/certbot \
  --email "$EMAIL" --agree-tos --no-eff-email -d "$DOMAIN"

echo "Certificates for $DOMAIN have been generated."
