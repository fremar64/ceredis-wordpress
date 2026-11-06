# Rapport final — Mise en page articles Blog CEREDIS

## Causes identifiées

1. Largeur des paragraphes : la règle `:where(...)` avait une spécificité nulle, neutralisée par les règles WordPress `is-layout-constrained` (spécificité supérieure) qui imposaient `content-size` (1200px) sur les enfants. Résultat : la contrainte de 80ch n'était pas effectivement appliquée.
2. Listes : `ul`/`ol` n'avaient aucune contrainte de largeur ni centrage, donc occupaient toute la largeur du conteneur, décalées par rapport aux paragraphes centrés.

## Fichiers modifiés

- `wp-content/themes/ceredis/style.css` uniquement.

## Corrections réalisées

- Remplacement des sélecteurs `:where(...)` (spécificité 0) par des sélecteurs directs pour `p, blockquote, pre, figure, .wp-block-columns` → spécificité suffisante pour outrepasser les règles WordPress.
- Ajout de `max-width: 80ch` + `margin-inline: auto` aux `ul` et `ol` pour aligner la colonne des listes avec celle des paragraphes, tout en conservant `padding-inline-start: 1.5rem` (retrait des puces).
- Mise à jour des media queries mobiles pour inclure les listes dans le passage à `max-width: 100%`.

## Contrôles exécutés

- `git diff --check` : OK.
- `theme.json` inchangé, JSON valide.
- Hiérarchie H1 (hero) > H2 (heading-2) > H3 (heading-3) préservée.
- Aucun test visuel disponible dans cet environnement.

## Points restant à vérifier visuellement

- Article représentatif sur desktop et mobile : largeur de colonne de lecture, alignement des listes, absence de débordement horizontal.
- Tableaux, images, citations non affectés.

## Recommandation

Accepter le correctif ; la spécificité était la cause réelle du problème de largeur.
