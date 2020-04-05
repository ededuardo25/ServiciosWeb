<?php

try{
        //$Cn = new PDO('mysql:host=localhost; dbname=bdalumnos','root','');    //MYSQL
        $Cn = new PDO('pgsql:host=localhost;port=5432;dbname=pweb;user=postgres;password=eduardo25');
        $Cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $Cn->exec("SET CLIENT_ENCODING TO 'UTF8';");
        //$Cn->exec("SET CHARACTER SET utf8");                                  // MYSQL
}catch(Exception $e){
    die("Error: " . $e->GetMessage());
}

// Función para ejecutar consultas SELECT
function Consulta($query)
{
    global $Cn;
 
    try{    
        $result =$Cn->query($query);
        $resultado = $result->fetchAll(PDO::FETCH_ASSOC); 
        $result->closeCursor();
        return $resultado;
    }catch(Exception $e){
        die("Error en la LIN: " . $e->getLine() . ", MSG: " . $e->GetMessage());
    }
}

// Función que recibe un insert y regresa el consecutivo que le genero en la llave primaria
// por ejemplo: Insert Into cuerpo.clasif (nomclasif) values ('Articulo en Extenso') 
// Returning idclasif, nomclasif;
function EjecutaConsecutivo($sentencia, $llave){
    global $Cn;
    try {
        $result = $Cn->query($sentencia);
        $resultado = $result->fetchAll(PDO::FETCH_ASSOC);
        $result->closeCursor();
        return $resultado[0][$llave];
    } catch (Exception $e) {
        die("Error en la linea: " + $e->getLine() + " MSG: " + $e->GetMessage());
        return 0;
    }
}
// Sirve para ejecutar una sentencia INSERT, UPDATE O DELETE
function Ejecuta ($sentencia){
    global $Cn;
    try {
        $result = $Cn->query($sentencia);
        $result->closeCursor();
        return 1; // Exito  
    } catch (Exception $e) {
        //die("Error en la linea: " + $e->getLine() + " MSG: " + $e->GetMessage());
        return 0; // Fallo
    }
}

//--------------------------Sensado-------------------------------
function consultaInformeSensado($ini,$fin)
{
    $query = "SELECT idsen,nomsensor,valor FROM cuerpo.sensado WHERE valor >= $ini And valor <= $fin ORDER BY nomsensor";
    return Consulta($query);
}

function consultaSensado()
{
    $query = "SELECT idsen,nomsensor,valor FROM cuerpo.sensado ORDER BY nomsensor";
    return Consulta($query);
}

function InsertarSensado(&$post){
    $noms = $post['noms'];
    $val = $post['valor'];
    $sentencia = "INSERT INTO cuerpo.sensado(nomsensor,valor) values('$noms', $val) RETURNING idsen";
    $id = EjecutaConsecutivo($sentencia,"idsen");
    $post['ids']=$id; 
    return $id;
}

function ActualizarSensado($post){
    $ids = $post['ids'];
    $noms = $post['noms'];
    $val = $post['valor'];
    $sentencia = "UPDATE cuerpo.sensado SET nomsensor='$noms', valor=$val WHERE idsen=$ids";
    return Ejecuta($sentencia);
}

function EliminarSensado($post){
    $ids = $post['ids'];
    $sentencia = "DELETE FROM cuerpo.sensado WHERE idsen=$ids";
    return Ejecuta($sentencia);
}

function InsertaSen(&$post){
    $id = $post['idsen'];
    $noms = $post['nomsensor'];
    $val = $post['valor'];
    $sentencia = "INSERT INTO cuerpo.sensado(idsen,nomsensor,valor) values($id, '$noms', $val)";
    return Ejecuta($sentencia);
}

function ActualizarSen($post){
    $ids = $post['idsen'];
    $noms = $post['nomsensor'];
    $val = $post['valor'];
    $sentencia = "UPDATE cuerpo.sensado SET nomsensor='$noms', valor=$val WHERE idsen=$ids";
    return Ejecuta($sentencia);
}

function InsActSen($post){
    if (InsertaSen($post)!=1)
        return ActualizarSen($post);
    else
        return 1;
}

function obj2array($obj) {
    $out = array();
    foreach ($obj as $key => $val) {
      switch(true) {
          case is_object($val):
           $out[$key] = obj2array($val);
           break;
        case is_array($val):
           $out[$key] = obj2array($val);
           break;
        default:
          $out[$key] = $val;
      }
    }
    return $out;
  } 
  
?>