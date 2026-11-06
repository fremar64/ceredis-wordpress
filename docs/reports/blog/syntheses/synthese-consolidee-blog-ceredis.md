# Synthèse consolidée — Blog CEREDIS

## Corrections réellement appliquées (style.css)

- `.ceredis-single-post-title` : ajout `font-size: var(--wp--preset--font-size--hero)` + `line-height: 1.15`.
- `.ceredis-single-post-content` (p, blockquote, pre, figure, .wp-block-columns) : `max-width` 70ch → 80ch.
- Sélecteurs `:where(...)` → sélecteurs directs (spécificité suffisante face à WP).
- `ul`/`ol` du contenu : ajout `max-width: 80ch` + `margin-inline: auto` (alignement avec paragraphes), `padding-inline-start: 1.5rem` conservé.
- Media queries mobiles : mise à jour pour inclure listes dans `max-width: 100%`.

## Fichiers concernés

- `wp-content/themes/ceredis/style.css` uniquement pour le Blog (modifications CSS ciblées).
- `templates/single.html` : non modifié (déjà correct : H1 level 1).
- `templates/index.html` : non modifié.
- `patterns/latest-content.php` : non modifié.

## Archive / cartes / pagination

Aucune correction effective : inspection uniquement. Aucune modification des modèles d'archive, cartes d'articles ou pagination.

## Contrôles effectués

- `git diff --check` : OK.
- `theme.json` JSON valide.
- Aucun navigateur disponible → pas de validation visuelle.

## Points restant à vérifier visuellement

- Largeur de colonne de lecture sur desktop.
- Alignement des listes à puces.
- Tableaux, images, citations non déformés.
- Absence de débordement horizontal sur mobile.
- Archive, cartes et pagination (non modifiées, comportement inchangé).
