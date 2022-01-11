@extends('layouts.app')

@section('pageActionbar')
    @foreach ($user->trackerUsers as $trackerUser)
        <a href="#" class="btn btn-info trackerCredModalOpenner" data-slug="{{$trackerUser->tracker->slug}}"><span class="icon-plus"></span> Add {{$trackerUser->tracker->name}} Auth</a>
    @endforeach
@endsection

@push('css')
    <style>
        .json-viewer,.json-viewer *{
            word-break: break-all !important;
            word-wrap: break-word !important;
        }
    </style>
@endpush

@section('content')

    <div class="white-box">
        <div class="clearfix">
            <div class="table-responsive">
                <table class="table table-bordered tracker-auth-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Tracker</th>
                            <th>Auth</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trackerAuths as $item)
                            <tr data-id="{{ $item->id }}">
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->trackerUser->tracker->name }}</td>
                                <td>
                                    <div class="auth_wrapper">
                                        <pre class="json-viewer" data-init="false">
                                            @json($item->auth)
                                        </pre>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex-box gap-5 align-center">
                                        <a href="#" class="btn btn-info btn-sm tracker-edit-btn" data-id="{{ $item->id }}" data-tracker="{{ $item->trackerUser->tracker->slug }}">Edit</a>
                                        <a href="#" class="btn btn-danger btn-sm tracker-delete-btn" data-id="{{ $item->id }}" data-tracker="{{ $item->trackerUser->tracker->slug }}">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($user->trackerUsers as $trackerUser)
        @php
            $tracker = $trackerUser->tracker;
        @endphp
        <div id="trackerCredModal-{{$tracker->slug}}" data-state="create" class="modal fade trackerCredModal switch_state_on_action" tabindex="-1" role="dialog" aria-labelledby="trackerCredModal-{{$tracker->slug}}Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.trackers.store') }}" method="POST" 
                        class="form-material form-horizontal m-0 ajax-form tracker_cred_form"
                        data-success="tracker-auth-create-success"
                        >
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">
                                <span class="switch_on_action" data-state="create">Add</span> 
                                <span class="switch_on_action" data-state="edit">Edit</span> 
                                {{$tracker->name}} Auth</h4>
                        </div>
                        <div class="modal-body">
                            <div class="clearfix p-10">
                                @csrf
                                <input name="tracker_id" value="" type="hidden">
                                <input name="tracker_user_id" value="{{$trackerUser->id}}" type="hidden">
                                <input name="tracker_slug" value="{{$tracker->slug}}" type="hidden">
                                    @if ($tracker->slug == 'voluum')
                                        <div class="clearfix tracker_cred" data-tracker="{{$tracker->slug}}">
                                            <div class="form-group">
                                                <label for="" class="col-xs-12">Name</label>
                                                <div class="col-xs-12">
                                                    <input type="text" name="name" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="col-xs-12">Access key ID</label>
                                                        <div class="col-xs-12">
                                                            <input type="text" name="access_key_id" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="col-xs-12">Access key</label>
                                                        <div class="col-xs-12">
                                                            <input type="text" name="access_key" class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif ($tracker->slug == 'binom')
                                        <div class="clearfix tracker_cred" data-tracker="{{$tracker->slug}}">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="col-xs-12">Name</label>
                                                        <div class="col-xs-12">
                                                            <input type="text" name="name" class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="col-xs-12">API key</label>
                                                        <div class="col-xs-12">
                                                            <input type="text" name="api_key" class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="col-xs-12">API Endpoint</label>
                                                        <div class="col-xs-12">
                                                            <input type="text" name="api_endpoint" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="col-xs-12">Web Portal URL</label>
                                                        <div class="col-xs-12">
                                                            <input type="text" name="web_portal_url" class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-info waves-effect">
                                <span class="switch_on_action" data-state="create">Submit</span> 
                                <span class="switch_on_action" data-state="edit">Update</span> 
                            </button>
                            <button type="button" class="btn btn-inverse waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection



@push('js')
    <script>
        formSubmission['tracker-auth-create-success'] = function(response, form){
            var data = response.data;
            if(data){
                var tracker = data.tracker;
                if(tracker != null){
                    var cells = `
                        <td>`+tracker.name+`</td>    
                        <td>`+tracker.tracker_user.tracker.name+`</td>    
                        <td><pre class="json-viewer" data-init="false">`+(JSON.stringify(tracker.auth))+`</pre></td>    
                        <td>
                            <div class="flex-box gap-5 align-center">
                                <a href="#" class="btn btn-info btn-sm tracker-edit-btn" data-id="`+tracker.id+`" data-tracker="`+tracker.tracker_user.tracker.slug+`">Edit</a>
                                <a href="#" class="btn btn-danger btn-sm tracker-delete-btn" data-id="`+tracker.id+`" data-tracker="`+tracker.tracker_user.tracker.slug+`">Delete</a>
                            </div>
                        </td>
                    `;
                    if(data.state == 'edit'){
                        $('.tracker-auth-table tbody>tr[data-id="'+tracker.id+'"]').html(cells);
                    }
                    else{
                        var html = `
                            <tr data-id="`+tracker.id+`">
                                `+cells+`
                            </tr>
                        `;
                        $('.tracker-auth-table tbody').prepend(html);
                    }
                    
                    setJsonViewer();
                }
            }
            if(typeof form !== "undefined"){
                form.get(0).reset();
            }
            $('.trackerCredModal').modal('hide');
        };
        $(function(){
            setJsonViewer();

            $('.trackerCredModalOpenner').click(function(e){
                e.preventDefault()
                var el = $(this);
                var slug = el.attr('data-slug');
                var modal = $('#trackerCredModal-'+slug);
                var form = modal.find('.tracker_cred_form');
                form.get(0).reset();
                modal.attr('data-state', 'create');
                modal.find('[name="tracker_id"]').val('');
                modal.modal('show');
            });

            $(document).on('click', '.tracker-edit-btn', function(e){
                e.preventDefault()
                var el = $(this);
                var slug = el.attr('data-tracker');
                var id = el.attr('data-id');
                var modal = $('#trackerCredModal-'+slug);
                modal.attr('data-state', 'edit');
                loadBtn(el);
                ajax({
                    blockUi: false,
                    url: listUrls.adminGetTracker(id),
                    _success: function(resp, prop){
                        unloadBtn(prop.el);
                        for(var x in resp.data.editData){
                            if(resp.data.editData.hasOwnProperty(x)){
                                prop.modal.find('[name="'+x+'"]').val(resp.data.editData[x]);
                            }
                        }
                        prop.modal.modal('show');
                    },
                    _error: function(resp, prop){
                        unloadBtn(prop.el);
                    }
                }, {el:el, modal:modal})
            });

            $(document).on('click', '.tracker-delete-btn', function(e){
                e.preventDefault();
                var el = $(this);
                var row = el.closest('tr');
                var id = el.attr('data-id');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        loadBtn(el);
                        ajax({
                            blockUi: false,
                            url: listUrls.adminDeleteTracker(id),
                            type: 'POST',
                            data:{
                                _method: "DELETE",
                                _token: csrfToken,
                            },
                            _success: function(resp, prop){
                                prop.row.remove();
                            },
                            _error: function(resp, prop){
                                unloadBtn(prop.el);
                            }
                        }, {el:el, row:row});
                    }
                });

            });
        });
    </script>
@endpush
