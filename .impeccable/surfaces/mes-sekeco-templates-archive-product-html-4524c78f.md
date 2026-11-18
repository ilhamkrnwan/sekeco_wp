---
version: 1
slug: "mes-sekeco-templates-archive-product-html-4524c78f"
primary_target: "wp-content/themes/sekeco/templates/archive-product.html"
related_targets: []
---

---
version: 1
slug: "wp-content-themes-sekeco-templates-archive-product-html"
primary_target: "wp-content/themes/sekeco/templates/archive-product.html"
related_targets: ["wp-content/themes/sekeco/templates/single-product.html", "wp-content/themes/sekeco/assets/css/pages.css", "wp-content/plugins/sekeco/src/blocks/project-grid", "wp-content/plugins/sekeco/src/blocks/toc", "wp-content/plugins/sekeco/src/blocks/facts"]
---

## Scope

Mode: Operate-leaning (the visitor scans a catalog and then checks one product). Inside the established Sekeco world. Visitor: a business owner, developer or product lead judging what Sekeco has built and run itself (SaaS, plugins, tools) and whether to open one. The list may grow to dozens of products and a single product page may be long documentation.

## Direction contract

THESIS: The Portfolio is a catalog, not a gallery. One dense row per product, filterable by type and searchable, and a product page that is a spec sheet first and a long read second. Refuses the card grid of equal tiles and the marketing hero with a screenshot and three icon features.

OWN-WORLD: Existing ink and paper grounds, Bricolage display at -0.04em, DM Sans text, hairline rows and pills, a cyan beacon only for a live status, the brand gradient not used at all on these pages. No new colours.

STORY: The visitor sees how many products exist and of what kind, narrows by type or by typing, reads one line of what each does with its status and year, opens one, finds its actions (visit, demo, docs, download, changelog) at the top, its facts in a strip, and, when the page is long, a contents list that follows the reading.

FIRST VIEWPORT: Archive: split head (display title, lead beside it), then a bar with type chips with counts and a search field, then rows from the first product down. Single: left column with the name, lead and action buttons, right column the product image; under them a hairline strip of Type, Status, Version, Platform, Stack, Year, Repository; then contents list left and the story right.

FORM: Chosen by the user in the question round: Portfolio as a filterable catalog (type filter plus search, dense rows), with new properties Type, Version, Platform and Docs, Demo, Download, Changelog links. Derived from the content's job, code-led, no concept tournament. seed: none (roll skipped)

FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance

## Signature interaction and motion grammar

The filter: chips and the search field narrow the same rows at once and the empty state names itself. The contents list marks the heading being read. No other new motion.

## Unresolved

Existing products have no Type yet, so on the live data every product falls under "Other" until the owner sets a type in the editor. DESIGN.md has no row or catalog component; the owner has not approved a DESIGN.md change.
