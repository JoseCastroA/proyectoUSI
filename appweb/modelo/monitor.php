<?php

class monitor {
    
    function add($param) {
        extract($param);
        
        $sql = "INSERT INTO monitor values('$id_monitor','$id_usuario')";
        $sql1 = "INSERT INTO usuario values('$id_usuario','$tipo_doc','$nombre','$apellido','$correo_login','$contrasena')";
        $conexion->getPDO()->exec($sql1);
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
 
        $sql = "UPDATE monitor
                       SET id_monitor = '$id_monitor', id_usuario = '$id_usuario'
                       WHERE id_monitor = '$id_monitor';";
        $sql1 = "UPDATE usuario
                       SET id_usuario = '$id_usuario', tipo_doc = '$tipo_doc', nombre = '$nombre', apellido = '$apellido', correo_login = '$correo_login', contrasena = '$contrasena'
                       WHERE id_usuario = '$id_usuario';";
        $conexion->getPDO()->exec($sql1);
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();

    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM monitor WHERE id_usuario = '$id';");
        $conexion->getPDO()->exec("DELETE FROM usuario WHERE id_usuario = '$id';");
        echo $conexion->getEstado();

    }


    public function getSelect($param) {
        $json = FALSE;
        extract($param);
        $select = "";
        $select .= "<option value='0'>Seleccione un monitor</option>";
        foreach ($conexion->getPDO()->query("SELECT e.id_usuario,u.nombre, u.apellido FROM monitor e inner join usuario u on e.id_usuario = u.id_usuario ") as $fila) {
            $descripcion = $fila['id_usuario'].'-'.$fila['nombre'].' '.$fila['apellido'];
            $select .= "<option value='{$fila['id_usuario']}'>$descripcion</option>";
        }
        echo $json ? json_encode($select) : ("<select id='$id'>$select</select>");
    }

    /**
     * Procesa las filas que son enviadas a un objeto jqGrid
     * @param type $param un array asociativo con los datos que se reciben de la capa de presentación
     */
    function select($param) {
        extract($param);
        $where = $conexion->getWhere($param);
        // conserve siempre esta sintaxis para enviar filas al grid:
        $sql = "SELECT e.id_monitor, e.id_usuario, u.tipo_doc, u.nombre, u.apellido,u.correo_login, u.contrasena FROM monitor e inner join usuario u on e.id_usuario = u.id_usuario ";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['id_usuario'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['id_monitor'],
                        $fila['id_usuario'],
                        $fila['tipo_doc'],
                        $fila['nombre'],
                        $fila['apellido'],
                        $fila['correo_login'],
                        $fila['contrasena']
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

}


