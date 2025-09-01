<p align="center"><a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# 📦 API de Controle de Produtos

A **API de Controle de Produtos** é um sistema de gestão de inventário voltado para empresas que precisam controlar seus produtos de forma eficiente.  
Ela permite cadastro, atualização, visualização e exclusão de produtos, além de oferecer:

- Filtragem por categorias.  
- Controle de estoque em tempo real.  
- Gerenciamento de movimentações de estoque.  

Ideal para sistemas de **e-commerce** e aplicações que necessitam de controle de inventário robusto.

---

## 🚀 Funcionalidades

- **Autenticação:** sistema de login/registro com **Laravel Sanctum**.  
- **Gerenciamento de Usuários:** CRUD completo com soft deletes.  
- **Filtros Avançados:** sistema de consultas flexíveis por parâmetros.  
- **Validações:** robustas em todas as operações.  
- **API RESTFul:** endpoints padronizados com documentação Swagger.  

### Entidades Principais
- **Categorias (Category):** CRUD completo com soft deletes.  
- **Produtos (Products):** CRUD completo com soft deletes e filtros por nome, status e SKU/código.  
- **Movimentações de Estoque (StockMovements):**  
  - CRUD completo de movimentações.  
  - Tipos:  
    - Entrada → compra, reposição.  
    - Saída → venda, baixa, média, alta.  
  - Consulta de saldo atual por produto em tempo real.  
  - Filtros por produto, categoria, status e quantidade movimentada.  

---

## 🛠️ Como Rodar o Projeto Localmente

### 1. Instale as dependências
```bash
composer install

## Configure o seu ambiente
cp .env.example .env
php artisan key:generate

## Edite o arquivo .env com suas credenciais do PostgreSQL:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ControleDeProdutos
DB_USERNAME=postgres
DB_PASSWORD=secret


## Execute migrations e seeders:
php artisan migrate
php artisan db:seed

## Rodando localmente (sem Docker)
php artisan serve
A API estará disponível em: http://localhost:8000

## Rodando com Docker:
docker-compose up -d --build

## 📑 Documentação da API
Acesse a documentação interativa com Swagger:
👉 http://localhost:8000/api/documentation


## 🧰 Tecnologias Utilizadas

Backend: Laravel 12.x

Banco de Dados: PostgreSQL 17

Autenticação: Laravel Sanctum

Documentação: Swagger (l5-swagger)

Containerização: Docker e Docker Compose

Ambiente local: WAMP Server (alternativa ao Laravel Herd)

Testes de Endpoints: Postman / Insomnia

## 📋 Pré-requisitos:

## Para Docker

Docker Desktop

Docker Compose

## Para instalação local

PHP >= 8.3

Composer

PostgreSQL 17 + extensões pdo_pgsql, mbstring

Git

WAMP (opcional)

Arquivo .env configurado corretamente

## 📂 Filtros disponíveis:
User: id, nome, status, role, email

Product: id, nome, categoria, status, sku

Category: id, nome, status

StockMovements: id, produto_id, tipo (entrada|saida), quantidade, data, status
##⚙️ Comandos Úteis

# Docker: 
# Iniciar containers 
docker-compose up -d

# Ver logs
docker-compose logs -f app

# Executar comandos artisan
docker-compose exec app php artisan [comando]

# Parar containers
docker-compose down

## Local:
# Iniciar servidor
php artisan serve

# Rodar migrations
php artisan migrate

# Rodar seeders
php artisan db:seed

# Listar rotas
php artisan route:list


## 🤝 Contribuindo:
Se você quiser contribuir para o projeto:

Faça um fork.

Crie uma branch (git checkout -b minha-feature).

Commit suas alterações (git commit -m 'Minha feature').

Push para o repositório (git push origin minha-feature).

Abra um Pull Request.