<?php
namespace App\Enum;

enum ProductCategory: string
{
    case Flour     = 'flour';
    case Oil       = 'oil';
    case Egg       = 'egg';
    case Yeast     = 'yeast';
    case Salt      = 'salt';
    case Sugar     = 'sugar';
    case Butter    = 'butter';
    case Milk      = 'milk';
    case Seed      = 'seed';
    case Chocolate = 'chocolate';
    case Bread     = 'bread'; // TODO le pain n'est pas un produit vendu, il est collecté. Si on garde les produits
                              //  collectés dans la table Product on peut l'appeler différemment pour tout englober ?
                              //  bread => collected
}
