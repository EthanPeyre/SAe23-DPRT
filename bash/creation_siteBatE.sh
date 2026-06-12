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

#EOF or End Of File is what we use instead of "echo"s at the start of every line
#"<<EOF" indicates the command to use till there is EOF
#then ">" is to rederect to index.html

cat <<EOF > "$OUTPUT"
<!DOCTYPE html>
<html lang=\"fr\">

<!-- ============================== HEADER ============================== --!>

<head>"
  <meta charset=\"UTF-8\">
  <meta http-equiv=\"refresh\" content=\"60\">
  <title>Temperature Monitoring</title>
  <link rel=\"stylesheet\" href=\"styles/style.css\">
</head>

<!-- ============================== START BODY ============================== --!>

<body>

<h1>Temperature Monitoring – IUT Blagnac</h1>
<div class=\"container\">

<!-- ============================== Room E208 ============================== --!>

<div class=\"room\">
<h2>Room E208 (Floor 2)</h2>

<div class=\"stats\">
<p>Min: $MIN_E208 °C</p>
<p>Max: $MAX_E208 °C</p>
<p>Average: $AVG_E208 °C</p>
</div>

<table>
<tr><th>Date</th><th>Temperature (°C)</th></tr>
EOF

echo "$RECENT_DATA" | grep ";E208;" | while read line
do
    DATE=$(echo "$line" | cut -d ";" -f1)
    VALUE=$(echo "$line" | cut -d ";" -f3)
    echo "<tr><td>$DATE</td><td>$VALUE</td></tr>" >> "$OUTPUT"
done

cat <<EOF >> "$OUTPUT"
</table>
</div>

<!-- ============================== Room E101 ============================== --!>

<div class=\"room\">
<h2>Room E101 (Floor 1)</h2>

<div class=\"stats\">
<p>Min: $MIN_E101 </p>
<p>Max: $MAX_E101 </p>
<p>Average: $AVG_E101 </p>
</div>

<table>
<tr><th>Date</th><th>CO2</th></tr>
EOF

echo "$RECENT_DATA" | grep ";E101;" | while read line
do
    DATE=$(echo "$line" | cut -d ";" -f1)
    VALUE=$(echo "$line" | cut -d ";" -f3)
    echo "<tr><td>$DATE</td><td>$VALUE</td></tr>" >> "$OUTPUT"
done

cat <<EOF >> "$OUTPUT"
</table>
</div>

<!-- ============================== End HTML ============================== --!>
</div>
<footer class=\"site-footer\">
  <div class=\"validators\">

    <a href=\"https://validator.w3.org/nu/?doc=http%3A%2F%2Fdewatine.atwebpages.com%2FSAE15%2Findex.html\" target=\"_blank\">
      <img src=\"img/validationhtml.png\" alt=\"HTML5 Valide !\">" >> "$OUTPUT"
    </a>" >> "$OUTPUT"

    <a href=\"https://jigsaw.w3.org/css-validator/validator?uri=http%3A%2F%2Fdewatine.atwebpages.com%2FSAE15%2Fstyles%2Fstyle.css\" target=\"_blank\">
      <img src=\"http://jigsaw.w3.org/css-validator/images/vcss-blue\" alt=\"CSS Valide !\">
    </a>" >> "$OUTPUT"

  </div>
</footer>
</body>
</html>
EOF



