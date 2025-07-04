# Projet fil rouge

# CDA 2025

# Minot’Or

# Cahier des charges


## TABLE DES MATIERES

- Section 1 Contexte du projet.....................................................................................................
    - 1.1 Avertissement...............................................................................................................................
    - 1.2 Notre société.................................................................................................................................
    - 1.3 Implantation géographique........................................................................................................
    - 1.4 Quelques données du groupe......................................................................................................
    - 1.5 La motivation de ce projet informatique...................................................................................
- Section 2 Le projet.....................................................................................................................
    - 2.1 Finalité du projet.........................................................................................................................
    - 2.2 Nos prestations.............................................................................................................................
    - 2.3 Nos clients et fournisseurs...........................................................................................................
        - 2.3.1 Nos fournisseurs : Les minoteries et moulins artisanaux......................................................................
        - 2.3.2 Les boulangers.......................................................................................................................................
    - 2.4 Nos collaborateurs.....................................................................................................................
        - 2.4.1 Le service commercial.........................................................................................................................
        - 2.4.2 Le service approvisionnement.............................................................................................................
        - 2.4.3 Le service préparation des commandes pour une boulangerie............................................................
            - 2.4.3.1 Réception d’une commande client boulangerie...........................................................................
        - 2.4.4 Nos livreurs..........................................................................................................................................
        - 2.4.5 Personnel de maintenance....................................................................................................................
- Section 3 Mesure de performances.........................................................................................
    - 3.1 Finalité........................................................................................................................................
- Section 4 Annexe : Calcul du prix d’une livraison................................................................
    - 4.1 Principe.......................................................................................................................................
        - 4.1.1 Transport direct depuis une minoterie vers une boulangerie...............................................................
            - 4.1.1.1 transport en vrac avec un camion monocuve directement chez le client boulanger....................
            - 4.1.1.2 Transport en palettes minoterie → Boulanger.............................................................................
        - 4.1.2 Transport depuis l’un de nos entrepôts vers une boulangerie..............................................................
- Section 5 Quelques cas d’utilisation.......................................................................................
    - 5.1 Je suis boulanger, que puis-je faire avec l’application Minot’Or ?......................................
    - 5.2 Je suis minotier, que puis-je faire avec l’application Minot’Or ?.........................................
    - commande......................................................................................................................................... 5.3 Je suis commercial, qu’ai je à faire quand une boulangerie a acquitté le payement d’une


```
Versions du document
```
```
Version Date Raison
```
V1.1 01/10/2024 Création


## Section 1 Contexte du projet.....................................................................................................

### 1.1 Avertissement...............................................................................................................................

```
Ce projet est complètement fictif!
Le rédacteur – votre formateur – n’a aucune compétence dans le métier des
minoteries et boulangeries. Il a simplement imaginé un projet d’envergure qui
couvre tous les items de la formation CDA.
Chaque équipe peut ajouter/modifier des fonctionnalités proposées avec accord du
client – le formateur.
```
### 1.2 Notre société.................................................................................................................................

```
La société Minot’Or a été créée en 2005 et se place comme intermédiaire entre des
minoteries (meniers industriels) et les boulangeries artisanales et industrielles.
Grâce à son importante flotte de véhicules elle livre quotidiennement les
boulangeries françaises et belges.
La société dispose de plusieurs entrepôts pour le stockage des matériaux, et a pour
objectif de limiter l’empreinte carbone en privilégiant les déplacements locaux.
```

```
Un autre point fort de notre société est d’avoir installé un réseau de transport pour la
reprise de pains invendus pour les collecter et les distribuer pour ré-utilisation dans
les minoteries et brasseries.
```
### 1.3 Implantation géographique........................................................................................................

```
La société a son siège social à Nanterre. Ce site regroupe un entrepôt ainsi que le
personnel administratif. Les locaux permettent d’accueillir le système informatique
de l’entreprise.
40 autres entrepôts existent en France et en Belgique.
```
### 1.4 Quelques données du groupe......................................................................................................

- En expansion régulière depuis 2005
- chiffre d’affaire annuel de 10 M €
- 400 collaborateurs
- plus de 8000 clients depuis 2005
- 450 véhicules


### 1.5 La motivation de ce projet informatique...................................................................................

