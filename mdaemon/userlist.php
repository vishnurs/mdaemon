<?php

include_once("includes.php");
require_once("config.php");

$strings = file_get_contents($accountfilename);


$file = fopen($accountfilename,"r");

$i = 0;
while(!feof($file))
{
	if($i != 0)	{
		$csv_file[] =fgetcsv($file);	
	}
	else {
		fgetcsv($file);
	}
	$i++;	
}
//echo "<pre>";
//$r = explode(" ", $csv_file);
//print_r($r);die();
//print_r($csv_file);
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-offset-4">
				<h1>User ListinG</h1>
			</div>
		</div>	
	</div>
	<br /><br /><br /><br />
	<div class="row">
		<div class="col-md-12">
			<form action="ajax.php" method="post" >
				<table class="table table-striped" id="tbl1">
					<thead>
						<th><input type="checkbox" id="all_check" class="check"/></th>
						<th>Email</th>
						<th>UserName</th>
						<th>Date of Email Change Request</th>
						<th>Date of Last Password Change</th>
						<th>Icon</th>
						<th>Send</th>	
					</thead>
					
					<?php foreach($csv_file as $csv) { if($csv) {?>
					<tr>
					<td>
						<input type="checkbox" class="check" name="<?php echo $csv[1]; ?>" value="<?php echo $csv[1]; ?>" />
						<input type="hidden" name="<?php echo $csv[1] ?>-email" value="<?php echo $csv[0] ?>" />
						<input type="hidden" name="<?php echo $csv[1] ?>-pass" value="<?php echo $csv[2] ?>"  />
					</td>
					<td><?php echo $csv[0]; ?></td>
					<td><?php echo $csv[1]; ?></td>
					<td><?php ?></td>
					<td><?php ?></td>	
					<td></td>
					<td style="width:100px;">
						<input type="button" class="btn btn-primary sendbtn" value="Send" /><img src="assets/images/loading.gif" width="20px" height="20px" class="hide">
					</td>
					</tr>
					<?php } }?>
					</form>
				</table>
				
		</div>
	</div>
	
</div>

<script>
	$(function() {
		$('#all_check').click(function() {
			if($(this).is(':checked')) {
				$('.check').prop('checked', true);
			}
			else {
				$('.check').prop('checked', false);
			}
		})
		/*$.fn.dataTableExt.afnSortData['dom-checkbox'] = function  ( oSettings, iColumn )
		{
		    var aData = [];
		    $( 'td:eq('+iColumn+') input', oSettings.oApi._fnGetTrNodes(oSettings) ).each( function () {
		        aData.push( this.checked==true ? "1" : "0" );
		    } );
		    return aData;
		}*/
		$("#tbl1").dataTable({
        "aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 0,6 ] }
       	]/*,
	    "aoColumns": [
            {"sSortDataType": "dom-checkbox"},
            null,
            null,
            null,
            null,
            null,
            null
         ] */
        });
 		$('<input type="submit" class="btn btn-primary pull-right" value="Send" />').insertAfter("#tbl1_filter");
 		$("#tbl1_filter").addClass("pull-left");
 		$(".sendbtn").click(function() {
 			$(this).next().removeClass("hide");
 			val = $(this).parent().parent().children().eq(1).text();
 			$(this).children().eq(0).removeClass("hide");
 			
			
			$.post( "ajax.php", {ajax:"1",addr: val }, function(response) {
				
				$(this).children().eq(0).addClass("hide");
				if(response == "success") {
					$(this).next().addClass("hide")
				  	$("#info").html("Comments removed successfully");
				  	$("#danger").html("");
				}
				else{
					$("#info").html("");
					$("#danger").html("There is an unexpected error");
				}
			});
 		})
 		
 		
	})
	
</script>