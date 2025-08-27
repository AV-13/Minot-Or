// front/src/TitleManager.jsx
import { useEffect } from "react";
import { useLocation } from "react-router"; // ou "react-router-dom" selon ton setup

function computeTitle(pathname) {

  const exact = {
    "/": "Minotor - Accueil",
    "/login": "Minotor - Connexion",
    "/register": "Minotor - Inscription",
    "/product": "Minotor - Catalogue",
    "/quotation": "Minotor - Devis",
    "/dashboard": "Minotor - Tableau de bord",
    "/dashboard/users": "Minotor - Utilisateurs",
    "/dashboard/products": "Minotor - Produits (Admin)",
    "/dashboard/warehouses": "Minotor - Entrepôts",
    "/dashboard/quotations": "Minotor - Devis (Admin)",
    "/order-history": "Minotor - Historique des commandes",
    "/profile": "Minotor - Profil",
    "/dashboard/companies": "Minotor - Sociétés",
  };
  if (exact[pathname]) return exact[pathname];

  // Règles pour routes dynamiques
  if (pathname.startsWith("/quotation/detail/")) {
    const id = pathname.split("/").pop();
    return `Détails du devis #${id} — MonApp`;
  }

  return "MonApp"; // fallback
}

export default function TitleManager() {
  const { pathname } = useLocation();

  useEffect(() => {
    document.title = computeTitle(pathname);
  }, [pathname]);

  return null;
}
