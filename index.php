<?php

$apiUrl = 'https://opendata.lillemetropole.fr/api/records/1.0/search/?';

$paramsApi = [
    'dataset' => 'ilevia-prochainspassages',
    'rows' => '10',
//    'sort' => 'heureestimeedepart',
    'timezone' => 'Europe%2FParis',
    'facet' => ['nomstation', 'codeligne', 'sensligne']
];

function nextPassages($apiUrl, $paramsApi, $params, $start, $end, $type) {
    $url = $apiUrl;
    $params = array_merge($paramsApi, $params);
    foreach ($params as $key => $param) {
        if (is_string($param)) {
            $url .= '&'.$key.'='.$param;
        }
        if (is_array($param)) {
            foreach ($param as $subParam) {
                $url .= '&'.$key.'='.$subParam;
            }
        }
    }
    echo '<br><h4><i class="fas fa-bus"></i> '.$start.' vers '.$end.' <a href="'.$url.'"><i class="fas fa-link"></i></a></h4>';

    $json = file_get_contents($url);
    if ($json) {
        $array = json_decode($json);
        if (isset($array->nhits) && $array->nhits != 0) {
            $times = [];
            $now = time();
            foreach ($array->records as $record) {
                $time = strtotime($record->fields->heureestimeedepart);
                $diff = null;
                if ($now < $time) {
                    $diff = round(($time - $now) / 60);
                }
                $times[] = [
                    'time' => $time,
                    'diff' => $diff
                ];
            }
            echo '<ul class="list-group">';
            foreach ($times as $time) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo date('H:i:s',$time['time']);
                if (!is_null($time['diff'])) {
                    echo '<span class="badge badge-success badge-pill">'.$time['diff'].' min</span>';
                }
                echo '</li>';
            }
            echo '</ul>';
        } else {
            ?>
                <div class="alert alert-warning">
                    <strong>Aucun résultat</strong>
                </div>
            <?php
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <meta name="robots" content="noindex, nofollow">
    <title>Horaires Bus</title>
    <link rel="shortcut icon" href="bus.png">
</head>
<body>
<div class="container">
    <h1 class="text-center">Horaires Bus</h1>
    <hr>
    <?php
        nextPassages($apiUrl, $paramsApi, ['refine.codeligne' => 'L3', 'refine.sensligne' => 'QUART.+BEAULIEU', 'refine.nomstation' => 'BD+DE+MULHOUSE'], 'Maison (Bd de Mulhouse)', 'Eurotéléport', 'Bus');
        nextPassages($apiUrl, $paramsApi, ['refine.codeligne' => 'L3', 'refine.sensligne' => 'QUART.+BEAULIEU', 'refine.nomstation' => 'FRATERNITE'], 'Maison (Fraternité)', 'Eurotéléport', 'Bus');
        nextPassages($apiUrl, $paramsApi, ['refine.codeligne' => 'L3', 'refine.sensligne' => 'LA+PLAINE', 'refine.nomstation' => 'EUROTELEPORT'], 'Eurotéléport', 'Maison', 'Bus');
    ?>
</div>
</body>
</html>
