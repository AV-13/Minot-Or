# Checklist de Couverture API — Projet Minot'Or

Cette checklist t’aide à vérifier que ton API répond à tous les besoins du cahier des charges, entité par entité.  
Utilise-la pour boucler les “dernières routes” avant la livraison, la soutenance ou les tests.

---

## 1. Vérification des entités majeures

Pour chaque entité, assure-toi d’avoir :

- **CRUD complet** (Create, Read/List, Update, Delete)
- **Routes métier spécifiques** (ex : assignation, filtrage, état, rôle…)
- **Vérifications d’intégrité** (erreur 409 si contrainte SQL, etc.)

---

### A. Utilisateurs (User)
- CRUD (avec gestion des rôles)
- Authentification, modification du profil, changement de mot de passe
- Assignation à une entreprise

### B. Entreprises (Company)
- CRUD
- Validation de l’unicité du SIRET

### C. Catégories de produits (Category)
- CRUD

### D. Produits (Product)
- CRUD + assignation à un entrepôt, une catégorie, un fournisseur
- Gestion du stock

### E. Entrepôts (Warehouse)
- CRUD + consultation du stock

### F. Fournisseurs (Supplier)
- CRUD + association produits-fournisseurs

### G. Commandes/Devis (SalesList)
- CRUD + ajout/retrait de produits

### H. Livraison (Delivery)
- CRUD + assignation à une commande, un camion, QR code, remarque livreur

### I. Camions (Truck)
- CRUD + assignation à un entrepôt, disponibilité, historique des nettoyages

### J. Réapprovisionnement (Restock)
- CRUD + suivi du statut

### K. Facture (Invoice)
- CRUD

### L. Evaluation (Evaluate)
- Accepter/refuser un devis

### M. Nettoyage camions (TruckCleaning + Clean)
- CRUD, liaison à un camion

---

## 2. Points souvent oubliés dans un projet CDA

- Gestion des rôles et permissions (routes protégées, accès REST sécurisé)
- Notifications/alertes (ex : seuils de stock, commandes à traiter…)
- Export de données (CSV, PDF, etc.)
- Statistiques/dashboard (produits les plus vendus, historiques, etc.)

---

## 3. Bonnes pratiques de fin de projet

- **Documente tes routes**
    - Swagger/OpenAPI OU collection Postman bien remplie
- **Teste les cas d’erreurs**
    - Suppression d’entités liées (blocage sur FK)
    - Tentative d’ajout en double (unicité)
    - Permissions/rôles (403, etc.)

---

## 4. Derniers conseils

- Priorise les entités où il te manque du CRUD ou de la logique métier
- N’hésite pas à faire un “exemple type” de controller RESTful par entité
- Pense à ajouter un bonus dashboard ou statistiques pour la présentation finale

---

**Besoin d’un exemple de controller, d’un flux de route ou d’un squelette .md pour une entité précise ?  
Tu peux compléter ou adapter cette checklist à ton projet.**

---
