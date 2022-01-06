@extends('layouts.app')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            Invoices
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
                                <th>Paid On</th>
                                <th>Transaction</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalUnpaid = 0;
                                $serial = count($invoices);
                            @endphp
                            @foreach ($invoices as $invoice)
                            <tr data-id="{{$invoice->id}}">
                                <td>{{$serial}}</td>
                                <td>{{$invoice->start_date}}</td>
                                <td>{{$invoice->end_date}}</td>
                                
                                @if($invoice->handled)
                                    <td><strong>{{$invoice->paid_on}}</strong></td>
                                    <td>{{$invoice->transaction_code}}</td>
                                @else
                                    <td><strong>Not Paid</strong></td>
                                    <td></td>
                                    @php
                                        $totalUnpaid += $invoice->total;
                                    @endphp
                                @endif

                                <td>${{ number_format($invoice->total, 2) }}</td>

                                @php
                                    $serial--;
                                @endphp
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <th colspan="5">Total Unpaid:</th>
                            <th class="text-right">${{ number_format($totalUnpaid, 2) }}</th>
                        </tfoot>
                    </table>
                </div>
          </div>
    </div>

@endsection