# Documentation des Tests - Projet Minotor

## Vue d'ensemble

Ce document explique la configuration et l'utilisation des tests pour le projet Minotor. Nous avons mis en place une base de données MySQL dédiée aux tests pour garantir l'intégrité des données de production.

## Configuration de la Base de Données de Test

### 1. Base de Données Séparée

La configuration utilise une base de données MySQL séparée nommée `minotor_test` pour isoler complètement les tests des données de production.

**Configuration dans `.env` :**
```env
DATABASE_TEST_URL="mysql://root:root@127.0.0.1:3307/minotor_test?serverVersion=8.0"
```

```### 2. dans config/packages/test/doctrine.yaml

```yaml
doctrine:
  dbal:
    url: '%env(resolve:DATABASE_TEST_URL)%'
```
Puis exécuter les commandes suivantes pour lancer les tests:
```
# Recréer la base de test
php bin/console doctrine:database:drop --env=test --force
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:create --env=test

# Exécuter les tests
php bin/phpunit tests/Controller/Api/QuotationControllerTest.php --testdox
```



