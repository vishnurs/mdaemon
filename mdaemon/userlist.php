<?php

include_once("includes.php");
require_once("config.php");

$strings = file_get_contents($accountfilename);




$i = 0;
if(isset($_POST['refresh'])) {
	$file = fopen($accountfilename,"r");
	while (($buffer = fgets($file, 40960)) !== false) {
	    $buffer = preg_replace('/(\\\",)/','\\ ",',$buffer); // to handle triling slash problem
	    $buffer = preg_replace('/(\\\"("?),)/',' ',$buffer);
	    $csv_file[] = str_getcsv($buffer);
	}
	if (!feof($file)) {
	    echo "Error: unexpected fgets() fail\n";
	}
	fclose($file);
	
	
	/**Write everything DB **/
	
	foreach($csv_file as $csv) {
		if($csv[0]) {
			if(!in_array("Email", $csv)) {
				
				$result  = $con->query("SELECT id FROM accounts WHERE email='$csv[0]'");
				if(!$result->num_rows) {
					
					mysqli_query($con,"INSERT INTO accounts(email,username,password,preqdate) VALUES('$csv[0]','$csv[3]','$csv[5]','')");	
				}
				else {
				//print_r($csv[4]);die("oo");
				$value = mysqli_fetch_assoc($result);
				//echo "UPDATE accounts SET email='$csv[0]',username='$csv[3]',password='$csv[5]' WHERE id='$value[id]'";
					mysqli_query($con, "UPDATE accounts SET email='$csv[0]',username='$csv[3]',password='$csv[5]' WHERE id='$value[id]'");
				}
			}
		}
	}
	
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
			<form><input type="submit" class="btn btn-primary" value="Refresh File" name="refresh" /></form>
			<div class="alert alert-success <?php if((!isset($_GET['f']) && !$_GET['f']==1)|$_GET['f']!=0) echo "hide"; ?>">
				Password Change Request Send Successfully
			</div>
			<div class="alert alert-danger <?php if((!isset($_GET['f']) && !$_GET['f']==0) || $_GET['f']!=1) echo "hide"; ?>">
				There is an error while sending password change request
			</div>
			<form action="ajax.php" method="post" >
				<table class="table table-striped" id="tbl1">
					<thead>
						<th><input type="checkbox" id="all_check" class="check"/></th>
						<th>Email</th>
						<th style="width: 124px;">UserName</th>
						<th>Date of Email Change Request</th>
						<th>Date of Last Password Change</th>
						<th>Status</th>
						<th>Send</th>	
					</thead>
					<?php
					
					$results = mysqli_query($con, "SELECT * FROM accounts"); 
					while($result = mysqli_fetch_assoc($results)) { 
					
						$status = "";
						/* if($csv) {
						 	
						 	$result = mysqli_query($con, "SELECT preqdate,pchangedate,status FROM accounts WHERE email='$csv[0]'");
							if($result->num_rows) {
								$row = mysqli_fetch_array($result);
								
								
								$preqdate = $row['preqdate'];
								$pchangedate = $row['pchangedate'];
								$status = $row['status'];
							} else {
								$preqdate = "";
								$pchangedate = "";
							}*/
					?>
					<tr>
					<td>
						<input type="checkbox" class="check" name="<?php echo $result['username']; ?>" />
						<input type="hidden" name="<?php echo $result['email'] ?>-email" value="<?php echo $result['email'] ?>" />
						<input type="hidden" name="<?php echo $csv['password'] ?>-pass" value="<?php echo $result['password'] ?>"  />
						<!--<input type="hidden" name="<?php echo $csv[0] ?>-email" value="<?php echo $csv[0] ?>" />
						<input type="hidden" name="<?php echo $csv[5] ?>-pass" value="<?php echo $csv[5] ?>"  />-->
					</td>
					<td><?php echo $result['email']; ?></td>
					<td><?php echo $result['username']; ?> </td>
					<!--<td style="display:none"><?php echo $csv ?></td>-->
					<td><?php echo $result['preqdate']; ?></td>
					<td><?php echo $result['pchangedate'] ?></td>	
					<td style="background-color:<?php if($result['status'] == "1") echo '#FFFF70'; else if($result['status'] == "0") echo '#8BD68B'  ?>" >
						<?php if($result['status'] == "1") {
							echo "Request Send";
						} else if($result['status'] == "0")  {
							echo "Password Changed";
						} else {
							echo "Password Never Changed";
						}?>
					</td>
					<td style="width:100px;">
						<input type="button" class="btn btn-primary sendbtn" value="Send" /><img src="assets/images/loading.gif" width="20px" height="20px" class="hide">
						<input type="hidden" value="<?php echo $result['password'] ?>" />
					</td>
					</tr>
					<?php } ?>
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
 			password = $(this).next().next().val();

 			$(this).next().removeClass("hide").addClass("active_load");
 			
			
			$.post( "ajax.php", {ajax:"1",email: email, username:username,password:password }, function(response) {
				
				$(this).next().addClass("hide");
				if(response == "success") {
					alert("Password Change Request send");
					//$(".active_load").parent().parent().children().eq(3).text(response);
					$(".active_load").parent().parent().children().eq(5).text("Request Send");
					$(".active_load").parent().parent().children().eq(5).css("background-color","#FFFF70");
					$(".active_load").addClass("hide").removeClass("active_load");
				}
				else{
					$(".active_load").addClass("hide").removeClass("active_load");
					alert(response);
				}
			});
 		})
 		
 		
	})
	
</script>