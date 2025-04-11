<?php

namespace App\Enum;

enum StatutLivraisonEnum: string
{
    case in_progress = "in_progress";
    case delivered = "delivered";
    case in_preparation = "in_preparation";

}
