@extends('layouts.app')

@section('content')
    
    <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Change Name</div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <form action="route"></form>
                        </div>
                    </div>
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
    </script>
@endpush
