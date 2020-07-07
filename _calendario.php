<?php include("conf/config.php"); ?>
<style>
	.bu{color: #fff; opacity: .8}
	.bu:hover{opacity: 1}
	
<?php $colores=array(
	"#000",
"#C0CA33",
"#00897B",
"#E53935",
"#FB8C00",
"#5E35B1",
"#D81B60",
"#F4511E",
"#546E7A",
"#FDD835",
"#6D4C41",
"#757575",
"#8E24AA",
"#FFB300",
"#3949AB",
"#1E88E5",
"#039BE5",
"#00ACC1",
"#43A047",
"#7CB342");
	
	foreach($colores as $clave => $co) {
		echo ".bc-$clave{border-color:$co}";
		echo ".bu-$clave{background-color:$co}";
	}
	
	?>

</style>
 <section class="vbox">
  <section class="scrollable padder">
    <section class="row m-b-md">
      <div class="col-sm-12">
                    <h3 class="m-b-xs text-black">Calendario <button class="btn btn-danger" onClick="agregarTarea()"><i class="fa fa-bolt icon"> </i> Registrar Tarea Extra</button><div class="btn-group pull-right" data-toggle="buttons">
                      <label class="btn btn-sm btn-bg btn-primary active" id="monthview">
                        <input type="radio" name="options">
                        Mes </label>
                      <label class="btn btn-sm btn-bg btn-success" id="weekview">
                        <input type="radio" name="options">
                        Semana </label>
                      <label class="btn btn-sm btn-bg btn-info" id="dayview">
                        <input type="radio" name="options">
                        Día </label>
                    </div></h3>
      </div>
    </section>
    <section class="row m-b-md">
      <div class="col-sm-9 col-lg-10">
                <section class="panel no-border bg-light">
                  <div class="calendar calendario" id="calendar"> </div>
                </section>      
      </div>
		<div class="col-sm-3 col-lg-2">
                     <section class="panel panel-info">
                     <header class="panel-heading">Usuarios </header>
                      <ul class="list-group lusuarios">
						  <a href="#" class="list-group-item active" onClick="actualizaCal(0,$(this))"> <span class="label label-default"><i class="fa fa-user"></i></span> <b>TODOS</b></a>
                      <?php 
						  $sql="SELECT id, nombre, paterno FROM usuarios ORDER BY nombre";
	
$stmt = $DB->query($sql); //revisión
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						  
						  ?>
						  <a href="#" class="list-group-item" onClick="actualizaCal(<?php echo $row['id']; ?>,$(this))"> <span class="label bu-<?php echo $row['id']; ?>"><i class="fa fa-user"></i></span> <?php echo $row['nombre']; ?> <?php echo $row['paterno']; ?></a>
                     <?php } ?>
                      </ul>
                    </section></div>
    </section>  
    
      
          
  </section>
