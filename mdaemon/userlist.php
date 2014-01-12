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
						<th>Status</th>
						<th>Send</th>	
					</thead>
					
					<?php foreach($csv_file as $csv) {
						 if($csv) {
						 	
						 	$result = mysqli_query($con, "SELECT preqdate,pchangedate,status FROM accounts WHERE email='$csv[0]'");
							if($result->num_rows) {
								$row = mysqli_fetch_array($result);
								
								
								$preqdate = $row['preqdate'];
								$pchangedate = $row['pchangedate'];
								$status = $row['status'];
							} else {
								$preqdate = "";
								$pchangedate = "";
							}
					?>
					<tr>
					<td>
						<input type="checkbox" class="check" name="<?php echo $csv[1]; ?>" value="<?php echo $csv[1]; ?>" />
						<input type="hidden" name="<?php echo $csv[1] ?>-email" value="<?php echo $csv[0] ?>" />
						<input type="hidden" name="<?php echo $csv[1] ?>-pass" value="<?php echo $csv[2] ?>"  />
					</td>
					<td><?php echo $csv[0]; ?></td>
					<td><?php echo $csv[1]; ?></td>
					<td><?php echo $preqdate; ?></td>
					<td><?php echo $pchangedate; ?></td>	
					<td style="background-color:<?php if($status == "1") echo '#FFFF70'; else if($status == "0") echo '#8BD68B'  ?>" >
						<?php if($status == "1") {
							echo "Request Send";
						} else if($status == "0")  {
							echo "Password Changed";
						} else {
							echo "Password Never Changed";
						}?>
					</td>
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
 			email = $(this).parent().parent().children().eq(1).text();
 			username = $(this).parent().parent().children().eq(2).text();
 			password = $(this).parent().parent().children().eq(3).text();
 			$(this).next().removeClass("hide").addClass("active_load");
 			
			
			$.post( "ajax.php", {ajax:"1",email: email, username:username,password:password }, function(response) {
				
				$(this).next().addClass("hide");
				if(response != "fail") {
					$(".active_load").parent().parent().children().eq(3).text(response);
					$(".active_load").addClass("hide").removeClass("active_load");
				}
				else{
					
				}
			});
 		})
 		
 		
	})
	
</script>