<?php

namespace App\Enum;

enum RoleUtilisateurEnum: string
{
    case Boulanger = 'boulanger';
    case Commercial = 'commercial';
    case Livreur = 'livreur';
    case Preparateur_Commande = 'preparateur_commande';
    case Minotier = 'minotier';
    case Approvisionnement = 'approvisionnement';
}
