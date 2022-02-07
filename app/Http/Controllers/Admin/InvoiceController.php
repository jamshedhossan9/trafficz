<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id' => 'required',
            'payment_method' => 'required',
            'date' => 'required',
            'transaction' => 'required',
        ],[
            'id.required' => 'Invoice not found',
            'transaction.required' => 'Transaction code is required'
        ]);

        $output = $this->ajaxRes(true);

        $id = intval($id);
        $payment_method = $request->input('payment_method');
        $date = $request->input('date');
        $transaction = $request->input('transaction');
        $comment = '';
        if($request->has('comment')){
            $comment = trim($request->input('comment'));
        }
        
        $newInvoice = Invoice::find($id);
        if(!empty($newInvoice)){
            $invoiceOwner = $newInvoice->user()->first();
            if(!empty($invoiceOwner) && $invoiceOwner->parent_id == $this->user->id){   
                $newInvoice->method = $payment_method;
                $newInvoice->paid_on = $date;
                $newInvoice->transaction_code = $transaction;
                $newInvoice->handled = true;
                $newInvoice->comment = $comment;
                $newInvoice->save();
                if($newInvoice){
                    $output->status = true;
                    $output->msg->text = 'Invoice updated';
                    $output->msg->title = 'Successful';
                    $output->msg->type = 'success';
                }
            }
            else{
                $output->msg->text = 'Not authorized';
            }
        }
        else{
            $output->msg->text = 'Invoice not found';
        }
        
        return response()->json($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function bulkUpdate(Request $request)
    {
        $output = $this->ajaxRes(true);

        $invoiceInputs = $request->invoices;
        if(!empty($invoiceInputs)){
            $updatedInvoices = 0;
			$missedInvoices = 0;
			$allFieldsAreRequired = 0;

            foreach ($invoiceInputs as $invoice) {
                $id = !empty($invoice['id']) ? $invoice['id'] : '';
                $id = intval($id);
                $payment_method = !empty($invoice['payment_method']) ? trim($invoice['payment_method']) : '';
                $date = !empty($invoice['date']) ? $invoice['date'] : '';
                $transaction = !empty($invoice['transaction']) ? trim($invoice['transaction']) : '';
                $comment = !empty($invoice['comment']) ? trim($invoice['comment']) : '';
                if($id != 0 && $payment_method != '' && $date != '' && $transaction != '' ){
                    $newInvoice = Invoice::find($id);
                    if(!empty($newInvoice)){
                        $invoiceOwner = $newInvoice->user()->first();
                        if(!empty($invoiceOwner) && $invoiceOwner->parent_id == $this->user->id){       
                            $newInvoice->method = $payment_method;
                            $newInvoice->paid_on = $date;
                            $newInvoice->transaction_code = $transaction;
                            $newInvoice->handled = true;
                            $newInvoice->comment = $comment;
                            $newInvoice->save();
                            if($newInvoice){
                                $updatedInvoices++;
                            }
                        }
                        else{
                            $missedInvoices++;
                        }
                    }
                    else{
                        $missedInvoices++;
                    }
                }
                else{
                    $allFieldsAreRequired++;
                }
            }
        }
        else{
            $output->msg->text = 'Data not found';    
        }

        if($updatedInvoices){
            $output->status = true;
            if(!$missedInvoices){
                $output->msg->text = 'Invoice updated';
                $output->msg->title = 'Successful';
                $output->msg->type = 'success';
            }
            else{
                if($allFieldsAreRequired){
                    $output->msg->text = 'Invoices updated. '.$allFieldsAreRequired.' Invoice(s) have/has missing fields';
                    $output->msg->title = 'Successful';
                    $output->msg->type = 'warning';
                }
                else{
                    $output->msg->text = 'Invoices updated. '.$missedInvoices.' Invoice(s) not found';
                    $output->msg->title = 'Successful';
                    $output->msg->type = 'warning';
                }
            }
        }

        return response()->json($output);
    }

    public function byUser($id)
    {
        $confirmedOwner = false;
        $this->subUser = User::find($id);
        if(!empty($this->subUser)){
            if($this->subUser->parent_id == $this->user->id){
                $confirmedOwner = true;
                $this->invoices = Invoice::whereUserId($id)->orderBy('id', 'desc')->get();
            }
        }
        if(!$confirmedOwner){
            abort(403, 'Unauthorized action');
        }
        
        return view('admin.invoice', $this->data);
        
    }

}
