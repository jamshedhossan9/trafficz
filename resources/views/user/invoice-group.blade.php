@extends('layouts.app')

@push('css')
    <style>
        .invoice-row-child:not(.open) {
            display: none;
        }
        .invoice-row-parent[data-first="true"] {
            cursor: pointer;
        }

        .invoice-row td{
            position: relative;
        }
        .invoice-row.open td{
            color: #444;
        }
        .invoice-row td .content-item{
            z-index: 1;
            position: relative;
        }
        .invoice-row.open td:first-child{
            padding-left: 13px;
        }
        .invoice-row.open td:last-child{
            padding-right: 13px;
        }
        .invoice-row.open[data-first="true"] td{
            padding-top: 20px;
        }
        .invoice-row.open[data-last="true"] td{
            padding-bottom: 20px;
        }
        .invoice-row.open td:before{
            content: "";
            position: absolute;
            top:0;
            left:0;
            right:0;
            bottom:0;
            background: #f2f6f8;
            z-index: 0;
        }
        .invoice-row.open td:first-child:before{
            left:5px;
        }
        .invoice-row.open td:last-child:before{
            right:5px;
        }
        .invoice-row.open[data-first="true"] td:before{
            top:5px;
        }
        .invoice-row.open[data-last="true"] td:before,
        .invoice-row.open td.column-end:before
        {
            bottom:5px;
        }
        .invoice-row.open[data-first="true"] td:first-child:before{
            border-top-left-radius: 10px;
        }
        .invoice-row.open[data-first="true"] td:last-child:before{
            border-top-right-radius: 10px;
        }
        .invoice-row.open[data-last="true"] td:first-child:before{
            border-bottom-left-radius: 10px;
        }
        .invoice-row.open[data-last="true"] td:last-child:before{
            border-bottom-right-radius: 10px;
        }
        .invoice-row-parent.open td {
            font-weight: 400;
        }
    </style>
@endpush

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
                                $serial = count($groupInvoices);
                            @endphp
                            @foreach ($groupInvoices as $groupInvoice)
                                @php
                                    $itemFirst = 'false';
                                    if(count($groupInvoice['items']) > 1){
                                        $itemFirst = 'true';
                                    }
                                @endphp
                                <tr class="invoice-row invoice-row-parent" data-serial="{{$serial}}" data-first="{{$itemFirst}}">
                                    <td><div class="content-item">{{$serial}}</div></td>
                                    <td><div class="content-item">{{$groupInvoice['group']['start_date']}}</div></td>
                                    <td><div class="content-item">{{$groupInvoice['group']['end_date']}}</div></td>
                                    
                                    @if($groupInvoice['group']['handled'])
                                        <td><div class="content-item"><strong>{{$groupInvoice['group']['paid_on']}}</strong></div></td>
                                        <td>
                                            <div class="content-item">
                                                @if($itemFirst == 'true')
                                                    <a class="trx-id" href="#">{{$groupInvoice['group']['transaction_code']}}</a>
                                                @else
                                                    {{$groupInvoice['group']['transaction_code']}}
                                                @endif
                                            
                                            </div>
                                        </td>
                                    @else
                                        <td><div class="content-item"><strong>Not Paid</strong></div></td>
                                        <td><div class="content-item"></div></td>
                                        @php
                                            $totalUnpaid += $groupInvoice['group']['total'];
                                        @endphp
                                    @endif

                                    <td><div class="content-item"><b>${{ number_format($groupInvoice['group']['total'], 2) }}</b></div></td>
                                </tr>
                                @if(count($groupInvoice['items']) > 1)
                                    @php
                                        $commentAdded = false;
                                        $comment = $groupInvoice['group']['comment'];
                                    @endphp
                                    @foreach ($groupInvoice['items'] as $key => $item)
                                        @php
                                            $itemLast = 'false';
                                            if($key == count($groupInvoice['items']) - 1){
                                                $itemLast = 'true';
                                            }
                                        @endphp
                                        <tr class="invoice-row invoice-row-child" data-serial="{{$serial}}" data-last="{{$itemLast}}">
                                            <td class="text-center"><div class="content-item">-</div></td>
                                            <td><div class="content-item">{{$item['start_date']}}</div></td>
                                            <td @if(empty($comment)) colspan="3" @endif><div class="content-item">{{$item['end_date']}}</div></td>
                                            
                                            @if(!$commentAdded && !empty($comment))
                                                <td class="column-end" colspan="2" rowspan="{{count($groupInvoice['items'])}}"><div class="content-item"><b>Note: </b>{{$item['comment']}}</div></td>
                                                @php
                                                    $commentAdded = true;
                                                @endphp
                                            @endif

                                            <td><div class="content-item">${{ number_format($item['total'], 2) }}</div></td>
                                        </tr>
                                    @endforeach
                                @endif

                                @php
                                    $serial--;
                                @endphp
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

@push('js')
    <script>
        function toggleInvoiceGroup(serial){
            var table = $('.all_invoice_table');
            table.find('tbody tr.invoice-row[data-serial="'+serial+'"]').toggleClass('open');
        }
        $(document).on('click', ' .invoice-row-parent[data-first="true"] ', function(e){
            e.preventDefault();
            var el = $(this);
            if(el.hasClass('trx-id')){
                el = el.closest('.invoice-row-parent');
            }
            var serial = el.attr('data-serial');
            toggleInvoiceGroup(serial);
        });
    </script>
@endpush