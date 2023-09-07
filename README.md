# Helix

## Docker

This repository is equipped with ready-to-use `Dockerfile`, `docker-compose.yml` and some other DevOps configs. You can change them to fit for your purposes (adding/removing packages, setting listening ports, `PHP` and `NginX` configuration options).

> Please note that `./runtime` directory is excluded in the `.dockerignore` for letting users link actual config files by orchestration mechanisms (such as `docker-compose` or `Kubernetes`).
> the `./.devops/docker/filesystem/app/runtime` directory is useful for storing and linking of the default or common application settings

## Installation

Run `docker exec -it helix /bin/bash && cd /app && php ./index.php install`