<?php

require_once "src/JsonRepository.php";

$repo = new JsonRepository("data/inscriptions.json");

$date = $_GET["date"] ?? "";

$heuresPrises = [];

if ($date) {

    $inscriptions = $repo->all();

    foreach ($inscriptions as $i) {

        if (
            $i["date"] === $date &&
            $i["status"] !== "refuse"
        ) {
            $heuresPrises[] = $i["heure"];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($heuresPrises);