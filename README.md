# personaldef

Site do Personal DEF (Denilson Ferreira), em Laravel 13 + PHP 8.5 (Docker).

## Rodando local

```sh
cp .env.example .env
docker compose up -d --build
docker exec -it php8_personaldef composer install
docker exec -it php8_personaldef php artisan key:generate
docker exec -it php8_personaldef php artisan migrate
```

Site em http://localhost:8086

Textos, contatos, serviços e posts ficam em `config/site.php`.