```
Les applications numériques existantes sont un site Web pour les fournisseurs et
les clients et une application Desktop/de bureau pour le back office.
Les chauffeurs/livreurs réalisent leurs déplacement depuis un bon de mission
papier.
```
```
Le système a été adapté depuis sa création mais arrive à son terme : maintenance
complexe et coûteuse, parties obsolescentes ...
```
```
Le projet à réaliser consiste à re-créer de toute pièce une application web, mobile,
et de bureau pour centraliser le processus d’achat et de vente de la société avec
ces applications.
Les codes sources du système existant appartiennent à un prestataire externe et ne
seront pas accessibles.
```

## Section 2 Le projet.....................................................................................................................

### 2.1 Finalité du projet.........................................................................................................................

```
Nous souhaitons disposer d’applications numériques pour permettre à nos clients
boulanger l’achat de produits de boulangerie. Ces applications permettront donc aux
boulangers de commander des produits, de planifier les livraisons des produits.
Ces applications seront utilisées aussi en interne pour assurer la gestion de cette
activité, en particulier l’approvisionnement du stock par entrepôt, la démarche
commerciale, la fourniture aux clients, la gestion des livraisons.
```
```
Nous souhaitons disposer de 3 types d’applications :
```
- une application Web pour les clients et les acteurs internes de l’entreprise
  (Back office)
- une application native mobile pour les clients et les livreurs
- une application fixe desktop utilisée en interne pour les mesures de
  performances du système

```
Dans la suite du document chaque application est mentionnée en fonction des
utilisateurs.
```
### 2.2 Nos prestations.............................................................................................................................

```
Minot’Or est avant tout une société de transport avec des véhicules spécialisés
dans le transport de produits de minoteries.
```
```
Nous sommes l’intermédiaire entre le moulin/minoterie et l’artisan/l’industriel
boulanger.
Nous achetons des produits des minoteries pour une livraison directe chez les
clients (pour le vrac), ou nous les stockons en nos lieux de stockage en France et
en Belgique pour les produits fournis sur palette (sacs de farines ...).
Nous collectons le pain invendu pour le revendre à des brasseries et minoteries.
```

### 2.3 Nos clients et fournisseurs...........................................................................................................

```
Ils sont actuellement exclusivement en France métropolitaine et en Belgique.
Les applications seront donc développées avec, pour l’instant, la langue française.
Toute proposition d’une solution avec un support multilingue sera appréciée (mais
non obligatoire).
```
#### 2.3.1 Nos fournisseurs : Les minoteries et moulins artisanaux......................................................................

```
Ils n’ont pas accès à notre application Web car la tentative par le passé a échoué :
chaque minoterie à son propre site Web marchand qui permet de leur passer des
commandes. Ils n’ont pas d’intérêt à s’inscrire sur notre site.
```
```
De ce fait ce sont nos responsables approvisionnement qui sont en charge de cette
gestion.
```
```
Chaque minoterie a ses spécialités et ne produit pas forcément l’ensemble des
produits proposés sur le site de Minot’Or.
Le transport que nous prenons en charge depuis la minoterie est de plusieurs
natures :
```
- transport en vrac avec un camion monocuve directement chez le client
  boulanger. C’est alors le client boulanger qui paye la prestation de transport.
  (voir le calcul en annexe)


- transport de palettes avec un camion porteur de palettes directement chez
  le client boulanger. C’est alors le client boulanger qui paye la prestation de
  transport.
- transport de palettes avec un camion porteur de palettes dans les entrepôts
  Minot’Or.

```
Minot’Or achète alors les marchandises aux minotiers.
A ce stade peut-être aucun client boulanger n’est identifié pour l’achat de
ces marchandises.
Minot’Or s’occupe ensuite de la gestion du stock, de proposer un catalogue
de produits sur son site et de la livraison chez un futur client boulanger.
Transport vers la minoterie :
Minot’Or dispose d’un service de collecte de pain invendu auprès des boulangers.
Ces pains sont regroupés et revendus à la minoterie qui les transforme à nouveau
en « farine ».
```
#### 2.3.2 Les boulangers.......................................................................................................................................

```
Il s’agit ici d’artisans boulanger et de boulangers industriels.
Ils auront accès à l’application Web.
Lors de son inscription sur le site, le représentant de l’entreprise doit fournir :
```
```
 ses nom, prénom, fonction
 les coordonnées de l’entreprise
```
```
 la raison sociale (le nom de la société)
 le siret de la société
L’application lui permet :
```
- de consulter l’ensemble des produits mis à disposition par les minotiers
- de demander un devis pour divers marchandises
- d’accepter un devis validé par un commercial de Minot’Or
- de choisir une date de livraison fixe ou cyclique
- d’indiquer qu’il a du pain invendu disponible


