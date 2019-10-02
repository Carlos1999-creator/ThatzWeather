<?php
error_reporting(0);
$mysqli = new mysqli('localhost', 'root', '', 'thatzweather');
if (mysqli_connect_errno()) {
    echo "Error de conexión a la BD: " . mysqli_connect_error();
    exit();
}

$encontrado = null;

function review_valid($valor)
{
    $error = 0;
    
    
    if (strlen($valor) != 5) {
        $error = 1;
    }
    
    else {
        
        if (substr($valor, 0, 1) < '0' || substr($valor, 0, 1) > '9') {
            $error = 1;
        } else if (substr($valor, 1, 1) < '0' || substr($valor, 1, 1) > '9') {
            $error = 1;
        } else if (substr($valor, 2, 1) < '0' || substr($valor, 2, 1) > '9') {
            $error = 1;
        } else if (substr($valor, 3, 1) < '0' || substr($valor, 3, 1) > '9') {
            $error = 1;
        } else if (substr($valor, 4, 1) < '0' || substr($valor, 4, 1) > '9') {
            $error = 1;
        }
    }
    
    return $error;
}

if ($_POST['ciudades']) {
    
    if (review_valid($_POST['ciudades']) == 0) {
        $jsonfile = file_get_contents('http://api.openweathermap.org/data/2.5/weather?q=' . $_POST['ciudades'] . ',es&APPID=709074e0cf1552b224e95d730073a7dc');
        
        $datos = json_decode($jsonfile, true);
        
        /*foreach ($datos as $key => $v1) {
        echo "$key => $v1<br>";
        }*/
        $datos_tratados = array();
        foreach ($datos as $key => $data) {
            
            if (is_array($data) == true) {
                foreach ($data as $element) {
                    if (is_array($element) == true) {
                        foreach ($element as $e) {
                            array_push($datos_tratados, $e);
                        }
                    } else {
                        array_push($datos_tratados, $element);
                    }
                }
            }
            
            else {
                /*$casillas = '$datos["'.$key.'"]';
                echo 'INSERT INTO prueba cp ='.$casillas;
                echo "<br>";*/
                array_push($datos_tratados, $data);
            }
        }
        
        // Mirar si el Código Postal existe en la BD
        
        
        $query = 'SELECT * FROM data WHERE cp = "' . $_POST['ciudades'] . '";';
        $resultado = $mysqli->query($query) or die($mysqli->error . " en la linea " . (__LINE__ - 1));
        
        $encontrado = $resultado->num_rows;
        $_POST[24]  = str_replace("", "'", $_POST[24]);
        
        
        if ($encontrado == 0) {
            $query = 'INSERT INTO data (cp, coord_lon, coord_lat, weather_id, weather_main, weather_descrip, weather_icon, base, main_temperature, main_preassure, main_humidity, main_temp_min, main_temp_max, visibility, wind_speed, wind_deg, clouds_all, dt, sys_type, sys_id, sys_message, sys_country, sys_sunrise, sys_sunset, timezone, city_id, city_name, cod) VALUES ("' . $_POST['ciudades'] . '", ' . $datos_tratados[0] . ', ' . $datos_tratados[1] . ', ' . $datos_tratados[2] . ', "' . addslashes($datos_tratados[3]) . '", "' . addslashes($datos_tratados[4]) . '", "' . addslashes($datos_tratados[5]) . '", "' . addslashes($datos_tratados[6]) . '", ' . $datos_tratados[7] . ',' . $datos_tratados[8] . ',' . $datos_tratados[9] . ',' . $datos_tratados[10] . ',' . $datos_tratados[11] . ',' . $datos_tratados[12] . ', ' . $datos_tratados[13] . ', ' . $datos_tratados[14] . ', ' . $datos_tratados[15] . ', ' . $datos_tratados[16] . ', ' . $datos_tratados[17] . ', ' . $datos_tratados[18] . ', ' . $datos_tratados[19] . ', "' . addslashes($datos_tratados[20]) . '", ' . $datos_tratados[21] . ', ' . $datos_tratados[22] . ', ' . $datos_tratados[23] . ', ' . $datos_tratados[24] . ', "' . addslashes($datos_tratados[25]) . '", ' . $datos_tratados[26] . ');';
            $mysqli->query($query) or die($mysqli->error . " en la linea " . (__LINE__ - 1));
        }
        
        else {
            $query = 'UPDATE data SET coord_lon = ' . $datos_tratados[0] . ', coord_lat = ' . $datos_tratados[1] . ', weather_id = ' . $datos_tratados[2] . ', weather_main = "' . addslashes($datos_tratados[3]) . '", weather_descrip = "' . addslashes($datos_tratados[4]) . '", weather_icon = "' . addslashes($datos_tratados[5]) . '", base = "' . addslashes($datos_tratados[6]) . '", main_temperature = ' . $datos_tratados[7] . ', main_preassure = ' . $datos_tratados[8] . ', main_humidity = ' . $datos_tratados[9] . ', main_temp_min = ' . $datos_tratados[10] . ', main_temp_max = ' . $datos_tratados[11] . ', visibility = ' . $datos_tratados[12] . ', wind_speed = ' . $datos_tratados[13] . ', wind_deg = ' . $datos_tratados[14] . ', clouds_all = ' . $datos_tratados[15] . ', dt = ' . $datos_tratados[16] . ', sys_type = ' . $datos_tratados[17] . ', sys_id = ' . $datos_tratados[18] . ', sys_message = ' . $datos_tratados[19] . ', sys_country = "' . addslashes($datos_tratados[20]) . '", sys_sunrise = ' . $datos_tratados[21] . ', sys_sunset = ' . $datos_tratados[22] . ', timezone = ' . $datos_tratados[23] . ', city_id = ' . $datos_tratados[24] . ', city_name = "' . addslashes($datos_tratados[25]) . '", cod = ' . $datos_tratados[26] . ' WHERE cp = "' . addslashes($_POST['ciudades']) . '";';
            
            $mysqli->query($query) or die($mysqli->error . " en la linea " . (__LINE__ - 1));
        }
        
        
    }
}



?>



<!DOCTYPE html>
<html>
<head>
<title>Avocode</title>
	<link rel="stylesheet" href="style.css">
	<link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
</head>

<body class="bg">
<div class="container-fluid">
<div class="logo"><img src="images/Bitmap.png"/></div>
<div>
<p class="textoslogan">Entérate del tiempo en la zona exacta que te interesa <br> buscando por código postal.</p>
</div>
<div class="selector">

<form method="post">
    <input type="text" class="textoform" name="ciudades" placeholder="  Introduce el código postal">
    <div class="rectangle">
        <input type="submit" class="buscar" value="Buscar"/>
        <img class ="lupa" src="images/Shape.png">
    </div>
</form>
<p class="textofooter">¡Que la lluvia no te pare!</p>
</div>
</div>
</body>
</html>



















