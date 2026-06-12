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
# Extract values for Amphi (last hour only)
# ==============================
VALUES_Amphi=$(echo "$RECENT_DATA" | grep ";Amphi1;" | cut -d ";" -f3)

MIN_E208=$(echo "$VALUES_Amphi1" | sort -n | head -1)
MAX_E208=$(echo "$VALUES_Amphi1" | sort -n | tail -1)
# sum+=$1 : adds each value 
# END : after going through the whole column
# sum/NR : sum divided by number of value 
AVG_E208=$(echo "$VALUES_Amphi1" | awk '{sum+=$1} END {print sum/NR}')



# ==============================
# Extract values for entry hall (last hour only)
# ==============================
VALUES_E101=$(echo "$RECENT_DATA" | grep ";Hall-amphi;" | cut -d ";" -f3)

MIN_E101=$(echo "$VALUES_Hall-amphi" | sort -n | head -1)
MAX_E101=$(echo "$VALUES_Hall-amphi" | sort -n | tail -1)
AVG_E101=$(echo "$VALUES_Hall-amphi" | awk '{sum+=$1} END {print sum/NR}')

# ==============================
# Start HTML
# ==============================

cat <<EOF >> "$OUTPUT"
<!DOCTYPE html>
<html lang=\"fr\">

<!-- ============================== START HTML ============================== --!>

<head>
  <meta charset=\"UTF-8\">
  <meta http-equiv=\"refresh\" content=\"60\">
  <title>Temperature Monitoring</title>
  <link rel=\"stylesheet\" href=\"styles/style.css\">
</head>
<body>

<h1>Temperature Monitoring – IUT Blagnac</h1>
<div class=\"container\">

<!-- ============================== ROOM AMPHI 1 ============================== --!>

<div class=\"room\">
<h2>Amphi 1 (Ground floor)</h2>

<div class=\"stats\">
<p>Min: $MIN_Amphi1 °C</p>
<p>Max: $MAX_Amphi1 °C</p>
<p>Average: $AVG_Amphi1 °C</p>
</div>

<table>
<tr><th>Date</th><th>Luminosite (lux)</th></tr>
EOF

echo "$RECENT_DATA" | grep ";Amphi;" | while read line
do
    DATE=$(echo "$line" | cut -d ";" -f1)
    VALUE=$(echo "$line" | cut -d ";" -f3)
    echo "<tr><td>$DATE</td><td>$VALUE</td></tr>" >> "$OUTPUT"
done

cat <<EOF >> "$OUTPUT"
</table>
</div>

<!-- ============================== ENTRY HALL ============================== --!>

<div class=\"room\">
<h2>Hall-amphi ( Groundfloor )</h2>

<div class=\"stats\">
<p>Min: $MIN_Hall-amphi °C</p>
<p>Max: $MAX_Hall-amphi °C</p>
<p>Average: $AVG_Hall-amphi °C</p>
</div>

<table>
<tr><th>Date</th><th>Humidite ( % )</th></tr>
EOF

echo "$RECENT_DATA" | grep ";Hall-amphi;" | while read line
do
    DATE=$(echo "$line" | cut -d ";" -f1)
    VALUE=$(echo "$line" | cut -d ";" -f3)
    echo "<tr><td>$DATE</td><td>$VALUE</td></tr>" >> "$OUTPUT"
done

cat <<EOF >> "$OUTPUT"
</table>
</div>

<!-- ============================== END HTML ============================== --!>

</div>
<footer class=\"site-footer\">
  <div class=\"validators\">

    <a href=\"https://validator.w3.org/nu/?doc=http%3A%2F%2Fdewatine.atwebpages.com%2FSAE15%2Findex.html\" target=\"_blank\">" >> "$OUTPUT"
      <img src=\"img/validationhtml.png\" alt=\"HTML5 Valide !\">" >> "$OUTPUT"
    </a>" >> "$OUTPUT"

    <a href=\"https://jigsaw.w3.org/css-validator/validator?uri=http%3A%2F%2Fdewatine.atwebpages.com%2FSAE15%2Fstyles%2Fstyle.css\" target=\"_blank\">" >> "$OUTPUT"
      <img src=\"http://jigsaw.w3.org/css-validator/images/vcss-blue\" alt=\"CSS Valide !\">" >> "$OUTPUT"
    </a>

  </div>
</footer>
</body>
echo "</html>
EOF



