---
version: 1
slug: "es-sekeco-templates-single-portfolio-html-fcef5d6b"
primary_target: "wp-content/themes/sekeco/templates/single-portfolio.html"
related_targets: []
---

---
version: 1
slug: "wp-content-themes-sekeco-templates-single-portfolio-html"
primary_target: "wp-content/themes/sekeco/templates/single-portfolio.html"
related_targets: ["wp-content/themes/sekeco/templates/single-product.html", "wp-content/themes/sekeco/templates/single-team.html", "wp-content/themes/sekeco/templates/single.html", "wp-content/themes/sekeco/templates/archive-portfolio.html", "wp-content/themes/sekeco/templates/archive-product.html", "wp-content/themes/sekeco/templates/archive-team.html", "wp-content/themes/sekeco/templates/home.html", "wp-content/themes/sekeco/templates/search.html", "wp-content/themes/sekeco/templates/404.html", "wp-content/themes/sekeco/templates/page.html", "wp-content/themes/sekeco/assets/css/pages.css"]
---

## Scope

Mode per template: Works single and Works archive are Experience (the artifact leads); Portfolio single and archive are Operate-leaning Persuade (evaluate a product, scan a line-up); Team is Read (meet a person); Journal single and index, pages and search results are Read; 404 is recovery. Inside the established Sekeco world: the homepage, header, footer, tokens and plugin blocks stay; every template below the homepage gets its own structure for its own job.

## Direction contract

THESIS: The same grammar (wrap, head, story, sign-off) spent five different ways, because the pages do five different jobs. Refuses the one-skeleton default: title, meta line, 16:9 image, prose, "more" grid, CTA on every single.

OWN-WORLD: The existing Sekeco ink and paper grounds, Bricolage display set at -0.04em, DM Sans text, 20px radii, hairline rules, pill controls, brand gradient only as fill (read-progress bar, search button hover), cyan beacon for a live product. No new colours; ledger, strip, pills and rows are all outline and hairline.

STORY: A visitor judging Sekeco meets real work first (a full-bleed work with its facts beside the story and the next work as the way on), checks a product like a spec sheet, meets a person beside a portrait that stays, reads an article in one calm column and is offered three more titles, scans the line-up as rows, and always has a contact line within reach.

FIRST VIEWPORT: Works single: full-bleed cover image under an ink scrim, category pills and the name at up to 6rem bottom-left with its detail line beneath. Portfolio single: 7/5 split, name and lede with the visit action on the left, media sticky on the right. Team: 5/7 split, sticky 4/5 portrait, name at display size, role, bio, profile pills. Journal single: left-aligned title in a 960px column, lead, byline with avatar, date and reading time, then a wide 21:9 cover. Archives: split head (display title, lead beside it) then Works a filterable mosaic, Portfolio hairline rows, Team a portrait roster, Journal an index with the newest entry leading.

FORM: Chosen by the user in the question round: all templates below the homepage, the work leads on Works single, new plugin blocks allowed sparingly (the Facts block and the sekeco/post-type binding). Derived from each template's job, code-led, no concept tournament (a precisely specified extension of an established world). seed: none (roll skipped)

FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance

## Signature interaction and motion grammar

One authored moment: the Journal's reading-progress hairline (CSS scroll-driven, hidden under reduced motion). Everything else reuses the existing runtime (reveals, spotlight, glare) and adds no new motion.

## Unresolved

The Journal index (home.html) only renders where Settings, Reading, Posts page is set to the Journal page; today /journal/ is an empty page. DESIGN.md's type ramp has no step for the lead (1.125rem to 1.5rem) or the 6rem display; owner has not approved a DESIGN.md change.
