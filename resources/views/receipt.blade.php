<style>
    .container {
        border:1px solid black;
        padding:5px 5px 5px 5px;
    }
    
	.header,.payment_detail,.bottom {
	    width: 100%;
	}
	.header tr td{
	    text-align:center;
	}    
	
	.bottom{
	    margin-top:10px;
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
    .bold{
	    font-weight:bold;
	}		
</style>


	<div class="container">
            <table class="header">
                <tr>
                    <td colspan="5">{{$society_name}}</td>
                </tr>
                <tr>
                    <td colspan="5">Registetration No : {{$registration_no}}</td>
                </tr>
                <tr>
                    <td colspan="5">{{$address}}</td>
                </tr>
                <tr>
                    <td colspan="5">Email Id - {{$email_id}} Tel. No - {{$telephone_no}}</td>
                </tr> 
                <tr>
                    <td></td>
                    <td></td>
                    <td></td><td></td><td></td>
                </tr>
                <tr>
                    <td colspan="5" class="bold">Receipt</td>
                </tr>
            </table>			
		<div>
		    <table class="payment_detail">
		        <tr>
		            <td>Receipt No : {{$receipt_id}}</td>
		            <td></td>
		            <td></td>
		            <td>Date : {{$payment_date}}</td>
		        </tr>
		        <tr>
		            <td>Received with Thanks From : {{$member_prefix}}{{$member_name}}</td>
		            <td></td>
		            <td></td>
		            <td>Flat No : {{$flat_no}}</td>
		        </tr>		        
		        <tr>
		            <td colspan="4" style="text-transform:uppercase">{{$amount_paid}} ({{numberTowordsEnglish($amount_paid)}}) </td>
		        </tr>
		        <tr>
		            <td colspan="4">By Cheque No. {{$cheque_reference_number}}     Dated On: {{$entry_date}} </td>
		        </tr>
		        <tr>
		            <td colspan="4">Drawn On: </td>
		        </tr>		     
		        <tr>
		            <td colspan="4">Narration: {{$narration}}</td>
		        </tr>		     		        
		    </table>
            <table class="bottom">
                <tr>
                    <td colspan="4" style="float:right;" class="bold">For {{$society_name}}</td>
                </tr>
                <tr>
                    <td colspan="4" style="float:right;margin-top:40px;" class="bold">CHAIRMAN/SCRETRY/ TREASURER</td>
                </tr>            
                <tr>
                    <td colspan="4" >This Receipt is Valid Subject to realisation of cheque</td>
                </tr>                
            </table>	                		    
		</div>
	</div>