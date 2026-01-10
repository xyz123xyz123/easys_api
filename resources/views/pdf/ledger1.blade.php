<style type="text/css">
	.container {
		font-size:12px !important;
	}

	.ledgerdata {
		width: 100%;
		border-collapse: collapse;
		font-family: arial sans-serif;
		margin-top: 10px;
	}
	.ledgerdata th,.ledgerdata tr td {
			border:1px solid black;			
			text-align: left;
			padding:2px;
	}
	
	.ledgerdata th{
	    text-align:center;
	}
	
	.ledgerdata tr td{
	    text-align:right;
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
</style>
	<div class="container">
		<div class="society_detail">
			<div>
				<div class="society_data society_name" style="font-weight:bold">{{$member_data['society_name']}}</div>
			</div>
			<div>
				<div class="society_data ">{{$member_data['registration_no']}}</div>
			</div>
			<div>
				<div class="society_data ">{{$member_data['address']}}</div>
			</div>						
			<div>
				<div class="society_data ">Email Id - {{$member_data['email_id']}} Tel. No - {{$member_data['telephone_no']}}</div>				
			</div>						
			<div>
				<div class="society_data ">Member Ledger</div>
			</div>																						
		</div>
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
			@foreach($ledger_data as $data)
			<tr>
				<td>{{$data['formattedDate']}}</td>
				<td>{{$data['particular']}}</td>
				<td>{{$data['debit']}}</td>
				<td>{{$data['credit']}}</td>
				<td>{{$data['total']}}</td>
			</tr>	
			@endforeach
			<tr>
				<td colspan="2" style="text-align: right;" >Total</td>
				<td>{{$total_debit}}</td>
				<td>{{$total_credit}}</td>
				<td style="font-weight:bold">{{$total_balance}}</td>
			</tr>				
		</table>
</div>



