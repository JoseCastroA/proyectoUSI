/* 
 * Permite la actualización de la información de órdenes de producción
 * Demostración de las posibilidades más usuales de un elemento jqGrid
 */

$(function () {

    $(window).resize(); // forzar un resize para detectar el ancho del contenedor (ver index.js)
    var anchoGrid = anchoContenedor; // se asigna a una variable local el ancho del contenedor
    var altoGrid = $(window).height() - 350;

    if (altoGrid < 200) {
        altoGrid = 200;
    }

    var clase = 'horas_disponibles_monitor';  // la clase que implementa el CRUD para este grid
    var idPager = 'horas_disponibles_monitor-pager';  // la barra de navegación del grid ubicada en la parte inferior

     var field1, check_function1 = function (value, colname)
    {

        if (colname === "hora_inicio") {
            field1 = value;
        }

        if (isNaN(value)|| value<=6 || value >=21) {
            return [false, "El campo debe tener un numero entre 6 y 21"];
        }

        return [true];
    };

    var field1, check_function2 = function (value, colname)
    {

        if (colname === "hora_fin") {
            field1 = value;
        }

        if (isNaN(value)|| value<=6 || value >=21) {
           
            return [false, "El campo debe tener un numero entre 6 y 21"];
        }
        return [true];
    };

    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
        {'label': 'Id Horario Monitor', name: 'id_horario', index: 'id_horario', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Día', name: 'dia', index: 'dia', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},
            editoptions: {dataInit: asignarAncho}
        },
		{'label': 'Hora Inicio', name: 'hora_inicio', index: 'hora_inicio', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1,custom:true,custom_func:check_function1},
            editoptions: {dataInit: asignarAncho}
        },
		{'label': 'Hora Fin', name: 'hora_fin', index: 'hora_fin', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1,custom:true,custom_func:check_function2},
            editoptions: {dataInit: asignarAncho}
        },
		{'label': 'Id Monitor', name: 'id_monitor', index: 'id_monitor', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},
            editoptions: {
                dataUrl: 'controlador/fachada.php?clase=horas_disponibles_monitor&oper=getSelect',
                dataInit: asignarAncho,
                defaultValue: '0'
            }
        }
		
    ];

    // inicializa el grid
    var grid = jQuery('#horas_disponibles_monitor-grid').jqGrid({
        url: 'controlador/fachada.php',
        datatype: "json",
        mtype: 'POST',
        postData: {
            clase: clase,
            oper: 'select'
        },
        rowNum: 10,
        rowList: [10, 20, 30],
        colModel: columnas,
        autowidth: false,
        shrinkToFit: false,
        sortname: 'id_horario', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Horas Disponibles Monitor",
        multiselect: false,
        multiboxonly: true,
        hiddengrid: false,
        cellurl: 'controlador/fachada.php?clase=' + clase,
        cellsubmit: 'remote', // enviar cada entrada
        gridComplete: function () {
            // hacer algo...
        },
        loadError: function (jqXHR, textStatus, errorThrown) {
            alert('Error. No se tiene acceso a los datos de órdenes de producción.')
            console.log('textStatus: ' + textStatus);
            console.log(errorThrown);
            console.log(jqXHR.responseText);
        },
        editurl: "controlador/fachada.php?clase=" + clase
    });

    // inicializa los elementos de la barra de navegación del grid
    grid.jqGrid('navGrid', "#" + idPager, {
        refresh: true,
        edit: true,
        add: true,
        del: true,
        view: false,
        search: true,
        closeOnEscape: false
    }, {// edit
        width: 420,
        modal: true,
        afterSubmit: respuestaServidor
    }, {// add
        width: 420,
        modal: true,
        afterSubmit: respuestaServidor
    }, {// del
        width: 335,
        modal: true, // jqModal: true,
        afterSubmit: respuestaServidor
    }, {// búsqueda
        multipleSearch: true,
        multipleGroup: true}, {}
    );

    /**
     * Asigna ancho a un elemento del grid
     * @param {type} elemento El nombre del elemento 
     * @returns {undefined}
     */
    function asignarAncho(elemento) {
        $(elemento).width(260);
    }

    /**
     * Validación personalizada de los campos de un jqGrid
     * @param {type} valor el dato contenido en un campo
     * @param {type} columna nombre con que está etiquetada la columna
     * @returns {Array} un array indicando si la validación fue exitosa o no
     */
    function validarOrdenProduccion(valor, columna) {

        if (columna == 'Cod_Asignaturas') {
            if (valor === '0') {
                return [false, "Falta seleccionar la Asignatura"];
            }
        }
        if (columna == 'Nom_Asignaturas') {
            if (valor === '0') {
                return [false, "Falta seleccionar la Asignatura"];
            }
        }
        return [true, ""];
    }

});


// JavaScript Document