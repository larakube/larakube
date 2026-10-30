#!/usr/bin/env sh
# Fails if values.schema.json or the values tables in readme.md and the docs are out of date with values.yaml.
set -eu

cp charts/larakube/values.schema.json /tmp/larakube-values.schema.json
sh ./build.sh >/dev/null 2>&1

# Print the values table (header up to the first line that is not a table row) of a Markdown file.
values_table() {
  awk '/^\| Key \| Type \| Default \| Description \|$/ { in_table = 1 } in_table && !/^\|/ { exit } in_table' "$1"
}

status=0
values_table charts/larakube/README.md > /tmp/larakube-values-table.md
rm charts/larakube/README.md

if ! diff -u /tmp/larakube-values.schema.json charts/larakube/values.schema.json; then
  echo "charts/larakube/values.schema.json was out of date and has been regenerated. Commit the result."
  status=1
fi

for file in readme.md docs/src/content/docs/reference/chart-config.md; do
  if ! values_table "$file" | diff -u /tmp/larakube-values-table.md - >/dev/null; then
    echo "The values table in $file is out of date. Run ./build.sh and copy the table from charts/larakube/README.md."
    values_table "$file" | diff -u /tmp/larakube-values-table.md - || true
    status=1
  fi
done

exit $status
