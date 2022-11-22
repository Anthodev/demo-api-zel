![CI](https://github.com/anthodev/demo-api-zel/workflows/CI/badge.svg)

## Getting Started

1. Copy `.env.example` to `.env` ou run the command `make env` if you have `make` installed on your machine
2. Run the container: `docker-compose up -d` or `make up` if you have make installed on your machine
3. Run the command inside the container: `make install-project`
4. Open the browser at [https://localhost](https://localhost) and accept the self-signed certificate

If you want to stop the container, run this command: `make prune`
