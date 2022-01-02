@extends('layouts.app')

@section('pageActionbar')
    <a href="#campaignGroupCreateModal" class="btn btn-info" data-toggle="modal"><span class="icon-plus"></span> Add Campaign group</a>
@endsection

@push('css')
    <style>
        .group-grid {
            /* display: grid; */
            /* grid-template-columns: repeat( auto-fit, minmax(350px, 1fr) ); */
            grid-gap: 15px 15px;
            margin-top: 15px;
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
        }
        .group-grid .campaign_group {
            background: #fff;
            /* padding: 15px; */
            /* border-radius: 4px; */
            box-shadow: 1px 1px 3px rgb(0 0 0 / 10%);
            display: flex;
            flex-direction: column;
            color: #60617d;
            gap: 10px;
            width: 350px;
            flex-grow: 1;
            max-width: 100%;
        }
        .campaign_group .title {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            align-items: center;
            gap: 5px;
        }
        .campaign_group .title .group_name {
            font-size: 16px;
            font-weight: 500;
            flex-grow: 1;
        }
        .campaign_group .toolbox .action-btn {
            padding-left: 5px;
            color: #777;
            opacity: .7;
        }
        .campaign_group .toolbox .action-btn:hover {
            color: #F49917;
        }
        .campaign_group .item_list {
            padding: 0px 15px 10px 15px;
            flex-grow: 1;
        }
        .campaign_group .item_list .item_title {
            letter-spacing: .3px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .campaign_group .item_list .list {
            padding: 0;
            list-style: none;
            margin-bottom: 0;
        }
        .campaign_group .item_list .list .item {
            position: relative;
            display: block;
            padding-left: 25px;
            padding-bottom: 5px;
        }
        .campaign_group .item_list .list .item:before {
            content: "";
            position: absolute;
            width: 5px;
            left: 10px;
            top: 9px;
            border-top: 2px solid #aaa;
        }
        .delete-campaign-from-group{
            opacity: .3;
            cursor: pointer;
            transition: .1s ease-in;
        }
        .delete-campaign-from-group:hover{
            opacity: 1;
            color: #ba411d;
        }
    </style>
@endpush

@section('content')
    <div class="group-grid campaign-groups-con">
        @foreach ($campaignGroups as $group)
            <div class="campaign_group" data-id="{{$group->id}}">
                <div class="title toolbox">
                    <span class="group_name">{{$group->name}}</span>
                    <a href="#" class="action-btn add-campaign-btn" data-id="{{$group->id}}" title="Add Campaign"><span class="fa fa-plus"></span></a>
                    <a href="#" class="action-btn add-user-btn" data-id="{{$group->id}}" title="Add Users"><span class="fa fa-user"></span></a>
                </div>
                <div class="campaign_list item_list">
                    <div class="item_title">Campaigns ({{count($group->campaigns)}}):</div>
                    <ul class="list">
                        @foreach ($group->campaigns as $campaign)
                            <li class="item" title="{{$campaign->trackerAuth->trackerUser->tracker->name}} ({{$campaign->trackerAuth->name}}) {{$campaign->camp_id}}">{{$campaign->name}} ({{$campaign->trackerAuth->name}}) <span class="delete-campaign-from-group tool-icon ti-trash" data-id="{{$campaign->id}}"></span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="user_list item_list">
                    <div class="item_title">Users ({{count($group->users)}}):</div>
                    <ul class="list">
                        @foreach ($group->users as $item)
                            <li class="item" title="{{$item->email}}">{{$item->name}}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    <div id="campaignGroupCreateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="campaignGroupCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.campaigns.store') }}" method="POST" 
                    class="form-material form-horizontal ajax-form m-0"
                    data-success="campaign-group-create-success"
                    >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Add Campaign Group</h4>
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

    <div id="campaignGroupAddCampaignModal" class="modal fade" role="dialog" aria-labelledby="campaignGroupAddCampaignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.addCampaignToGroup') }}" method="POST" 
                    class="form-material form-horizontal ajax-form m-0"
                    data-success="campaign-group-add-campaign-success"
                    >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Add Campaign to Group</h4>
                    </div>
                    <div class="modal-body">
                        <div class="clearfix p-10">
                            @csrf
                            <input type="hidden" name="campaign_group_id" value="">
                            <div class="form-group">
                                <label class="col-md-12">Name</label>
                                <div class="col-md-12">
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Campaign ID</label>
                                <div class="col-md-12">
                                    <input type="text" name="campaign_id" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Tracker Auth</label>
                                <div class="col-md-12">
                                    <select name="tracker_auth_id" id="" class="default-select2" data-live-search="true" data-placeholder="Select">
                                        <option></option>
                                        @foreach ($trackerAuths as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->trackerUser->tracker->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Tags</label>
                                <div class="col-md-12">
                                    <select name="campaign_tag_id[]" id="" class="default-select2-tag" data-live-search="true" data-placeholder="Write or Select" multiple>
                                        @foreach ($campaignTags as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
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

    <div id="campaignGroupAdduserModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="campaignGroupAdduserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.addUserToGroup') }}" method="POST" 
                    class="form-material form-horizontal ajax-form m-0"
                    data-success="campaign-group-add-campaign-success"
                    >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Add Campaign to Group</h4>
                    </div>
                    <div class="modal-body">
                        <div class="clearfix p-10">
                            @csrf
                            <input type="hidden" name="campaign_group_id" value="">
                            <div class="flex-box gap-15 flex-column user_list">

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
        formSubmission['campaign-group-create-success'] = function(response, form){
            var data = response.data;
            if(data){
                var group = data.group;
                if(group != null){
                    var html = `
                        <div class="campaign_group" data-id="`+group.id+`">
                            <div class="title toolbox">
                                <span class="group_name">`+group.name+`</span>
                                <a href="#" class="action-btn add-campaign-btn" data-id="`+group.id+`" title="Add Campaign"><span class="fa fa-plus"></span></a>
                                <a href="#" class="action-btn add-user-btn" data-id="`+group.id+`" title="Add Users"><span class="fa fa-user"></span></a>
                            </div>
                            <div class="campaign_list item_list">
                                <div class="item_title">Campaigns (0):</div>
                                <ul class="list">
                                </ul>
                            </div>
                            <div class="user_list item_list">
                                <div class="item_title">Users (0):</div>
                                <ul class="list">
                                </ul>
                            </div>
                        </div>
                    `;
                    $('.campaign-groups-con').prepend(html);
                }
            }
            if(typeof form !== "undefined") form.get(0).reset();
            $('#campaignGroupCreateModal').modal('hide');
        };
        
        formSubmission['campaign-group-add-campaign-success'] = function(response, form){
            var data = response.data;
            if(data){
                var group = data.group;
                if(group != null){
                    var campaignsHtml = `
                        <div class="item_title">Campaigns (`+(group.campaigns.length)+`):</div>
                        <ul class="list">`;
                            for(var item of group.campaigns){
                                campaignsHtml += `<li class="item" title="`+item.tracker_auth.tracker_user.tracker.name+` (`+item.tracker_auth.name+`) `+item.camp_id+`">`+item.name+` (`+item.tracker_auth.name+`) <span class="delete-campaign-from-group tool-icon ti-trash" data-id="`+item.id+`"</li>`;
                            }
                            campaignsHtml += `
                        </ul>`;
                    var usersHtml = `
                        <div class="item_title">Users (`+(group.users.length)+`):</div>
                        <ul class="list">`;
                            for(var item of group.users){
                                usersHtml += `<li class="item" title="`+item.email+`">`+item.name+`</li>`;
                            }
                            usersHtml += `
                        </ul>`;

                    
                    $('.campaign_group[data-id="'+group.id+'"] .campaign_list').html(campaignsHtml);
                    $('.campaign_group[data-id="'+group.id+'"] .user_list').html(usersHtml);
                }
            }
            if(typeof form !== "undefined"){
                form.get(0).reset();
                select2(form.find('.default-select2'));
                select2Tag(form.find('.default-select2-tag'));
            }
            $('#campaignGroupAddCampaignModal').modal('hide');
            $('#campaignGroupAdduserModal').modal('hide');
        };

        $(document).on('click', '.add-campaign-btn', function(e){
            e.preventDefault();
            var el = $(this);
            var groupId = el.attr('data-id');
            $('#campaignGroupAddCampaignModal').find('[name="campaign_group_id"]').val(groupId);
            $('#campaignGroupAddCampaignModal').modal('show');
        });
        
        $(document).on('click', '.add-user-btn', function(e){
            e.preventDefault();
            var el = $(this);
            var groupId = el.attr('data-id');
            var modal = $('#campaignGroupAdduserModal');
            ajax({
                url : listUrls.adminAddedUsersToGroup(groupId),
                successCallback: function(response){
                    var userList = modal.find('.user_list');
                    userList.empty();
                    if(typeof response !== "undefined"){
                        var data = response.data;
                        if(data != null){
                            var users = data.users;
                            if(users != null){
                                for(var item of users){
                                    var checkBoxId = `user-add-to-campaign-group-`+item.id;
                                    var html = `
                                        <div class="flex-box gap-10 align-center">
                                            <div class="flex-none">
                                                <div class="checkbox checkbox-success">
                                                    <input id="`+checkBoxId+`" type="checkbox" name="users[]" value="`+item.id+`">
                                                    <label for="`+checkBoxId+`">`+item.name+` (`+item.email+`)</label>
                                                </div>    
                                            </div>
                                            <div class="flex-grow">
                                                
                                            </div>
                                        </div>
                                    `;
                                    userList.append($(html));
                                    if(item.using_group){
                                        $('#'+checkBoxId).prop('checked', true);
                                    }
                                }
                            }
                        }
                        modal.modal('show');
                    }
                }
            }, el)
            modal.find('[name="campaign_group_id"]').val(groupId);
            
        });

        $(document).on('click', '.delete-campaign-from-group', function(e){
            e.preventDefault();
            var el = $(this);
            var row = el.closest('li');
            var campaignId = el.attr('data-id');
            
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
                        ajax({
                            url: listUrls.adminDeleteCampaignFromGroup(campaignId),
                            successCallback: function(resp, el){
                                el.remove();
                            }
                        }, row);
                    }
                })

        });
    </script>
@endpush

{{-- <div class="lander_group" data-status="play">
    <div class="title toolbox">
        <span class="group_name">Group 2</span>
        <a href="#" class="action-btn add-campaign-btn" data-id="" title="Add Campaign"><span class="fa fa-plus"></span></a>
        <a href="#" class="action-btn add-user-btn" data-id="" title="Add Users"><span class="fa fa-user"></span></a>
    </div>
    <div class="item_list">
        <div class="item_title">Campaigns (0):</div>
        <ul class="list">
        </ul>
    </div>
    <div class="item_list">
        <div class="item_title">Users (0):</div>
        <ul class="list">
        </ul>
    </div>
</div> --}}