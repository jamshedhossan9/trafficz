@extends('layouts.app')

@section('pageActionbar')
    <a href="#userCreateModal" class="btn btn-info" data-toggle="modal"><span class="icon-plus"></span> Add User</a>
@endsection

@section('content')
    <div class="white-box">
        <div class="clearfix">
            <table class="table table-bordered user-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>
                                <a href="{{ route('admin.invoicesByUser', $item->id) }}" class="btn btn-info">Invoices</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    
    <div id="userCreateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="userCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST" 
                    class="form-material form-horizontal ajax-form m-0"
                    data-success="user-create-success"
                    >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Add User</h4>
                    </div>
                    <div class="modal-body">
                        <div class="clearfix p-10">
                            @csrf
                            <div class="form-group">
                                <label class="col-md-12">Name</label>
                                <div class="col-md-12">
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Email <span class="help"> e.g. "example@gmail.com"</span></label>
                                <div class="col-md-12">
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Password</label>
                                <div class="col-md-12">
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info waves-effect">Submit</button>
                        <button type="button" class="btn btn-inverse waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



@push('js')
    <script>
        formSubmission['user-create-success'] = function(response, form){
            var data = response.data;
            if(data){
                var user = data.user;
                if(user != null){
                    var html = `
                        <tr>
                            <td>`+user.name+`</td>
                            <td>`+user.email+`</td>
                            <td>
                                <a href="`+(listUrls.adminInvoicesByUser(user.id))+`" class="btn btn-info">Invoices</a>
                            </td>
                        </tr>
                    `;
                    $('.user-table tbody').prepend(html);
                }
            }
            if(typeof form !== "undefined") form.get(0).reset();
            $('#userCreateModal').modal('hide');
        };
    </script>
@endpush


