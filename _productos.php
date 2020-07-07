<?php include("conf/config.php"); ?><section class="vbox">
  <section class="scrollable padder">
    <section class="row m-b-md">
      <div class="col-sm-12">
                    <h3 class="m-b-xs text-black">Productos <button class="btn btn-primary" onClick="carga('bin/productos_crear')"><i class="fa fa-archive icon"> </i> Agregar Producto</button></h3>
      </div>
    </section>
    <section class="row m-b-md">
      <div class="col-sm-12">
        <table id="tabladatos" class="display table table-responsive table-striped table-bordered" cellspacing="0" cellpadding="2" width="100%">
        <thead>
            <tr>
               <th>Descripción</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Descripción</th>
                <th>Valor</th>
            </tr>
        </tfoot>
    </table>      
      </div>
    </section>    
  </section>
</section>
<script>
    var tabla = $('#tabladatos').DataTable( {
		"dom": 'Bflrtip',
        "processing": true,
        "serverSide": true,
		"lengthChange": true,
		"fixedHeader": true,
		"scrollY": $("#contenido").height()-290,
  "scrollCollapse": true,
		"buttons": [
			{ extend: 'copy', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copiar', className:'btn btn-primary' },
			{ extend: 'excel', text: '<i class="i i-file-excel"></i>', titleAttr: 'Exportar lo visualizado a Excel', className:'btn btn-success' },
			{ extend: 'pdf', text: '<i class="i i-file-pdf"></i>', titleAttr: 'Exportar lo visualizado a PDF', className:'btn btn-info' },
			{ extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir lo visualizado', className:'btn btn-warning' }
    ],
		"language": {
            "url": "js/datatables/Spanish.json"
        },
        "ajax": "_bin/productos_grid.php"
    } );
	
	$('#tabladatos tbody').on( 'click', 'tr', function () {
		var ids=$(this).attr("id").replace("r_","");
        console.log(ids);
		cargap('bin/productos_detalle.php?f='+ids)
    } );

/*new $.fn.dataTable.Buttons( tabla, {
    "buttons": [
        "copy", "excel", "pdf"
    ]
} );*/
	
	/*tabla.buttons().container()
    .appendTo( $('.col-sm-6:eq(0)', tabla.table().container() ) );*/

</script>
