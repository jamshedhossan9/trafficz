@extends('layouts.app')

@section('pageActionbar')
    <div class="flex-none">
        <a class="btn btn-warning import-yesterday-stats" href="{{ route('superAdmin.importYesterdayCampaignData') }}" >Import Yesterday Stats</a>
    </div>
    <div class="flex-none">
        <a href="#userCreateModal" class="btn btn-info" data-toggle="modal"><span class="icon-plus"></span> Add User</a>
    </div>
@endsection

@section('content')
    <div class="white-box">
        <div class="clearfix">
            <table class="table table-bordered user-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Trackers</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>
                                @php
                                    $userTrackers = [];
                                    foreach($item->trackers as $tr){
                                        $userTrackers[] = $tr->name;
                                    }
                                @endphp
                                {{ implode(', ', $userTrackers) }}
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
                <form action="{{ route('superAdmin.users.store') }}" method="POST" 
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
                            <div class="form-group">
                                <label class="col-md-12">Trackers</label>
                                <div class="col-md-12">
                                    <select name="trackers[]" id="" class="form-control default-select2" data-placeholder="Select" required multiple>
                                        @if(!$trackers->isEmpty())
                                            @foreach ($trackers as $tracker)
                                                <option value="{{ $tracker->id }}">{{ $tracker->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
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
                            <td>`;
                                if(user.trackers != null && user.trackers.length){
                                    var trackers = [];
                                    for(var item of user.trackers){
                                        trackers.push(item.name);
                                    }
                                    html += trackers.join(', ');
                                }
                                html += `</td>
                        </tr>
                    `;
                    $('.user-table tbody').prepend(html);
                }
            }
            if(typeof form !== "undefined"){
                form.get(0).reset();
                select2(form.find('.default-select2'));
            }
            $('#userCreateModal').modal('hide');
        };

        $('.import-yesterday-stats').click(function(e){
            e.preventDefault();
            var el = $(this);
            var url = el.attr('href');
            Swal.fire({
                title: 'Are you sure?',
                text: "This will clear campaign stats for yesterday and will import fresh. Then it will re-adjust yesterday's invoice if there is any!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Import!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    ajax({url: url});
                }
            });

        });
    </script>
@endpush