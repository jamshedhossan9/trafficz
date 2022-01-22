@extends('layouts.app')

@section('pageActionbar')
    <a href="#campaignGroupCreateModal" class="btn btn-info" data-toggle="modal"><span class="icon-plus"></span> Add Campaign group</a>
@endsection

@push('css')
    <link rel="stylesheet" href="{{asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">
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
            color: #c77b11;
        }
        .campaign_group .toolbox .action-btn.edit-campaign-group:hover {
            color: #048dca;
        }
        .campaign_group .toolbox .action-btn.delete-campaign-group:hover {
            color: #ba411d;
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
        .campaign_list .item .tool-icon{
            color: #333;
            opacity: 0;
            visibility: hidden;
            transition: .2s ease-in;
        }
        .campaign_list .item:hover .tool-icon{
            opacity: .5;
            visibility: visible;
        }
        .campaign_list .item .tool-icon:hover{
            opacity: 1;
        }
        .campaign_list .item:hover .tool-icon.delete-campaign-from-group:hover{
            color: #ba411d;
        }
        .campaign_list .item:hover .tool-icon.edit-campaign-from-group:hover{
            color: #048dca;
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
                    <a href="#" class="action-btn add-credit-btn" data-id="{{$group->id}}" title="Add Credit"><span class="fa fa-credit-card-alt"></span></a>
                    <a href="#" class="action-btn add-user-btn" data-id="{{$group->id}}" title="Add Users"><span class="fa fa-user"></span></a>
                </div>
                <div class="campaign_list item_list">
                    <div class="item_title">Campaigns ({{count($group->campaigns)}}):</div>
                    <ul class="list">
                        @foreach ($group->campaigns as $campaign)
                            <li class="item" title="{{$campaign->trackerAuth->trackerUser->tracker->name}} ({{$campaign->trackerAuth->name}}) {{$campaign->camp_id}}">
                                {{$campaign->name}} ({{$campaign->trackerAuth->name}}) 
                                <a href="#{{$campaign->id}}" class="edit-campaign-from-group tool-icon" data-id="{{$campaign->id}}"><span class="ti-pencil"></span></a>
                                <a href="#" class="delete-campaign-from-group tool-icon m-l-5" data-id="{{$campaign->id}}"><span class="ti-trash"></span></a>
                            </li>
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
                <div class="flex-grow-none p-15 b-t">
                    {{-- <div class="flex-box gap-5 align-center toolbox"> --}}
                        {{-- <div class="flex-grow"> --}}
                            <strong>
                                Credit: 
                                $<span class="credit-amount">
                                    @if(!is_null($group->credit))
                                    {{$group->credit->amount}}
                                    @else
                                    0
                                    @endif
                                </span>
                            </strong>
                        {{-- </div> --}}
                        {{-- <div class="flex-none">
                            <a href="#" class="edit-campaign-group action-btn" data-id="{{$group->id}}"><span class="fa fa-edit"></span></a>
                        </div>
                        <div class="flex-none">
                            <a href="#" class="delete-campaign-group action-btn" data-id="{{$group->id}}"><span class="fa fa-trash"></span></a>
                        </div> --}}
                    {{-- </div> --}}
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

    <div id="campaignGroupAddCampaignModal"  data-state="create" class="modal fade switch_state_on_action" role="dialog" aria-labelledby="campaignGroupAddCampaignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.addCampaignToGroup') }}" method="POST" 
                    class="form-material form-horizontal ajax-form m-0 add_campaign_to_group_form"
                    data-success="campaign-group-add-campaign-success"
                    >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">
                            <span class="switch_on_action" data-state="create">Add</span> 
                            <span class="switch_on_action" data-state="edit">Edit</span> 
                             Campaign to Group</h4>
                    </div>
                    <div class="modal-body">
                        <div class="clearfix p-10">
                            @csrf
                            <input type="hidden" name="campaign_group_id" value="">
                            <div class="form-group">
                                <label class="col-md-12">Name</label>
                                <div class="col-md-12">
                                    <input type="text" name="name" class="form-control" required>
                                    <input type="hidden" name="campaign_db_id" value="">
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

    
    <div id="campaignGroupAddCreditModal" class="modal fade" role="dialog" aria-labelledby="campaignGroupAddCreditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add Credit to Group</h4>
                </div>
                <div class="modal-body">
                    <div class="clearfix">
                        <div class="clearfix">
                            <form action="{{ route('admin.addCreditToGroup') }}" method="POST" 
                                class="form-horizontal ajax-form m-0"
                                data-success="campaign-group-add-credit-success"
                                >
                                @csrf
                                <div class="flex-box gap-10">
                                    <div class="flex-grow">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <label for="" class="control-label">Date</label>
                                                <input type="text" class="form-control credit_datepicker"  name="date" required autocomplete="off" placeholder="Choose date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <label for="" class="control-label">Amount</label>
                                                <input type="text" class="form-control"  name="amount" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-none">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <label for="" class="control-label invisible">button</label>
                                                <div class="clearfix">
                                                    <input type="hidden" name="campaign_group_id" value="">
                                                    <button class="btn btn-info" type="submit">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="clearfix credit-list-wrapper" data-loading="true">
                            <table class="table table-bordered credit-list">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div class="loader">
                                <span class="fa fa-spin fa-circle-o-notch icon"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-inverse waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script src="{{asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
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
                                <a href="#" class="action-btn add-credit-btn" data-id="`+group.id+`" title="Add Credit"><span class="fa fa-credit-card-alt"></span></a>
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
                            <div class="flex-grow-none p-15 b-t">
                                <strong>
                                    Credit: 
                                    $<span class="credit-amount">0</span>
                                </strong>
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
                                campaignsHtml += `<li class="item" title="`+item.tracker_auth.tracker_user.tracker.name+` (`+item.tracker_auth.name+`) `+item.camp_id+`">
                                    `+item.name+` (`+item.tracker_auth.name+`) 
                                    <a href="#`+item.id+`" class="edit-campaign-from-group tool-icon" data-id="`+item.id+`"><span class="ti-pencil"></span></a>
                                    <a href="#" class="delete-campaign-from-group tool-icon m-l-5" data-id="`+item.id+`"><span class="ti-trash"></span></a>
                                </li>`;
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
            var modal = $('#campaignGroupAddCampaignModal');
            var form = modal.find('.add_campaign_to_group_form');
            form.get(0).reset();
            modal.find('[name="campaign_group_id"]').val(groupId);
            modal.find('[name="campaign_db_id"]').val('');
            modal.find('[name="tracker_auth_id"]').val('').change();
            modal.find('[name="campaign_tag_id[]"]').val([]).change();
            modal.attr('data-state', 'create');
            modal.modal('show');
        });

        $(document).on('click', '.edit-campaign-from-group', function(e){
            e.preventDefault()
            var el = $(this);
            var id = el.attr('data-id');
            var modal = $('#campaignGroupAddCampaignModal');
            modal.attr('data-state', 'edit');
            loadBtn(el);
            ajax({
                blockUi: false,
                url: listUrls.adminGetCampaignFromGroup(id),
                _success: function(resp, prop){
                    unloadBtn(prop.el);
                    for(var x in resp.data.editData){
                        if(resp.data.editData.hasOwnProperty(x)){
                            prop.modal.find('[name="'+x+'"]').val(resp.data.editData[x]);
                            if(x == 'tracker_auth_id' || x == 'campaign_tag_id[]'){
                                prop.modal.find('[name="'+x+'"]').trigger('change');
                            }
                        }
                    }
                    prop.modal.modal('show');
                },
                _error: function(resp, prop){
                    unloadBtn(prop.el);
                }
            }, {el:el, modal:modal})
        });

        $('.credit_datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
        });

        $(document).on('click', '.add-credit-btn', function(e){
            e.preventDefault();
            var el = $(this);
            var groupId = el.attr('data-id');
            modal = $('#campaignGroupAddCreditModal');
            modal.find('[name="campaign_group_id"]').val(groupId);
            modal.modal('show');
            var creditListCon = modal.find('.credit-list-wrapper');
            var creditList = creditListCon.find('.credit-list');
            showLoader(creditListCon);
            ajax({
                blockUi: false,
                url: listUrls.adminListCreditFromGroup(groupId),
                successCallback: function(resp, el){
                    hideLoader(el.creditListCon);
                    var listArea = el.creditList.find('tbody');
                    listArea.empty();
                    var dataFound = false;
                    if(typeof resp !== "undefined" && resp.data != null && resp.data.credits != null){
                        var html = ``;
                        for(var item of resp.data.credits){
                            dataFound = true;
                            html += `
                                <tr class="credit-row" data-id="`+item.id+`">
                                    <td>`+item.date+`</td>
                                    <td>`+item.amount+`</td>
                                    <td class="text-center">
                                    `;
                                    if(!item.used){
                                        html += `<a class="btn btn-sm btn-danger delete-credit" href="`+(listUrls.adminDeleteCreditFromGroup(item.id))+`">Delete</a>`;
                                    }
                                    html += `
                                    </td>
                                </tr>
                            `;
                        }
                        listArea.html(html);
                    }

                    if(!dataFound){
                        listArea.html('<tr><td colspan="3" class="text-center">No Credits found</td></tr>');
                    }
                },
                errorCallback: function(resp, el){
                    hideLoader(el.creditListCon);
                }
            }, {creditListCon: creditListCon, creditList: creditList});
        });

        $(document).on('click', '.credit-list .delete-credit', function(e){
            e.preventDefault();
            var el = $(this);
            var row = el.closest('.credit-row');
            var url = el.attr('href');
            
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
                        url: url,
                        successCallback: function(resp, row){
                            row.remove();
                            var todayAmount = 0;
                            var groupId = 0;
                            if(resp.data != null && resp.data.group != null){
                                groupId = resp.data.group.id;
                                if(resp.data.group.credit != null){
                                    todayAmount = resp.data.group.credit.amount;
                                }
                            }
                            if(groupId){
                                var groupDom = $('.campaign-groups-con .campaign_group[data-id="'+groupId+'"]')
                                if(groupDom.length){
                                    groupDom.find('.credit-amount').text(todayAmount);
                                }
                            }
                        }
                    }, row);
                }
            });

        });

        formSubmission['campaign-group-add-credit-success'] = function(resp, form){
            var creditList = form.closest('.modal').find('.credit-list tbody');
            var html = ``;
            var todayAmount = 0;
            var groupId = 0;
            form.get(0).reset();
            if(resp.data != null){
                if(resp.data.group != null){
                    groupId = resp.data.group.id;
                    if(resp.data.group.credit != null){
                        todayAmount = resp.data.group.credit.amount;
                    }
                }
                if(resp.data.credit != null){
                    var item = resp.data.credit;
                    html += `
                        <tr class="credit-row" data-id="`+item.id+`">
                            <td>`+item.date+`</td>
                            <td>`+item.amount+`</td>
                            <td class="text-center">
                            `;
                            if(!item.used){
                                html += `<a class="btn btn-sm btn-danger delete-credit" href="`+(listUrls.adminDeleteCreditFromGroup(item.id))+`">Delete</a>`;
                            }
                            html += `
                            </td>
                        </tr>
                    `;
                    creditList.prepend(html);
                }
            }
            if(groupId){
                var groupDom = $('.campaign-groups-con .campaign_group[data-id="'+groupId+'"]');
                console.log(groupDom);
                if(groupDom.length){
                    groupDom.find('.credit-amount').text(todayAmount);
                }
            }
        };
        
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
            });

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