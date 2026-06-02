#!/bin/bash
# Script to calculate min, max and average temperature for each room

# File containing the collected temperature data
FILE="data_JSON.txt"

# ===== Statistics for room E208 =====
echo "Room E208"

# Extract temperature values for room E208
values=$(grep ";E208;" "$FILE" | cut -d ";" -f3)

# Calculate minimum temperature
min=$(echo "$values" | sort -n | head -1)

# Calculate maximum temperature
max=$(echo "$values" | sort -n | tail -1)

# Calculate average temperature
avg=$(echo "$values" | awk '{sum+=$1} END {print sum/NR}')

# Display results for room E208
echo "Min : $min"
echo "Max : $max"
echo "Average : $avg"
echo ""

# ===== Statistics for room E101 =====
echo "Room E101"

# Extract temperature values for room E101
values=$(grep ";E101;" "$FILE" | cut -d ";" -f3)

# Calculate minimum temperature
min=$(echo "$values" | sort -n | head -1)

# Calculate maximum temperature
max=$(echo "$values" | sort -n | tail -1)

# Calculate average temperature
avg=$(echo "$values" | awk '{sum+=$1} END {print sum/NR}')

# Display results for room E101
echo "Min : $min"
echo "Max : $max"
echo "Average : $avg"

# ===== Statistics for room A011 =====
echo "Room A011"

# Extract temperature values for room E208
values=$(grep ";A011;" "$FILE" | cut -d ";" -f3)

# Calculate minimum temperature
min=$(echo "$values" | sort -n | head -1)

# Calculate maximum temperature
max=$(echo "$values" | sort -n | tail -1)

# Calculate average temperature
avg=$(echo "$values" | awk '{sum+=$1} END {print sum/NR}')

# Display results for room E208
echo "Min : $min"
echo "Max : $max"
echo "Average : $avg"
echo ""

# ===== Statistics for room A101 =====
echo "Room A101"

# Extract temperature values for room E101
values=$(grep ";A101;" "$FILE" | cut -d ";" -f3)

# Calculate minimum temperature
min=$(echo "$values" | sort -n | head -1)

# Calculate maximum temperature
max=$(echo "$values" | sort -n | tail -1)

# Calculate average temperature
avg=$(echo "$values" | awk '{sum+=$1} END {print sum/NR}')

# Display results for room E101
echo "Min : $min"
echo "Max : $max"
echo "Average : $avg"
