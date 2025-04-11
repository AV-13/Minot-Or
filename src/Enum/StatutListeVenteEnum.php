<?php

namespace App\Enum;

enum StatutListeVenteEnum: string
{
    case en_attente = "en_attente";
    case preparation_produit = "preparation_produit";
    case attente_livraison = "attente_livraison";
}
