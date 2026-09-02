#!/bin/sh
set -e

# O codigo e montado do host (src/, dono lucas), mas o php-fpm atende as
# requisicoes como www-data. Garantimos que ele consiga escrever nos diretorios
# de runtime (views compiladas do Blade/Livewire, caches e logs) toda vez que o
# container sobe -- assim o deploy nunca mais quebra com 500 de permissao.
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

exec docker-php-entrypoint "$@"
