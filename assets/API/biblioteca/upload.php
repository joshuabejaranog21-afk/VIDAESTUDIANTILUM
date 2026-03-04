<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
include("../db.php");
$db = new Conexion();
setlocale(LC_ALL,"es_ES");
if (isset($_FILES["file"]))
{
    $reporte = null;
    for($x=0; $x<count($_FILES["file"]["name"]); $x++){   
        $urlImg = "/archivos/";
        $file = $_FILES["file"];
        $nombre = $file["name"][$x];
        $tipo = $file["type"][$x];
        $ruta_provisional = $file["tmp_name"][$x];
        $size = $file["size"][$x];
        $dimensiones = getimagesize($ruta_provisional);
        $width = $dimensiones[0];
        $height = $dimensiones[1];
        $ext = date('Y')."/".date('m')."/";
        $carpeta = "../../../../archivos/".$ext;

        $piv = explode('.',$nombre);
        $extension = end($piv);
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $newName = 'img-'.substr(str_shuffle($permitted_chars), 0, 16).'.' . $extension;

        if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png')
        {
            $reporte .= "Error $nombre, extension no permitida.";
        }
        // else if($size > 1024*1024)
        // {
        //     $reporte .= "Error $nombre, el tamaño máximo permitido es 1mb";
        // }
        // else if($width > 500 || $height > 500)
        // {
        //     $reporte .= "Error $nombre, la anchura y la altura máxima permitida es de 500px";
        // }
        // else if($width < 60 || $height < 60)
        // {
        //     $reporte .= "Error $nombre, la anchura y la altura mínima permitida es de 60px";
        // }
        else
        {
            $src = $carpeta.$newName;

            //Caragamos imagenes al servidor
            // move_uploaded_file($ruta_provisional, $src);
            if (!file_exists($carpeta)) {
                mkdir($carpeta,0777,true);
                if (file_exists($carpeta)) {
                    if(move_uploaded_file($ruta_provisional,$src)){
                        http_response_code(200);
                        echo "La imagen $nombre ha sido subida con éxito";
                        $cad = "INSERT INTO SYSTEM_UPLOADS (NOMBRE,URL) VALUES('$nombre','".$urlImg.$ext.$newName."')";
                        $sql = $db->query($cad);
                        if ($sql) {} else { echo "error al guardar en sql";$flag = 1;}
                    } else {
                        http_response_code(500);
                        echo "La imagen $newName no se pudo guardar";
                        $flag = 1;
                    }
                }
            } else {
                if(move_uploaded_file($ruta_provisional,$src)){
                    http_response_code(200);
                    echo "La imagen $nombre ha sido subida con éxito";
                    $cad = "INSERT INTO SYSTEM_UPLOADS (NOMBRE,URL) VALUES('$nombre','".$urlImg.$ext.$newName."')";
                    $sql = $db->query($cad);
                    if ($sql) {} else { echo "error al guardar en sql";$flag = 1;}
                } else {
                    http_response_code(500);
                    echo "La imagen $newName no se pudo guardar";
                    $flag = 1;
                }
            }

            //Codigo para insertar imagenes a tu Base de datos.
            //Sentencia SQL
        }
    }

    echo $reporte;
}
