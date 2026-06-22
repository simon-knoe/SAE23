#!/bin/bash

# --- CONFIGURATION BDD LAMPP ---
MYSQL_BIN="/opt/lampp/bin/mysql"
DB_USER="sae23"
DB_PASS="sae23" 
DB_NAME="SAE23"

# --- CONFIGURATION MQTT ---
MQTT_USER="student"
MQTT_PASS="student"
MQTT_HOST="mqtt.iut-blagnac.fr"
MQTT_PORT="8883"            

export MYSQL_PWD="$DB_PASS"

echo "=== LANCEMENT DE LA COLLECTE PAR SALLE (SANS UNITÉ) ==="

# 1. Récupération de la liste des salles depuis la BDD
mapfile -t LISTE_SALLES < <($MYSQL_BIN -u$DB_USER $DB_NAME -se "SELECT salle FROM salles ORDER BY salle")

# 2. Boucle principale sur CHAQUE salle
for SALLE_COURANTE in "${LISTE_SALLES[@]}"
do
    echo "🔎 Interrogation de la salle : $SALLE_COURANTE..."

    # Définition du topic exact pour cette salle
    TOPIC_SALLE="sensors/AM107/by-room/${SALLE_COURANTE}/data"

    # 3. Récupération des types de capteurs configurés pour cette salle
    CAPTEURS_SALLE=$($MYSQL_BIN -u$DB_USER $DB_NAME -se "SELECT capt_type FROM capteurs WHERE salle='$SALLE_COURANTE'")

    # Horodatage pour l'insertion
    DATE_ACTUELLE=$(date +%Y-%m-%d)
    HEURE_ACTUELLE=$(date +%H:%M:%S)

    # 4. ÉCOUTE UNIQUE : 1 message (-C 1) et limite de 5 secondes (-W 5)
    PAYLOAD=$(mosquitto_sub -h $MQTT_HOST -p $MQTT_PORT -u $MQTT_USER -P $MQTT_PASS -t "$TOPIC_SALLE" -C 1 -W 5 2>/dev/null)

    # Si la salle a répondu
    if [ "$PAYLOAD" != "" ]; then
        
        # Extraction des données du JSON
        TEMP=$(echo "$PAYLOAD" | jq -r '.[0].temperature // ""')
        HUM=$(echo "$PAYLOAD" | jq -r '.[0].humidity // ""')
        CO2=$(echo "$PAYLOAD" | jq -r '.[0].co2 // ""')
        LUM=$(echo "$PAYLOAD" | jq -r '.[0].illumination // ""')

        # Fonction interne d'insertion (CORRIGÉE : sans la colonne unite)
        inserer_si_autorise() {
            local TYPE_DEMANDE="$1"
            local VALEUR="$2"
            local ID_CAPTEUR="$3"

            if [ "$VALEUR" != "" ] && [ "$VALEUR" != "null" ]; then
                # Vérification si le type de capteur est actif pour cette salle
                if echo "$CAPTEURS_SALLE" | grep -qxF "$TYPE_DEMANDE"; then
                    
                    # CORRIGÉ : Requête SQL sans la colonne 'unite'
                    $MYSQL_BIN -u$DB_USER $DB_NAME -e "INSERT INTO mesures (date, horaire, valeur, capteur) VALUES ('$DATE_ACTUELLE', '$HEURE_ACTUELLE', $VALEUR, '$ID_CAPTEUR');"
                    echo "  ✅ [$TYPE_DEMANDE] Valeur $VALEUR insérée pour $ID_CAPTEUR"
                fi
            fi
        }

        # Lancement des vérifications pour les 4 capteurs autorisés
        inserer_si_autorise "temperature" "$TEMP"  "AM107_${SALLE_COURANTE}_Temp"
        inserer_si_autorise "humidite"    "$HUM"   "AM107_${SALLE_COURANTE}_Hum"
        inserer_si_autorise "co2"         "$CO2"   "AM107_${SALLE_COURANTE}_CO2"
        inserer_si_autorise "luminosite"  "$LUM"   "AM107_${SALLE_COURANTE}_Lum"

    else
        echo "  ⏳ Pas de réponse MQTT pour la salle $SALLE_COURANTE (Timeout 5s)"
    fi
done
