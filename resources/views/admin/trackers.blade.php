@extends('layouts.app')

@section('pageActionbar')
    @foreach ($user->trackerUsers as $trackerUser)
        <a href="#trackerCredModal-{{$trackerUser->tracker->slug}}" class="btn btn-info" data-toggle="modal"><span class="icon-plus"></span> Add {{$trackerUser->tracker->name}} Auth</a>
    @endforeach
@endsection

@section('content')

    <div class="white-box">
        <div class="clearfix">
            <table class="table table-bordered tracker-auth-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Tracker</th>
                        <th>Auth</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trackerAuths as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->trackerUser->tracker->name }}</td>
                            <td>
                                <pre class="json-viewer" data-init="false">
                                    {!! json_encode($item->auth) !!}
                                </pre>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($user->trackerUsers as $trackerUser)
        @php
            $tracker = $trackerUser->tracker;
        @endphp
        <div id="trackerCredModal-{{$tracker->slug}}" class="modal fade trackerCredModal" tabindex="-1" role="dialog" aria-labelledby="trackerCredModal-{{$tracker->slug}}Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.trackers.store') }}" method="POST" 
                        class="form-material form-horizontal m-0 ajax-form"
                        data-success="tracker-auth-create-success"
                        >
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">Add {{$tracker->name}} Auth</h4>
                        </div>
                        <div class="modal-body">
                            <div class="clearfix p-10">
                                @csrf
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
                            <button type="submit" class="btn btn-info waves-effect">Submit</button>
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
                    var html = `
                        <tr>
                            <td>`+tracker.name+`</td>    
                            <td>`+tracker.tracker_user.tracker.name+`</td>    
                            <td><pre class="json-viewer" data-init="false">`+(JSON.stringify(tracker.auth))+`</pre></td>    
                        </tr>
                    `;
                    $('.tracker-auth-table tbody').prepend(html);
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
        });
    </script>
@endpush