### 2.4 Nos collaborateurs.....................................................................................................................

#### 2.4.1 Le service commercial.........................................................................................................................

```
Tout commercial de Minot’Or a pour mission :
```
- de gérer dans l’application Web les fournisseurs minotiers
  (ajout/modification/suppression)
  L’ajout d’un minotier nécessite :
  ◦ nom, prénom, fonction du contact de l’entreprise, adresse mail ...
  ◦ les coordonnées de l’entreprise
  ◦ la raison sociale (le nom de la société)
  ◦ le siret de la société
- d’enregistrer dans l’application web l’ensemble des types de produits que
  délivrent les minotiers, que le stock existe ou est à venir.
- de répondre aux demandes de devis des boulangers. Un devis accepté par
  le client boulanger devient alors une commande. La date de livraison
  souhaitée est indiquée.

```
Ce service traite des ristournes qui peuvent être accordées à un client boulanger
lorsque celui-ci établit une demande de devis (vu ci avant).
Il dispose uniquement de l’application Web. Cette application lui permet :
 de regarder les demandes de devis en attente de traitement, en cours de
traitement et traités
 de regarder les historiques de commandes des clients pour juger de la
ristourne
 d’accorder une ristourne. La ristourne peut être décidée globale (exemple
10 % pour tous les articles) ou par article.
 De calculer automatiquement les frais de livraison en fonction du poids, du
volume et de la distance à parcourir. Une annexe décrit ce calcul métier.
```
```
Lorsque le devis est clôturé dans l’application, le client devrait recevoir un mail.
Cette version ne la demande pas. C’est au client de consulter le site et de regarder
l’état de ses devis.
```
```
C’est aussi ce service qui détermine le prix de vente des matériaux stockés chez
Minot’Or en appliquant une marge :
prix d’achat HT + marge = prix de vente HT
Une marge par défaut de 20 % est appliquée pour l’ensemble des produits. Cette
marge doit pouvoir être ajustée par article.
```

#### 2.4.2 Le service approvisionnement.............................................................................................................

```
La mission de ce service est de contrôler l’état des stocks dans les divers entrepôts
Minot’Or afin d’éviter toute rupture de stock.
Ce service connaît et gère les fournisseurs minotiers.
Il dispose uniquement de l’application Web. Cette application lui permet :
```
- de connaître l’état des stocks par entrepôt (pour rappel il y a 41 entrepôts)
- d’enregistrer les types de produit par fournisseur minotier (le commercial le
  peut aussi)
- De renseigner une demande de transport depuis un fournisseur. Un bon de
  transport est établi dans l’application et sera utilisé par le chauffeur. Ce bon
  contient :
  ◦ le nom et coordonnées du fournisseur
  ◦ le no de commande émise par le fournisseur (Minot’Or est client du
  fournisseur, on a utilisé leur site ou commercial pour leur commander de
  la marchandise)
  ◦ la date prévue du transport
  ◦ les types de produits et quantité à transporter
  ◦ le type de camion à utiliser
  ◦ le no du camion. Le camion choisi doit appartenir à l’entrepôt le plus
  proche pour limiter l’impact écologique.
  ◦ La destination : soit un des entrepôts, soit directement chez un client
  boulanger enregistré dans l’application
- De montrer l’état des demandes de transport. Sur réception d’une livraison
  pour un entrepôt, actualiser dans l’application les stocks pour le bon
  entrepôt.
  La réception des produits, suite à la livraison d’un fournisseur minotier, consiste à
  réaliser les tâches suivantes :

```
 depuis l’application Web, retrouver la commande fournisseur, vérifier que la
livraison correspond à la commande et la faire passer à l’état livrée. Le stock
de l’entrepôt concerné évolue en conséquence.
 Il peut arriver qu’une commande soit incomplète. L’application doit permettre
d’ajuster les quantités de produits effectivement reçues.
```

#### 2.4.3 Le service préparation des commandes pour une boulangerie............................................................

```
Les personnes de ce service sont physiquement dans les entrepôts. Elles gèrent la
réception des produits, et contrôlent le chargement des camions pour une livraison
chez un client boulanger.
Vis à vis de l’application certaines personnes auront accès aux mêmes choses que
pour le service approvisionnement.
```
##### 2.4.3.1 Réception d’une commande client boulangerie...........................................................................

