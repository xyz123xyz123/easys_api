<!DOCTYPE html>
<html>
    <head>

<style>
    .container {
        border:1px solid black;
        padding:5px 5px 5px 5px;
    }
	.society_data{
		text-align: center;
		padding-top: 4px;
	}    
	
	.header,.bottom {
	    width: 100%;
	}
	.header tr td{
	    text-align:center;
	}
	
	.bottom{
	    margin-top:10px;
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
	
	.member_n_bill{
	    width: 100%;
	}
	
	.tarrif,.payment_info,.receipt_info{
		width: 100%;
		border-collapse: collapse;
		font-family: arial sans-serif;
		margin-top: 10px;
	}
	.tarrif th,.tarrif tr td {
			border:1px solid black;			
			text-align: left;
			padding:2px;
	}
	
	.payment_info th,.payment_info tr td {
			border:1px solid black;			
			text-align: left;
			padding:2px;
	}	
	
	.soc_title_bottom{
    	 margin-top:25px;   
    	 text-align:right;
    	 font-weight:bold;
	}
	
	.signature{
	     margin-top:60px;   
    	 text-align:right;
    	 font-weight:bold;
	}
	
	table th{
	    font-weight:bold;
	    text-align:center !important;
	}
    
    .bold{
	    font-weight:bold;
	}	
	.text-right{
	    text-align:right !important;
	}
	
	.payment_info tr td{
	    text-align:center;
	}
</style>
</head>
<body>
<div class="container">
    <div>
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
                <td colspan="5">Email Id - {{$email_id}}  Tel. No - {{$telephone_no}} </td>
            </tr> 
            <tr>
                <td></td>
                <td></td>
                <td></td><td></td><td></td>
            </tr>
        </table>
    </div>
    <hr>
    <div class="recipt_data">
        <div style="text-align:center" class="bold" >Payment Detail</div>
        <table class="receipt_info">
            <tr>
                <td>Received with thanks from <span class='bold'>{{$member_prefix}}{{$member_name}} </span></td>
                <td></td>
                <td></td>
                <td>Unit Number : {{$flat_no}}</td>
            </tr>
        </table>
        <table class="payment_info">
            <tr>
                <th>RECEIPT</th>
                <th>DATE</th>
                <th>PAYMENT MODE.</th>
                <th>CHQ NO.</th>
                <th>CHQ DATE </th>
                <th>BANK & BRANCH</th>
                <th>TOWARDS BILL NUMBER.</th>
                <th>AMOUNT</th>
            </tr>
                <tr>
                <td>{{$receipt_id}} </td>
                <td>{{$payment_date}} </td>
                <td>{{$payment_mode}} </td>
                <td>{{$cheque_reference_number}} </td>
                <td>{{$entry_date}} </td>
                <td>{{$bank_name}} </td>
                <td></td>
                <td>{{$amount_paid}} </td>
            </tr>            
            <tr>
                <td colspan="6" class="bold">{{$total_paid_in_words}}</td>
                <td class="bold">Total</td>
                <td class="bold">{{$total_paid}}</td>
            </tr> 
        </table>        
        <table class="bottom">
            <tr>
                <td colspan="8" >Subject to Realisation of Cheque</td>
            </tr>
            <tr>
                <td colspan="8" style="float:right;" class="bold">For {{$society_name}}</td>
            </tr>
            <tr>
                <td colspan="8" style="float:right;margin-top:40px;" class="bold">CHAIRMAN/SCRETRY/ TREASURER</td>
            </tr>            
        </table>	                
    </div>
</div>
</body>
</html>