@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">
    <style>
        .all_invoice_table td{
            vertical-align: middle !important;
        }
    </style>
@endpush

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="flex-box gap-10 align-center">
                <div class="flex-grow">
                    Invoices
                </div>
                <div class="flex-none initial-case">
                    <span>{{$subUser->name}} ({{$subUser->email}})</span>
                </div>
            </div>    
        </div>
        <div class="panel-wrapper collapse in" aria-expanded="true">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-stripped table-bordered all_invoice_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Splits</th>
                                <th>Method</th>
                                <th>Paid On</th>
                                <th>Transaction</th>
                                <th>Action</th>
                                <th><a class="btn btn-info all_invoice_update_btn" href="#bulk-update-invoice-modal" data-toggle="modal">Update</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalUnpaid = 0;
                                $serial = count($invoices);
                            @endphp
                            @foreach ($invoices as $invoice)
                                <tr class="invoice_update_form" data-id="{{$invoice->id}}">
                                    <td>{{$serial}}</td><td>{{$invoice->start_date}}</td><td>{{$invoice->end_date}}</td><td>{{$invoice->description}}</td><td>${{ number_format($invoice->total, 2) }}</td>
                                    <td>
                                        <div class="flex-box justify-space-between gap-5"><div class="font-normal text-primary">Credits: </div> <div>${{number_format($invoice->credit, 2)}}</div></div>
                                        @if (!empty($invoice->splits))
                                            @foreach ($invoice->splits as $split)
                                                <div class="flex-box justify-space-between gap-5"><div class="font-normal">{{ $split['name'] }}: </div> <div>${{number_format($split['amount'], 2)}}</div></div>
                                            @endforeach
                                        @endif
                                    </td>
                                    @if($invoice->handled)
                                        <td>{{$invoice->method}}</td><td>{{$invoice->paid_on}}</td><td>{{$invoice->transaction_code}}</td><td colspan="2">{{$invoice->comment}}</td>
                                    @else
                                        <td class="payment_method_area">
                                            <select class="form-control payment_method">
                                                <option value="">Select</option>
                                                <option value="Wire">Wire</option>
                                                <option value="Paypal">Paypal</option>
                                            </select>
                                        </td>
                                        <td class="datepicker_field_area">
                                            <div class="" style="position:relative;">
                                                <input class="form-control datepicker_field" placeholder="Choose date" style="width:120px;" readonly value="{{ date('Y-m-d') }}">
                                            </div>
                                        </td>
                                        <td class="transaction_area">
                                            <input class="form-control transaction m-b-10" placeholder="Transaction code">
                                            <input class="form-control comment" placeholder="Comment">
                                        </td>
                                        <td>
                                            <a class="btn btn-info invoice_update_btn" href="#">Update</a>
                                        </td>
                                        <td>
                                            <div class="checkbox checkbox-info text-center invoice_update_checkbox_con">
                                                <input class="invoice_update_checkbox" type="checkbox" value="1" id="invoice_update_checkbox_{{$invoice->id}}">
                                                <label for="invoice_update_checkbox_{{$invoice->id}}"></label>
                                            </div>
                                        </td>
                                        @php
                                            $totalUnpaid += $invoice->total;
                                        @endphp
                                    @endif
                                    @php
                                        $serial--;
                                    @endphp
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <th colspan="8">Total Unpaid:</th>
                            <th colspan="2" class="text-right">${{ number_format($totalUnpaid, 2) }}</th>
                        </tfoot>
                    </table>
                </div>
          </div>
    </div>

    <div class="modal" id="bulk-update-invoice-modal">
        <div class="modal-dialog">
          <div class="modal-content">
              <form class="bulk-update-invoice-form">
                  <div class="modal-header">
                      <h4 class="modal-title">Bulk Update</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                      <div class="clearfix">
                          <div class="form-group">
                              <label>Payment Method</label>
                              <select class="form-control payment_method" name="payment_method" required>
                                  <option value="">Select</option>
                                  <option value="Wire">Wire</option>
                                  <option value="Paypal">Paypal</option>
                              </select>
                          </div>
                          <div class="form-group">
                              <label>Paid On</label>
                              <div class="" style="position:relative;">
                                  <input class="form-control datepicker_field" name="date" placeholder="Choose date"  readonly value="<?php echo date('Y-m-d'); ?>" required>
                              </div>
                          </div>
                          <div class="form-group">
                              <label>Transaction Code</label>
                              <input class="form-control transaction" name="transaction" placeholder="Transaction code" required>
                          </div>
                          <div class="form-group">
                              <label>Comment</label>
                              <input class="form-control comment" name="comment" placeholder="Comment">
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-info" >Update</button>
                  </div>
              </form>
          </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $(function(){
            $('.datepicker_field').datepicker({
                clearBtn: true,
		        format: "yyyy-mm-dd",
		        orientation: "top left",
		        autoclose: true,
		        todayHighlight: true,
            });

            $(document).on('click', '.invoice_update_btn', function(e){
		    	e.preventDefault();
		    	var el = $(this);
		    	var area = el.closest('.invoice_update_form');
		    	var id = area.attr('data-id');
		    	var payment_method = area.find('select.payment_method').val();
		    	var datepicker_field = area.find('.datepicker_field').val();
		    	var transaction = area.find('.transaction').val();
		    	var comment = area.find('.comment').val();
                var data = {
                    _token: csrfToken,
                    _method: 'PUT',
                    id: id,
                    payment_method: payment_method,
                    date: datepicker_field,
                    transaction: transaction,
                    comment: comment,
                };
		    	ajax({
		    		url: listUrls.adminInvoiceUpdate(id),
		    		type: 'POST',
		    		data: data,
		    		successCallback: function(resp, el){
                        el.area.find('.payment_method_area').html(el.data.payment_method);
                        el.area.find('.datepicker_field_area').html(el.data.datepicker_field);
                        el.area.find('.transaction_area').html(el.data.transaction);
                        el.area.find('.invoice_update_btn').remove();
                        el.area.find('.invoice_update_checkbox_con').remove();
                        el.area.find('>td').last().remove();
                        el.area.find('>td').last().attr('colspan', 2).html(el.data.comment);
		    		}
		    	},{area:area, data:data});
		    });

		    $(document).on('submit', '.bulk-update-invoice-form', function(e){
		    	e.preventDefault();
		    	var form = $(this);
		    	var formData = {
		    		payment_method: form.find('select.payment_method').val(),
		    		date: form.find('.datepicker_field').val(),
		    		transaction: form.find('.transaction').val(),
		    		comment: form.find('.comment').val(),
		    	};
		    	var checkboxes = $('.invoice_update_form .invoice_update_checkbox:checked');
		    	if(checkboxes.length){
		    		var data = [];
		    		checkboxes.each(function(){
		    			var area = $(this).closest('.invoice_update_form');
		    			var dataSingle = {
		    				id: area.attr('data-id'),
			    			payment_method: formData.payment_method,
			    			date: formData.date,
			    			transaction: formData.transaction,
			    			comment: formData.comment,
		    			};
		    			data.push(dataSingle);
		    		});
			    	ajax({
			    		url: '{{ route('admin.invoiceBulkUpdate') }}',
			    		type: 'POST',
			    		data: {
                            _token: csrfToken,
			    			invoices: data
			    		},
			    		successCallback: function(resp){
                            Swal.fire(
                                resp.msg.title,
                                resp.msg.text,
                                resp.msg.type
                            ).then(() => window.location.reload());
							
			    		},
			    	});
		    	}
		    	else{
		    		Swal.fire(
					  'Warning',
					  'Please use the checkboxes to select the invoices',
					  'warning'
					)
		    	}
		    });
        });
    </script>

@endpush