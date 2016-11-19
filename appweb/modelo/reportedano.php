<?php

/**
 * Description of OrdenProduccion
 * Implementa el CRUD para las órdenes de producción
 * @author Administrador
 */
class reportedano {
    
    /*function add($param) {
        extract($param);
<<<<<<< HEAD:appweb/modelo/reportedano.php
        
        $sql = "INSERT INTO reporte_danos values('$id_reporte','$descripcion','$id_usuario','$id_equipo_sala')";

=======
        $sql = "INSERT INTO pruebas values('$inicio_periodo','$fin_periodo','$grupo','$sala','$dia','$hora','$horas')";
>>>>>>> 90f84c98c0e3ede79f3ee8a2d9311ca2e9277e1e:cronograma.php
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }*/
    function add($param){
        extract($param);

        $inicio = new DateTime($inicio_periodo);
        $fin = new DateTime($fin_periodo);
        $interval = DateInterval::createFromDateString('1 day');
        $fechas = new DatePeriod($inicio, $interval, $fin);



        foreach ($fechas as $fecha) {
            $fecha = $fecha->format('Y-m-d');
            $inicio = "$fecha";
            $fin = "$fecha";
            $diaSemana = date("l", strtotime($fecha));
            if($dia==$diaSemana){
                $sql = "INSERT INTO pruebas values('$inicio','$fin','$grupo','$sala','$dia','$inicio_hora','$fin_hora')";
                $conexion->getPDO()->exec($sql);
            }
        }
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
 
        $sql = "UPDATE reporte_danos
                       SET id_reporte = '$id_reporte', descripcion = '$descripcion',id_usuario='$id_usuario',id_equipo_sala='$id_equipo_sala'
                       WHERE id_reporte = '$id';";
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();

    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM reporte_danos WHERE id_reporte = '$id';");
        echo $conexion->getEstado();

    }

    /**
     * Procesa las filas que son enviadas a un objeto jqGrid
     * @param type $param un array asociativo con los datos que se reciben de la capa de presentación
     */
    function select($param) {
        extract($param);
        $where = $conexion->getWhere($param);
        // conserve siempre esta sintaxis para enviar filas al grid:
        $sql = "SELECT id_reporte,descripcion,id_usuario,id_equipo_sala FROM reporte_danos $where";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['id_reporte'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['id_reporte'],
                        $fila['descripcion'],
                        $fila['id_usuario'],
                        $fila['id_equipo_sala']
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

}
?>