</section>
<script>
$(document).ready(function() {

    // fullcalendar
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

      $('.calendario').fullCalendar({
        header: {
          left: 'prev',
          center: 'title',
          right: 'next'
        },
        editable: false,
		  firstDay: 1,
		  events: {
				url: '_bin/calendario_obtener.php'
			}
      });

	
	$('#dayview').on('click', function() {
      $('.calendario').fullCalendar('changeView', 'agendaDay')
    });

    $('#weekview').on('click', function() {
      $('.calendario').fullCalendar('changeView', 'agendaWeek')
    });

    $('#monthview').on('click', function() {
      $('.calendario').fullCalendar('changeView', 'month')
    });

  });
	
	function actualizaCal(a,b){
		$('.calendario').fullCalendar('removeEvents');
		$('.calendario').fullCalendar('addEventSource', {url: '_bin/calendario_obtener.php',data: {id: a }} );
		$(".lusuarios a").removeClass("active");
		b.addClass("active");
	}
	
	function agregarTarea(){
		BootstrapDialog.show({
            title: 'Agregar Tarea Extra', //fono,email,clave,intranet,foto
            message: $("<div></div>").load("_bin/tareas_crear.php"),
            type: BootstrapDialog.TYPE_DANGER,
            buttons: [{
                label: 'Agregar',
                cssClass: 'btn-danger',
                hotkey: 13,
                action: function(dialog) {
					//email=?, fono=?, clave=?, intranet=?, foto=?
					
					var inicio = $("#iniciox").val();
					var fin = $("#fin").val();
					var titulo = $("#titulo").val();
					var brief = $("#brief").val();
					var descripcion = $("#descripcion").val();
					

					$.post( "_bin/tareas_registrar.php", {inicio:inicio, fin:fin, titulo:titulo, brief:brief, descripcion:descripcion}, function(data){
						if(data=="ok") {
	                   $.toast({ icon : 'info', heading : 'Registrar Tarea', position: 'top-center', text : 'La tarea se ingresó correctamente.'});
							$('.calendario').fullCalendar('refetchEvents');
							dialog.close();
						} else {
						   BootstrapDialog.alert('Ha ocurrido algún error: '+ data);
						   }
						
					}  );
					

                }
            }, {
                label: 'Cerrar',
                action: function(dialog) {
                    dialog.close();
                }
            }]
        });
		
	}
	
	function tareaextra(a,b){
		var dialogo = BootstrapDialog.show({
            title: 'Tarea Extra', //fono,email,clave,intranet,foto
            message: $("<div></div>").load("_bin/tareas_detalle.php?id="+a+"&b="+b, function(){
				
				
			}),
            type: BootstrapDialog.TYPE_INFO,
            buttons: [{
                label: 'Eliminar',
                cssClass: 'btn-danger btn-editartarea pull-left',
                action: function(dialog) {
					
					BootstrapDialog.confirm({
            title: 'Eliminar Tarea',
            message: '¿Está seguro de eliminar esta tarea?',
            type: BootstrapDialog.TYPE_DANGER,
            btnCancelLabel: 'Cancelar',
            btnOKLabel: 'OK',
            callback: function(result) {
                // result will be true if button was click, while it will be false if users close the dialog directly.
                if(result) {
                    $.post( "_bin/eliminar.php", { tbl: "tareas", id: a}, function(data){
						if(data=="ok") {
							$.toast({ icon : 'success', heading : 'Eliminar Tarea', position: 'top-center', text : 'La tarea se borró correctamente.'});
							$('.calendario').fullCalendar('refetchEvents');
							dialog.close();
						} else {
						   BootstrapDialog.alert('Ha ocurrido algún error al borrar: '+ data);
						   }
						
					}  );
                }else { }
            }
        });
					

                }
            },{
                label: 'Editar',
                cssClass: 'btn-primary btn-editartarea',
                action: function(dialog) {
					//email=?, fono=?, clave=?, intranet=?, foto=?
					
					var inicio = $("#iniciox").val();
					var fin = $("#fin").val();
					var titulo = $("#titulo").val();
					var brief = $("#brief").val();
					var descripcion = $("#descripcion").val();
					

					$.post( "_bin/tareas_editar.php", {id:a,inicio:inicio, fin:fin, titulo:titulo, brief:brief, descripcion:descripcion}, function(data){
						if(data=="ok") {
	                   $.toast({ icon : 'info', heading : 'Editar Tarea', position: 'top-center', text : 'La tarea se modificó correctamente.'});
							$('.calendario').fullCalendar('refetchEvents');
							dialog.close();
						} else {
						   BootstrapDialog.alert('Ha ocurrido algún error: '+ data);
						   }
						
					}  );
					

                }
            }, {
                label: 'Cancelar',
                action: function(dialog) {
                    dialog.close();
                }
            }]
        });
	
	if(b!=<?php echo $_SESSION['idu']?>) {
	dialogo.getModalFooter().find(".btn-editartarea").remove(); }
	
	}
	
</script>
