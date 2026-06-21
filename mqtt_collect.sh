#!/bin/bash

# ==========================================
# MQTT BROKER CONFIGURATION
# ==========================================
BROKER_MQTT="mqtt.iut-blagnac.fr"
UTILISATEUR_MQTT="student"
MDP_MQTT="student"
TOPIC_MQTT="AM107/by-room/+/data" # The '+' wildcard listens to all rooms

# ==========================================
# MYSQL CONFIGURATION (Based on phpMyAdmin)
# ==========================================
UTILISATEUR_BDD="root"
MDP_BDD=""          # Default LAMPP root has no password
NOM_BDD="SAE23"     # Exact database name from the screenshot

while true
do
    # ---------------------------------------------------------
    # 1. LISTEN TO MQTT BROKER
    # ---------------------------------------------------------
    # -v: Verbose output (prints "Topic Payload")
    # -C 1: Exit immediately after receiving exactly 1 message
    # timeout 30: Kill the process if no message arrives within 30s
    SORTIE_BRUTE=$(timeout 30 mosquitto_sub -h "$BROKER_MQTT" -u "$UTILISATEUR_MQTT" -P "$MDP_MQTT" -t "$TOPIC_MQTT" -v -C 1)

    # Check if the SORTIE_BRUTE string is not empty (meaning we received data)
    if [ -n "$SORTIE_BRUTE" ]; then
        
        # ---------------------------------------------------------
        # 2. EXTRACT DATA FROM RAW OUTPUT
        # ---------------------------------------------------------
        # Split the raw output to get the Topic and the JSON string
        TOPIC_RECU=$(echo "$SORTIE_BRUTE" | awk '{print $1}')
        DONNEES_JSON=$(echo "$SORTIE_BRUTE" | cut -d' ' -f2-)

        # Extract the room name from the 3rd part of the topic string
        # Example: AM107/by-room/B104/data -> extracts "B104"
        SALLE=$(echo "$TOPIC_RECU" | cut -d'/' -f3)

        # Extract the actual value using jq (Example: extracting temperature)
        # Ensure jq is installed: sudo apt install jq
        VALEUR=$(echo "$DONNEES_JSON" | jq -r '.temperature')

        # ---------------------------------------------------------
        # 3. DEFINE SENSOR PROPERTIES
        # ---------------------------------------------------------
        # We define the attributes matching the columns in the 'capteurs' table
        NOM_CAPTEUR="AM107_$SALLE"
        UNITE="°C"
        TYPE_CAPTEUR="temperature"

        echo "$(date) | Data detected for room: $SALLE"

        # ---------------------------------------------------------
        # 4. INSERT INTO MYSQL DATABASE
        # ---------------------------------------------------------
        # SQL Query targeting the exact columns: capteur, unite, salle, capt_type
        # We use 'INSERT IGNORE' to prevent SQL errors if the sensor is already registered in the table
        REQUETE_CAPTEUR="INSERT IGNORE INTO capteurs (capteur, unite, salle, capt_type) VALUES ('$NOM_CAPTEUR', '$UNITE', '$SALLE', '$TYPE_CAPTEUR');"

        # Execute the query using the LAMPP MySQL client
        /opt/lampp/bin/mysql -u "$UTILISATEUR_BDD" -D "$NOM_BDD" -e "$REQUETE_CAPTEUR"

        echo "   -> [OK] Sensor $NOM_CAPTEUR registered in the 'capteurs' table!"

        # ---------------------------------------------------------
        # 5. INSERT MEASUREMENT INTO MYSQL DATABASE
        # ---------------------------------------------------------
        # Check if VALEUR is valid (not empty and not 'null' string from jq)
        if [ "$VALEUR" != "null" ] && [ -n "$VALEUR" ]; then
            # We use MySQL native functions CURDATE() and CURTIME() for date and time
            REQUETE_MESURE="INSERT INTO mesures (date, horaire, valeur, capteur) VALUES (CURDATE(), CURTIME(), $VALEUR, '$NOM_CAPTEUR');"
            
            # Execute the measurement insertion
            /opt/lampp/bin/mysql -u "$UTILISATEUR_BDD" -D "$NOM_BDD" -e "$REQUETE_MESURE"
            
            echo "   -> [OK] Measurement ($VALEUR $UNITE) inserted into 'mesures' table!"
        else
            echo "   -> [WARNING] No valid temperature value found in the JSON payload."
        fi

    else
        # Triggered if the 30-second timeout expires without any message
        echo "$(date) : No new data received from MQTT. Waiting..."
    fi

    # Wait 15 seconds before restarting the listening loop
    sleep 15
done
