<?php 
if(isset($_GET['code'])){
    $qry = $conn->query("SELECT r.*,CONCAT(c.lastname,', ',c.firstname,' ',c.middlename) as client from `repair_list` r inner join client_list c on r.client_id = c.id where r.code = '{$_GET['code']}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k)){
                $$k = $v;
            }
        }
    }else{
    echo "<script>alert('Código de reparación desconocido'); location.replace('./');</script>";
    }
}
else{
    echo "<script>alert('Se requiere código de reparación'); location.replace('./');</script>";
}
?>
<style>
    @media screen {
        .show-print{
            display:none;
        }
    }
    img#repair-banner{
		height: 45vh;
		width: 20vw;
		object-fit: scale-down;
		object-position: center center;
	}
    .table.border-info tr, .table.border-info th, .table.border-info td{
        border-color:var(--dark);
    }
</style>
<div class="content py-3">
    <div class="card card-outline card-dark rounded-0">
        <div class="card-header rounded-0">
            <h5 class="card-title text-primary">Detalles de reparación</h5>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div id="outprint">
                    <fieldset>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered border-info">
                                    <colgroup>
                                        <col width="30%">
                                        <col width="70%">
                                    </colgroup>
                                    <tr>
                                        <th class="text-muted text-white bg-gradient-dark px-2 py-1">Código</th>
                                        <td><?= ($code) ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted text-white bg-gradient-dark px-2 py-1">Nombre del cliente</th>
                                        <td><?= ucwords($client) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="text-muted border-bottom">Servicios</legend>
                                        <table class="table table-stripped table-bordered" data-placeholder='true' id="service_list">
                                            <colgroup>
                                                <col width="70%">
                                                <col width="30%">
                                            </colgroup>
                                            <thead>
                                                <tr class='bg-gradient-dark text-light'>
                                                    <th class="text-center py-1">Servicio</th>
                                                    <th class="text-center py-1">Tarifa</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $services = $conn->query("SELECT rs.*,s.service FROM `repair_services` rs inner join service_list s on rs.service_id = s.id where rs.repair_id = '{$id}' ");
                                                while($row =  $services->fetch_assoc()):
                                                ?>
                                                    <tr>
                                                        <td class="py-1 px-2"><?= $row['service'] ?></td>
                                                        <td class="py-1 px-2 text-right"><?= number_format($row['fee'],2) ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </fieldset>
                                </div>
                               <! <div class="col-md-6">
                                    <fieldset>
                                        <legend class="text-muted border-bottom">Materiales</legend>
                                        <table class="table table-stripped table-bordered" data-placeholder='true' id="material_list">
                                            <colgroup>
                                                <col width="70%">
                                                <col width="30%">
                                            </colgroup>
                                            <thead>
                                                <tr class='bg-gradient-dark text-light'>
                                                    <th class="text-center py-1">Nombre del material</th>
                                                    <th class="text-center py-1">Costo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $materials = $conn->query("SELECT * FROM `repair_materials` where repair_id = '{$id}' ");
                                                while($row =  $materials->fetch_assoc()):
                                                ?>
                                                    <tr>
                                                        <td class="py-1 px-2"><?= $row['material'] ?></td>
                                                        <td class="py-1 px-2 text-right"><?= number_format($row['cost'],2) ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </fieldset>
                                </div> >
                            </div>
                            <div class="row mt-3">
                                <div class="form-group col-md-12">
                                    <h3><b>Monto total a pagar: <span id="total_amount" class="pl-3"><?=number_format($total_amount,2)?></span></b></h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <small class="text-muted px-2">Observaciones</small><br>
                                    <p><?= str_replace("\n","<br/>",$remarks) ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <small class="text-muted px-2">Estado de pago</small><br>
                                    <?php if($payment_status == 1): ?>
                                        <span class="rounded-pill badge badge-success ml-4">Pagado</span>
                                    <?php else: ?>
                                        <span class="rounded-pill badge badge-dark bg-gradiend-dark ml-4">No pagado</span>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group col-md-4">
                                    <small class="text-muted px-2">Estado</small><br>
                                    <?php 
									switch ($status){
										case 0:
											echo '<span class="ml-4 rounded-pill badge badge-secondary">Pendiente</span>';
											break;
										case 1:
											echo '<span class="ml-4 rounded-pill badge badge-primary">Aprobado</span>';
											break;
										case 2:
											echo '<span class="ml-4 rounded-pill badge badge-info">En progreso</span>';
											break;
										case 3:
											echo '<span class="ml-4 rounded-pill badge badge-warning">Validación</span>';
											break;
										case 4:
											echo '<span class="ml-4 rounded-pill badge badge-success">Realizado</span>';
											break;
										case 5:
											echo '<span class="ml-4 rounded-pill badge badge-danger">Cancelado</span>';
											break;
									}
								?>
                                </div>
                            </div>
                    </fieldset>
                </div>
                
                <hr>
                <div class="rounded-0 text-center mt-3">
                        <a class="btn btn-light border btn-flat btn-sm" href="./?page=check_status" ><i class="fa fa-angle-left"></i> Regresar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#delete_data').click(function(){
			_conf("¿Estás seguro de eliminar <b><?= $code ?>\'s</b> de la reparación de forma permanente?","delete_repair",[$(this).attr('data-id')])
		})
    })
    function delete_repair($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_repair",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("Ocurrió un error.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.replace= './?page=repairs';
				}else{
					alert_toast("Ocurrió un error.",'error');
					end_loader();
				}
			}
		})
	}
</script>