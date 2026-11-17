#!/usr/bin/env bash
# Smoke checks against a running Sekeco site. Read-only: no content is created.
#
#   scripts/smoke.sh [base-url]        # default http://localhost
#
# Exits non-zero when any check fails.
set -u

BASE="${1:-http://localhost}"
BASE="${BASE%/}"
fail=0

ok()   { printf '  ok    %s\n' "$1"; }
bad()  { printf '  FAIL  %s\n' "$1"; fail=1; }

status() { curl -s -o /dev/null -w '%{http_code}' "$1"; }

expect_status() { # path expected
  local got
  got="$(status "$BASE$1")"
  [ "$got" = "$2" ] && ok "$1 -> $2" || bad "$1 -> $got (want $2)"
}

expect_redirect() { # path expected-location-suffix
  local out code loc
  out="$(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "$BASE$1")"
  code="${out%% *}"
  loc="${out#* }"
  if [ "$code" = "301" ] && [ "${loc%/}" = "$BASE${2%/}" ]; then
    ok "$1 -> 301 $2"
  else
    bad "$1 -> $code $loc (want 301 $BASE$2)"
  fi
}

page() { curl -s "$BASE$1"; }

echo "Archives and pages"
for p in / /works/ /portfolio/ /team/ /contact/; do expect_status "$p" 200; done
expect_status "/?s=studio" 200
expect_status "/this-page-does-not-exist/" 404

echo "Single entries (first link of each archive)"
for archive in works portfolio team; do
  single="$(page "/$archive/" | grep -o "href=\"$BASE/$archive/[^\"/]*/\"" | grep -v "/$archive/feed/" | head -1 | sed 's/^href="//; s/"$//')"
  if [ -n "$single" ]; then
    expect_status "${single#"$BASE"}" 200
  else
    bad "/$archive/ lists no entries to open"
  fi
done

echo "Legacy redirects"
expect_redirect /products/ /portfolio/
work="$(page /works/ | grep -o "href=\"$BASE/works/[^\"/]*/\"" | grep -v '/works/feed/' | head -1 | sed "s#^href=\"$BASE/works/##; s#/\"\$##")"
if [ -n "$work" ]; then expect_redirect "/portfolio/$work/" "/works/$work/"; fi

echo "SEO and structured data"
home="$(page /)"
for tag in 'property="og:title"' 'property="og:url"' 'name="twitter:card"' 'name="description"' 'application/ld+json'; do
  printf '%s' "$home" | grep -q "$tag" && ok "home has $tag" || bad "home lacks $tag"
done
if command -v python3 >/dev/null 2>&1; then
  printf '%s' "$home" | python3 -c '
import json, re, sys
m = re.search(r"<script type=\"application/ld\+json\">(.*?)</script>", sys.stdin.read(), re.S)
json.loads(m.group(1))' 2>/dev/null && ok "home JSON-LD parses" || bad "home JSON-LD does not parse"
fi

echo "REST"
expect_status "/wp-json/sekeco/v1/search?q=studio" 200
expect_status "/wp-json/sekeco/v1/forms/999999/submit" 404

echo "Search with no match shows suggestions"
page "/?s=zzzqqqxx" | grep -q 'sk-row' && ok "empty search lists recent work" || bad "empty search has no suggestions"

echo "Local hygiene"
if [ -f "$(git rev-parse --show-toplevel 2>/dev/null)/wp-content/debug.log" ]; then
  bad "wp-content/debug.log exists"
else
  ok "no debug.log"
fi

echo
[ "$fail" = 0 ] && echo "All checks passed." || echo "Some checks failed."
exit "$fail"
