<style>
    .container {
        border:1px solid black;
        padding:5px 5px 5px 5px;
    }
	.society_data{
		text-align: center;
		padding-top: 8px;
	}    
	
	.left{
	    display:block;
	}
	.right{
	    display:inline-block;
	}
	
	.soc_title_bottom{
    	 margin-top:25px;   
    	 text-align:right;
	}
	
	.signature{
	     margin-top:60px;   
    	 text-align:right;
	}
	
</style>


	<div class="container">
		<div class="society_detail">
			<div>
				<div class="society_data society_name">{{$society_name}}</div>
			</div>
			<div>
				<div class="society_data ">{{$registration_no}}</div>
			</div>
			<div>
				<div class="society_data ">Registration Date : {{$registration_date}}</div>
			</div>			
			<div>
				<div class="society_data ">{{$address}}</div>
			</div>						
			<div>
				<div class="society_data ">Email Id - {{$email_id}} Tel. No - {{$telephone_no}}</div>				
			</div>						
			<div>
				<div class="society_data ">Receipt</div>
			</div>																						
		</div>
		<div class="payment_detail">
		    <table>
		        <tr>
		            <td>Receipt No : {{$receipt_id}}</td>
		            <td>Date : {{$payment_date}}</td>
		        </tr>
		        <tr>
		            <td>Received with Thanks From : {{$member_prefix}}{{$member_name}}</td>
		            <td>Flat No : {{$flat_no}}</td>
		        </tr>		        
		        <tr>
		            <td colspan="2" style="text-transform:uppercase">{{$amount_paid}} ({{numberTowordsEnglish($amount_paid)}}) </td>
		        </tr>
		        <tr>
		            <td colspan="2">By Cheque No. {{$cheque_reference_number}}     Dated On: {{$entry_date}} </td>
		        </tr>
		        <tr>
		            <td colspan="2">Drawn On: </td>
		        </tr>		     
		        <tr>
		            <td colspan="2">Narration: {{$narration}}</td>
		        </tr>		     		        
		    </table>
    		<div class="soc_title_bottom">
    		    <div>For {{$society_name}}</div>
    		</div>					
    		<div class="signature">
    		    <div>CHAIRMAN/SCRETRY/ TREASURER</div>
    		</div>							
    		<div>
    		    <div>This Receipt is Valid Subject to realisation of cheque</div>
    		</div>									
		</div>
	</div>