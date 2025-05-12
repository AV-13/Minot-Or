# Mise en place du JWT local avec LexikJWTAuthenticationBundle

Ce guide explique comment générer et configurer les clés JWT pour faire fonctionner l'authentification dans le projet Minot'Or (Symfony 7.2 + PHP 8.3).

---

## 1. Prérequis

- PHP 8.3
- Symfony installé
- OpenSSL disponible (`php -m` doit contenir `openssl`)
- Extension `sodium` activée (voir `php.ini` → `extension=sodium`)

---

## 2. Génération manuelle des clés JWT

Depuis la racine du projet :

```bash
mkdir -p config/jwt
cd config/jwt

# Génère la clé privée
openssl genrsa -out private.pem 4096

# Génère la clé publique
openssl rsa -pubout -in private.pem -out public.pem
