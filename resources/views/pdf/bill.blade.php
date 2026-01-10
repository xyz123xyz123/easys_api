<style>
    .container {
        border:1px solid black;
        padding:5px 5px 5px 5px;
        font-size:12px;
    }
	.society_data{
		text-align: center;
		padding-top: 4px;
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
	
	.societyName{
	    font-weight:bold;
	    text-transform:uppercase;	    
	}	
	
	.payment_info tr td{
	    width:100% !important;
	    text-align:center;
	}
</style>

<div class="container">
    <div class="society_detail">
    	<div>
    		<div class="society_data societyName">{{$society_data['name']}}</div>
    	</div>
    	<div>
    		<div class="society_data ">Registetration No : {{$society_data['registeration_no']}}</div>
    	</div>
    	<div>
    		<div class="society_data ">{{$society_data['address']}}</div>
    	</div>						
    	<div>
    		<div class="society_data ">Email Id - {{$society_data['email_id']}}  Tel. No - {{$society_data['telephone_no']}} </div>				
    	</div>						
    	<div>
    		<div class="society_data ">Maintanance Bill</div>
    	</div>																						
    </div>
    <hr>
    <div class="member_n_bill_data">
        <table class="member_n_bill">
            <tr>
                <td>Unit No: {{$member_data['flat_no']}}</td>
                <td>Unit Area: {{$member_data['area']}}</td>
                <td>Unit Type: {{($member_data['unit_type'] == "R" ? "Residential" : "Commercial")}}</td>
                <td class='bold'>Bill Number: {{$bill_data['bill_no']}}</td>
            </tr>
            <tr>
                <td>Name: {{$member_data['prefix']}}{{$member_data['name']}}</td>
                <td></td>
                <td></td>
                <td class='bold'>Bill Date: {{$bill_data['bill_date']}}</td>
            </tr>
            <tr>
                <td class='bold'>Bill For: {{$bill_data['bill_for']}}</td>
                <td></td>
                <td></td>
                <td class='bold'>Due Date: {{$bill_data['bill_due_date']}}</td>
            </tr>   
            <tr>
                <td>Wing : {{$member_data['wing_id']}}</td>
                <td>Floor No: {{$member_data['floor_no']}}</td>
                <td></td>
                <td></td>
                
            </tr>              
        </table>
    </div>
    <div class="tarrif_data">
        <table class="tarrif">
            <tr>
                <th style="width:1% !important">SR</th>
                <th colspan="3" >Particular Of Charges</th>
                <th style="width:15% !important">Amount</th>
            </tr>
            @foreach($tarrif_data as $key => $tarrif)
            <tr>
                <td>{{($key+1)}}</td>
                <td  colspan="3">{{$tarrif['tarrif']}}</td>
                <td class="text-right">{{$tarrif['tarrif_amount']}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" rowspan="5"></td>
                <td colspan="2"  class="bold">&nbsp; Total</td>
                <td class="text-right" style="font-weight:bold;"><b>{{ $bill_data['bill_amount'] }} </b></td>
            </tr>
            <tr>
                <td colspan="2" >&nbsp; Interest</td>
                <td class="text-right">{{$bill_data['interest'] }}</td>
            </tr>
            <tr>
                <td colspan="2" >&nbsp; Less: Adjustment</td>
                <td class="text-right">{{$bill_data['less_adjustment'] }} </td>
            </tr>
            <tr>
                <td >&nbsp; Principal Arrears</td>                                                                    
                <td class="text-right" >{{ $bill_data['principal_arrears'] }} </td>  
                <td class="text-right" rowspan="2"> {{ $bill_data['principal_credit']}} </td>
            </tr>
            <tr>
                <td>&nbsp; Interest Arrears</td>
                <td class="text-right">{{$bill_data['interest_arrears']}}</td>
            </tr>
            <tr>
                <td colspan="3" class="bold" >&nbsp; <span class='font-weight-bold'>{{ $bill_data['amount_payable_in_words']}} Only</span></td>
                <td class="bold">{{($bill_data['amount_payable'] < 0 ? "Excess Amount Received" : "Total Due Amount & Payable" )}}</td>
                <td class="text-right bold">{{$bill_data['amount_payable']}} </td>
            </tr>            
        </table>
    	
    	<div class="bill_note">
    		<div>Note : </div>
    		<div>{{$society_paramter['bill_note']}}</div>
    	</div>    
    	<div class="soc_title_bottom">
    	    <div>For {{$society_data['name']}}</div>
    	</div>					
    	<div class="signature">
    	    <div>CHAIRMAN/SCRETRY/ TREASURER</div>
    	</div>	        
    </div>
    <hr>
    <div class="recipt_data">
        <div style="text-align:center">Receipt</div>
        <table class="receipt_info">
            <tr>
                <td>Received with thanks from <span class='bold'>{{$member_data['prefix']}}{{$member_data['name']}} </span></td>
                <td></td>
                <td></td>
                <td>Unit Number : {{$member_data['flat_no']}}</td>
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
            @foreach($payment_data['payments'] as $payment)
                <tr>
                <td>{{$payment['receipt_id']}} </td>
                <td>{{$payment['payment_date']}} </td>
                <td>{{$payment['payment_mode']}} </td>
                <td>{{$payment['cheque_reference_number']}} </td>
                <td>{{$payment['entry_date']}} </td>
                <td>{{$payment['bank_name']}} </td>
                <td></td>
                <td>{{$payment['amount_paid']}} </td>
            </tr>            
            @endforeach
            <tr>
                <td colspan="6" class="bold">{{$payment_data['total_paid_in_words']}}</td>
                <td class="bold">Total</td>
                <td class="bold">{{$payment_data['total_paid']}}</td>
            </tr>            
        </table>        
    	<div style="margin-top:10px">
    	    <div>Subject to Realisation of Cheque)</div>
    	</div>				        
    	<div class="soc_title_bottom">
    	    <div>For {{$society_data['name']}}</div>
    	</div>					
    	<div class="signature">
    	    <div>CHAIRMAN/SCRETRY/ TREASURER</div>
    	</div>	                
    </div>
</div>