```
La réception d’une commande client boulanger arrive dans ce service dès que le
client a accepté un devis proposé par notre service commercial. Le devis indique
une date de livraison souhaitée.
A l’échéance de cette date, les personnes du service préparation vont chercher les
produits, les placer sur palette(s) et remplir le camion.
```
```
Lorsque la commande est complète (on ne gère pas dans cette version les
commandes incomplètes) un bordereau de livraison existe. Un QR Code est
généré. Il contient la référence de ce bordereau de livraison et sera utilisé lors de la
livraison. Le QR code est imprimé et est collé sur le colis/palette.
Toute commande qui ne peut pas aboutir (il manque des articles) est mise en
attente (n’est pas commencée) le temps qu’un fournisseur ré approvisionne le stock.
```
#### 2.4.4 Nos livreurs..........................................................................................................................................

```
L’expédition en camion est prise en charge par nos propres livreurs.
C’est l’application mobile qui est utilisée ici. Nous disposerons de terminaux sous
Android avec lecteur de QR code. Ce matériel se comporte comme une tablette ou
téléphone Android
Lors du chargement sur le camion, le QR code est utilisé par le service prépa
commande pour faire passer la commande dans l’état en cours de livraison. La
personne a la possibilité d’écrire une remarque, une consigne ...
Le livreur a aussi accès à l’application mobile pour connaître la destination de
livraison. Il fera passer la commande à livrée à la réception chez le client. Le livreur
a aussi la possibilité d’écrire une remarque.
```

#### 2.4.5 Personnel de maintenance....................................................................................................................

```
Pour respecter les normes sanitaires en vigueur, les cuves des camions cuves
doivent être nettoyées régulièrement.
Chaque entrepôt qui dispose de ces camions est équipée d’une station de
nettoyage vapeur haute pression et du personnel formé.
Chaque cuve est nettoyée après 10 chargements ou au maximum 3 semaines
depuis le dernier nettoyage.
Cette opération dure 1/2 journée pendant laquelle le camion est indisponible.
L’application Web, dans sa liste des véhicules disponibles doit retirer ces véhicules
le temps du nettoyage. Le calcul est fait automatiquement.
Le personnel de maintenance dispose de l’application web.
Il peut consulter la liste des camions à maintenir et peut faire évoluer le status après
intervention.
```

## Section 3 Mesure de performances.........................................................................................

### 3.1 Finalité........................................................................................................................................

```
Nous souhaitons connaître comment nos clients visitent les pages de notre site
Web, ceci afin de pouvoir optimiser par la suite son contenu.
Ceci est une sorte de Google analytics très simplifié.
Nous nous attendons à un très grand nombre de visites, donc prévoir un système
qui permet de nombreuses écritures.
Les données collectées seront récupérées et visibles depuis une application de
bureau. A terme nous utiliserons des modules de la data science en Python pour
analyser les données.
Dans cette 1ère version le besoin minimal est :
 quelles pages sont visitées et à quelles dates
```

## Section 4 Annexe : Calcul du prix d’une livraison................................................................

### 4.1 Principe.......................................................................................................................................

```
Pour rappel notre société Minot’Or réalise deux types de prestations :
```
- Transport direct depuis une minoterie vers une boulangerie
- Transport depuis un de nos entrepôts le plus proche vers une boulangerie

```
Il faut aussi connaître le coût de transport pour les approvisionnements internes
entre une minoterie et un de nos entrepôts
```
#### 4.1.1 Transport direct depuis une minoterie vers une boulangerie...............................................................

##### 4.1.1.1 transport en vrac avec un camion monocuve directement chez le client boulanger....................

```
Prix = coûtKilomètre x nbreKilomètre + fraisFixe
Avec :
coûtKilomètre : coût pour le parcours d’un kilomètre. C’est une valeur moyenne
qui sera appliquée à l’ensemble des camions monocuve. Elle comprend le
salaire du chauffeur, l’amortissement du véhicule, le coût de carburant,
l’assurance, une marge...
La valeur utilisée en ce moment dans notre entreprise est de 2,50 €
Cette valeur doit pouvoir être modifiée par un commercial. Ceci affectera les
nouveaux prix à calculer. Les anciens ne doivent pas être modifiés (on doit
pouvoir ré éditer une facture ancienne)
```
```
nbreKilomètre : c’est la somme des nombres de kilomètres du trajet routier
entre l’entrepôt et la minoterie (à vide) et entre la minoterie et le client
boulanger à livrer. Vous utiliserez un site gouvernemental pour le connaître.
```

