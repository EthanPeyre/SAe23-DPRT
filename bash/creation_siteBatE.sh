#!/bin/bash

# ==============================
# Absolute paths (IMPORTANT for cron)
# ==============================
DATA_FILE="/home/etud/Documents/data_JSON.txt"
OUTPUT="/home/etud/Documents/index.html"

# ==============================
# Number of lines to keep (120 = ~1 hour, 2 rooms)
# ==============================
LAST_LINES=120

# ==============================
# Get only the most recent data
# ==============================
RECENT_DATA=$(tail -$LAST_LINES "$DATA_FILE")

# ==============================
# Extract values for room E208 (last hour only)
# ==============================
VALUES_E208=$(echo "$RECENT_DATA" | grep ";E208;" | cut -d ";" -f3)

MIN_E208=$(echo "$VALUES_E208" | sort -n | head -1)
MAX_E208=$(echo "$VALUES_E208" | sort -n | tail -1)
AVG_E208=$(echo "$VALUES_E208" | awk '{sum+=$1} END {print sum/NR}')

# ==============================
# Extract values for room E101 (last hour only)
# ==============================
VALUES_E101=$(echo "$RECENT_DATA" | grep ";E101;" | cut -d ";" -f3)

MIN_E101=$(echo "$VALUES_E101" | sort -n | head -1)
MAX_E101=$(echo "$VALUES_E101" | sort -n | tail -1)
AVG_E101=$(echo "$VALUES_E101" | awk '{sum+=$1} END {print sum/NR}')

# ==============================
# Start HTML
# ==============================
echo "<!DOCTYPE html>" > "$OUTPUT"
echo "<html lang=\"en\">" >> "$OUTPUT"
echo "<head>" >> "$OUTPUT"
echo "  <meta charset=\"UTF-8\">" >> "$OUTPUT"
echo "  <meta http-equiv=\"refresh\" content=\"60\">" >> "$OUTPUT"
echo "  <title>Temperature Monitoring</title>" >> "$OUTPUT"
echo "  <link rel=\"stylesheet\" href=\"styles/style.css\">" >> "$OUTPUT"
echo "</head>" >> "$OUTPUT"
echo "<body>" >> "$OUTPUT"

echo "<h1>Temperature Monitoring – IUT Blagnac</h1>" >> "$OUTPUT"
echo "<div class=\"container\">" >> "$OUTPUT"

# ==============================
# Room E208
# ==============================
echo "<div class=\"room\">" >> "$OUTPUT"
echo "<h2>Room E208 (Floor 2)</h2>" >> "$OUTPUT"

echo "<div class=\"stats\">" >> "$OUTPUT"
echo "<p>Min: $MIN_E208 °C</p>" >> "$OUTPUT"
echo "<p>Max: $MAX_E208 °C</p>" >> "$OUTPUT"
echo "<p>Average: $AVG_E208 °C</p>" >> "$OUTPUT"
echo "</div>" >> "$OUTPUT"

echo "<table>" >> "$OUTPUT"
echo "<tr><th>Date</th><th>Temperature (°C)</th></tr>" >> "$OUTPUT"

echo "$RECENT_DATA" | grep ";E208;" | while read line
do
    DATE=$(echo "$line" | cut -d ";" -f1)
    VALUE=$(echo "$line" | cut -d ";" -f3)
    echo "<tr><td>$DATE</td><td>$VALUE</td></tr>" >> "$OUTPUT"
done

echo "</table>" >> "$OUTPUT"
echo "</div>" >> "$OUTPUT"

# ==============================
# Room E101
# ==============================
echo "<div class=\"room\">" >> "$OUTPUT"
echo "<h2>Room E101 (Floor 1)</h2>" >> "$OUTPUT"

echo "<div class=\"stats\">" >> "$OUTPUT"
echo "<p>Min: $MIN_E101 </p>" >> "$OUTPUT"
echo "<p>Max: $MAX_E101 </p>" >> "$OUTPUT"
echo "<p>Average: $AVG_E101 </p>" >> "$OUTPUT"
echo "</div>" >> "$OUTPUT"

echo "<table>" >> "$OUTPUT"
echo "<tr><th>Date</th><th>CO2</th></tr>" >> "$OUTPUT"

echo "$RECENT_DATA" | grep ";E101;" | while read line
do
    DATE=$(echo "$line" | cut -d ";" -f1)
    VALUE=$(echo "$line" | cut -d ";" -f3)
    echo "<tr><td>$DATE</td><td>$VALUE</td></tr>" >> "$OUTPUT"
done

echo "</table>" >> "$OUTPUT"
echo "</div>" >> "$OUTPUT"

# ==============================
# End HTML
# ==============================
echo "</div>" >> "$OUTPUT"
echo "<footer class=\"site-footer\">" >> "$OUTPUT"
echo "  <div class=\"validators\">" >> "$OUTPUT"

echo "    <a href=\"https://validator.w3.org/nu/?doc=http%3A%2F%2Fdewatine.atwebpages.com%2FSAE15%2Findex.html\" target=\"_blank\">" >> "$OUTPUT"
echo "      <img src=\"img/validationhtml.png\" alt=\"HTML5 Valide !\">" >> "$OUTPUT"
echo "    </a>" >> "$OUTPUT"

echo "    <a href=\"https://jigsaw.w3.org/css-validator/validator?uri=http%3A%2F%2Fdewatine.atwebpages.com%2FSAE15%2Fstyles%2Fstyle.css\" target=\"_blank\">" >> "$OUTPUT"
echo "      <img src=\"http://jigsaw.w3.org/css-validator/images/vcss-blue\" alt=\"CSS Valide !\">" >> "$OUTPUT"
echo "    </a>" >> "$OUTPUT"

echo "  </div>" >> "$OUTPUT"
echo "</footer>" >> "$OUTPUT"
echo "</body>" >> "$OUTPUT"
echo "</html>" >> "$OUTPUT"
# curl -T "$OUTPUT" ftp://dewatine.atwebpages.com/SAE15/ \
# --user "4689065_dewatine:;!WK5DkJ45n6Z"


