# Version Finale du TI (Sym64Docker)


### Après avoir cloné le repository, exécutez les commandes suivantes  pour le version Symfony :- 

```bash

composer install
php bin/console d:d:c
php bin/console d:m:m -n
php bin/console d:f:l -n
symfony serve -d
```

### Pour le version Docker, fait ceci :-

Renommer les fichiers suivants :

- compose.override.yaml => compose.override.yaml.bak
- compose.yaml => compose.yaml.bak
- docker-compose.yaml.bak => docker-compose.yaml
- Dockerfile.bak => Dockerfile
```
docker-compose up -d
```