```
FraisFixe : englobe les temps de chargement et déchargement de la farine en
vrac. Tout comme coutKilomètre, cette valeur doit pouvoir être modifiée par un
commercial
La valeur utilisée en ce moment dans notre entreprise est de 175 €
```
##### 4.1.1.2 Transport en palettes minoterie → Boulanger.............................................................................

```
Nous utilisons ce genre de véhicule :
```
```
Le minotier a préparé la/les palettes pour son client. Nous assurons uniquement le
transport.
Prix = coûtKilomètre x nbreKilomètre + fraisFixe
```
```
Ici coûtKilomètre est aussi ajustable par un commercial. En ce moment il vaut 1,25€
nbreKilomètre est le même que celui du paragraphe précédent
fraisFixe concerne les temps de chargement/déchargement avec un transpalette.
En ce moment il vaut 75€
```
#### 4.1.2 Transport depuis l’un de nos entrepôts vers une boulangerie..............................................................

```
Le camion cuve n’est jamais utilisé car nous ne disposons pas de silo vrac dans les
entrepôts.
Seul le transport par palettes est possible.
```
```
Le calcul du prix est du même ordre que les calculs précédents :
```
```
Prix = coûtKilomètre x nbreKilomètre + fraisFixe
```
```
Ici coûtKilomètre est aussi ajustable par un commercial. En ce moment il vaut 1,25€
nbreKilomètre est la distance entre l’entrepôt et le client
```

fraisFixe concerne les temps de chargement/déchargement avec un transpalette et
le temps de préparation des palettes. Ce temps de préparation est proportionnel au
poids des marchandises. FraisFixe est donc à son tour calculé par une formule :

fraisFixe = Base + (Prkg x Poids)

Base est une base forfaitaire. Configurable par un commercial. Vaut en ce moment
50 €

Prt : Prix de préparation pour un kilo. Configurable par un commercial. Vaut en ce
moment 0,05 €/kg

Poids : Poids des marchandises en kg

Exemple pour une palette de 10 sacs de farine bio de 25kg et 20 sacs de farine de
maïs de 30kg, soit 250 + 600 = 850kg

fraisFixe = 50 + (0,005 x 850) = 92,50€

Si le parcours est de 50km :

Prix = 1,25 x 50 + 92,50 = 155 €


## Section 5 Quelques cas d’utilisation.......................................................................................

### 5.1 Je suis boulanger, que puis-je faire avec l’application Minot’Or ?......................................

1. Je peux m’inscrire sur le site Minot’Or. C’est un de leurs commerciaux qui
   accepte ou refuse l’inscription

```
Si je suis inscrit le site Minot’Or me propose des produits de boulangerie :
```
2. Je peux consulter sur le site Web la liste des produits de boulangerie. Pour
   la plupart des produits je vois le prix HT et TTC, pour certains il est indiqué
   « sur demande de devis »
3. Je ne peux pas acheter en direct sans passer par un devis
4. Je peux constituer une liste d’envies pour préparer un futur devis. Ceci
   n’engage en rien.
5. Je peux mettre dans une demande de devis une liste de produits, quantités
   et date de livraison souhaitée
6. Les commerciaux de Minot’Or répondent rapidement au devis, les prix sont
   visibles et précis, une date est proposée
7. Si le devis me convient, je le transforme en commande et m’engage à son
   payement. S’il ne me convient pas, je discute avec le commercial qui a
   répondu, ou j’abandonne
8. Je réalise le payement par virement bancaire, depuis ma banque pro, hors
   de leur application. J’indique la référence de la commande.

### 5.2 Je suis minotier, que puis-je faire avec l’application Minot’Or ?.........................................

```
Le site Minot’Or n’est pas pour moi du type Marketplace. Je ne peux pas proposer
de produit à la vente. Je peux démarcher par téléphone un commercial de Minot’Or
pour proposer de la marchandise.
Il me référencera alors dans leur application Minot’Or
```
### commande......................................................................................................................................... 5.3 Je suis commercial, qu’ai je à faire quand une boulangerie a acquitté le payement d’une

### le payement d’une commande

```
Un devis proposé par le commercial est en attente jusqu’à l’acceptation par le client.
Sur acceptation le devis se transforme en commande.
La commande est ensuite livrée au client. Nos clients ont jusqu’à 30 jours pour
payer.
```
- Sur la banque pro, j’ai activé l’envoi de mail sur réception d’un virement. Sur
  réception d’un virement je change le statut de la commande à « payée »
  depuis l’appli Web
- une facture est produite et expédiée au boulanger


