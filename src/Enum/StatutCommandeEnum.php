<?php

namespace App\Enum;

enum StatutCommandeEnum: string
{
    case EnAttente          = 'en_attente';
    case PreparationProduit  = 'preparation_produit';
    case AttenteLivraison    = 'attente_livraison';
}

