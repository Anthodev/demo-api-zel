![CI](https://github.com/anthodev/demo-api-zel/workflows/CI/badge.svg)

## Requirements
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

### Outside of Docker
- PHP 8.1
- Composer ^2
- PostgreSQL 14

## Getting Started

1. Copy `.env.example` to `.env` ou run the command `make env` if you have `make` installed on your machine
2. Fill the blank fields in `.env` file
3. Run the container: `docker-compose up -d` or `make up` if you have make installed on your machine
4. Run the command inside the container: `make install-project`
5. Open the browser at [https://localhost](https://localhost) and accept the self-signed certificate

If you want to stop the container, run this command: `make prune`

## Routes
### USER
```
GET /user
GET /user/{uuid}
POST /user
POST /register
PATCH /user/{uuid}
DELETE /user/{uuid}
```

### ARTICLE
```
GET /article
GET /article/{uuid}
GET /article/{status}/list
POST /article
PATCH /article/{uuid}
PATCH /article/{uuid}/{status}
DELETE /article/{uuid}
```
