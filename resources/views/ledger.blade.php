<style type="text/css">
	.container {
	}

	.ledgerdata {
		width: 100%;
		border-collapse: collapse;
		font-family: arial sans-serif;
		margin-top: 10px;
	}

	.ledgerdata th,.ledgerdata tbody tr td,.ledgerdata tfoot tr td {
		border:1px solid black;			
		padding:2px;
	}
	
	.ledgerdata tbody tr td {
	   text-align: center !important;
	   padding:15px;
	}
	
    .ledgerdata tbody tr td:nth-child(2) {
	   text-align: left !important;
	}
	
    .ledgerdata tfoot tr td{
	   text-align: center !important;
	}	
	
	.header {
	    width: 100%;
	}
	
	.header tr td{
	    text-align:center;
	}	
	
	.ledgerdata th{
	    text-align:center;
	}
	
	.society_data{
		text-align: center;
		padding-top: 8px;
	}
	.member_data{
		padding-top: 10px;	
		width:100%;
	}

	.society_name{
		text-transform: uppercase;		
	}

	.member_data tr td{
		padding-right: 15px;
	}
	
	.societyName {
	    font-weight:bold;
	    text-transform:uppercase;
	}
	
    .bold{
	    font-weight:bold;
	}		
</style>
	<div class="container">
	    @if(!empty($member_data))
        <table class="header">
            <tr>
                <td colspan="5" class="societyName">{{$member_data['society_name']}}</td>
            </tr>
            <tr>
                <td colspan="5">Registetration No : {{$member_data['registration_no']}}</td>
            </tr>
            <tr>
                <td colspan="5">{{$member_data['address']}}</td>
            </tr>
            <tr>
                <td colspan="5">Email Id - {{$member_data['email_id']}}  Tel. No - {{$member_data['telephone_no']}} </td>
            </tr> 
            <tr>
                <td></td>
                <td></td>
                <td></td><td></td><td></td>
            </tr>
                <tr>
                    <td colspan="5" class="bold">Ledger</td>
                </tr>            
        </table>
		<div>
			<table class="member_data">
				<tr>
					<td>Building Name :  {{$member_data['building_name']}}</td>
					<td></td>
					<td>Wing :  {{$member_data['wing_id']}}</td>
					<td></td>
				</tr>
				<tr>
					<td>Member Name :  {{$member_data['member_name']}}</td>
					<td></td>
					<td>Unit No :  {{$member_data['flat_no']}}</td>
					<td></td>
				</tr>				
			</table>
		</div>			
		<table class="ledgerdata">
    			<tr>
    				<th style="width:15% !important">DATE</th>
    				<th>PARTICULAR</th>
    				<th style="width:15% !important">DR.AMOUNT</th>
    				<th style="width:15% !important">CR.AMOUNT</th>
    				<th style="width:15% !important">BALANCE</th>
    			</tr>	
    		 <tbody>
			@foreach($ledger_data as $data)
			<tr>
				<td>{{$data['formattedDate']}}</td>
				<td>{{$data['particular']}}</td>
				<td>{{$data['debit']}}</td>
				<td>{{$data['credit']}}</td>
				<td>{{$data['total']}}</td>
			</tr>	
			@endforeach
			</tbody>
			<tfoot>
    			<tr class="tfoot">
    				<td colspan="2" >Total</td>
    				<td>{{$total_debit}}</td>
    				<td>{{$total_credit}}</td>
    				<td style="font-weight:bold">{{$total_balance}}</td>
    			</tr>
			</tfoot>
		</table>
		@endif
</div>



