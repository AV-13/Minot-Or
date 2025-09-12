# Plan de Test - Application Minotor

## Vue d'ensemble

Ce document présente la stratégie de test complète pour l'application Minotor, couvrant tous les types de tests implémentés pour garantir la qualité et la fiabilité du système.

## 1. Tests Unitaires

**Objectif** : Vérifier le bon fonctionnement des composants isolés  
**Couverture** : Service `SecurityHelper`  
**Technologies** : PHPUnit avec mocks

### Tests implémentés :
- `testGetUserReturnsUser()` - Vérifie le retour d'un utilisateur authentifié
- `testGetUserThrowsExceptionIfNotAuthenticated()` - Vérifie la gestion des utilisateurs non authentifiés
- `testHasRoleReturnsTrueIfRoleMatches()` - Vérifie la validation des rôles utilisateur
- `testCalculateTotalAmountUnit()` - Vérifie le calcul du montant total avec mocks

## 2. Tests d'Intégration

**Objectif** : Vérifier l'interaction entre plusieurs couches  
**Couverture** : Controller + Service + Repository + Base de données  
**Infrastructure** : MySQL réelle, Symfony complet

### Tests implémentés :
- `testCreateQuotationIntegration()` - Création de devis avec vérification en base
- `testCreate()` - Test complet de création d'entité
- `testUpdate()` - Test de mise à jour avec persistance

## 3. Tests Fonctionnels

**Objectif** : Valider les parcours utilisateur complets  
**Couverture** : Workflows métier de bout en bout  
**Scénarios** : Création → Paiement → Vérification

### Tests implémentés :
- `testQuotationLifecycleFunctional()` - Cycle de vie complet d'un devis
- `testAdminList()` - Parcours administrateur avec filtres
- `testList()` - Navigation utilisateur standard

## 4. Tests de Validation

**Objectif** : Contrôler les règles de validation  
**Couverture** : Champs obligatoires, formats de données

### Tests implémentés :
- `testCreateQuotationValidation()` - Validation des champs obligatoires
- `testCreateWithExistingQuotation()` - Gestion des doublons

## 5. Tests de Sécurité

**Objectif** : Vérifier les contrôles d'accès  
**Couverture** : Authentification, autorisation par rôle

### Tests implémentés :
- `testUnauthorizedAccess()` - Accès sans token
- Tests de rôles - Vérification des permissions ROLE_SALES vs ROLE_USER

## 6. Tests de Performance

**Objectif** : Valider les temps de réponse  
**Couverture** : Pagination, gros volumes de données

### Tests implémentés :
- `testAdminListPerformance()` - Test de charge avec 100 devis
- Pagination efficace avec limite de 2 secondes

## 7. Tests de Régression

**Objectif** : Détecter les régressions sur les cas d'erreur  
**Couverture** : Gestion des données manquantes

### Tests implémentés :
- `testNoPricingAvailableRegression()` - Gestion de l'absence de tarification

## 8. Tests CRUD Complets

**Objectif** : Vérifier toutes les opérations de base  
**Couverture** : Create, Read, Update, Delete

### Tests implémentés :
- `testDetail()` - Lecture d'un devis
- `testUpdate()` - Modification d'un devis
- `testDelete()` - Suppression d'un devis
- `testGetUserQuotations()` - Requêtes spécialisées

## Infrastructure d'Exécution

### Environnement de Test
- **Base de données** : `minotor_test` (MySQL 8) isolée de la production
- **PHP** : Version 8.3 avec extensions pdo_mysql, intl, mongodb
- **Framework** : Symfony 6 complet avec sécurité JWT

### CI/CD GitHub Actions
- **Déclenchement** : Push/PR sur branche `main` avec modifications dans `api/`
- **Services** : MySQL en conteneur avec health checks
- **Étapes** :
    1. Configuration de l'environnement de test
    2. Génération des clés JWT
    3. Création du schéma de base
    4. Chargement des fixtures
    5. Exécution de tous les tests

### Métriques de Qualité
- **Couverture** : Tests sur 7 types différents
- **Isolation** : Base de données séparée pour chaque test
- **Performance** : Seuil de 2 secondes pour les requêtes paginées
- **Fiabilité** : Nettoyage automatique après chaque test

## Commandes d'Exécution

```bash
# Tests complets
cd api && php bin/phpunit

# Tests spécifiques
php bin/phpunit tests/Controller/Api/QuotationControllerTest.php --testdox

# Avec couverture
php bin/phpunit --coverage-html coverage/
