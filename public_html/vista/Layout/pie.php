<?php

function f_admin_pie() {
?>
	<!-- Pie de Pagina -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> <?php echo VERSION ?>
        </div>
        <strong>Copyright &copy; <?php echo COPYRIGHT ?> <a href="<?php echo COPYRIGHT_WEB ?>" target="_blank"><?php echo COPYRIGHT_AUTOR ?></a>.</strong> Todos los derechos reservados.
    </footer>
    <!-- Fin de Pie de Pagina -->         
<?php
}  

// Carga Fin
function f_admin_script($as_columna, $as_orden){
?>

<!-- jQuery 3 -->  
<script src="../recursos/js/jquery.min.js"></script>

<!-- jQuery UI 1.11.4 -->
<script src="../recursos/js/jquery-ui.min.js"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<!-- Bootstrap 3.3.7 -->
<script src="../recursos/js/bootstrap.min.js"></script>

<!-- DataTables -->
<script src="../recursos/js/jquery.dataTables.min.js"></script>
<script src="../recursos/js/dataTables.bootstrap.min.js"></script>

<!-- DataTables Select -->
<script src="../recursos/js/dataTables.select.min.js"></script>

<?php
if ( strlen($as_columna)==0 or ($as_columna == '') ) {
    $as_columna = 0;
    $as_orden   = 'asc';
};
?>


<!-- Configurar DataTables -->
<script type="text/javascript" language="javascript" class="init">
	$(document).ready(function() {
        <?php
            echo "var id_columna ='$as_columna';";
            echo "var id_orden ='$as_orden';";
        ?>
        // Lista
        //$('#lista').DataTable();
		$('#lista').DataTable({
            //"order": [[0, 'desc']],
            "order": [[id_columna, id_orden]],
        });
            
        // Lista Detalle
        $('#listaDetalle').DataTable( {
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            } ],
            select: {
                //style:    'os',
                //selector: 'td:first-child',
                style:'single',     // Slección Simple
                items:'row',        // Seleccionar Fila
                toggleable: false,  // No permite deseleccionar
                blurable: true      // Color de Selección
            },
            order: [[ 1, 'asc' ]],  // Orden
            language: {             // Lenguaje
                select: {
                    rows: {
                        _: "Ud. seleccionó %d filas",
                        0: "Clic en una fila para seleccionar",
                        1: "1 Fila seleccionada"
                    }
                }        
            }
        } );
	} );
</script>

<!-- bootstrap datepicker -->
<script src="../recursos/js/bootstrap-datepicker.min.js"></script>

<!-- AdminLTE App -->
<script src="../recursos/js/adminlte.min.js"></script>

</body>
</html>
<?php
}