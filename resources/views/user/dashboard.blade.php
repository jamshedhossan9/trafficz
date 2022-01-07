@extends('layouts.app')

@push('css')
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .dashboard-stats-area{
            gap:25px;
            /*margin-top: 15px;*/
            margin-bottom: 20px;
        }
        .dashboard-stats-area>.col{
        width: 33.33%;
        }
        .dashboard-stat{
            position: relative;
            width: 100%;
            max-width:100%;
            display: block;
            background: #aaa;
            border-radius: 3px;
            padding-top: 35%;
        }
        .dashboard-stat .chart-svg-wrapper{
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        border-radius: 3px;
        overflow: hidden;
        }
        .dashboard-stat .chart-svg-wrapper .chart-svg{
            width: 100%;
            height: auto;
            opacity: .5;
            position: absolute;
            bottom: 0;
        }
        .dashboard-stat-1{
            background: #6685f4;
            background: linear-gradient(45deg, #6685f4, #3f60d6);
        }
        .dashboard-stat-2{
            background: #5090c7;
            background: linear-gradient(45deg, #5090c7, #2571b1);
        }
        .dashboard-stat-3{
            background: #f09b1d;
            background: linear-gradient(45deg, #f09b1d, #ca831a);
        }
        .dashboard-stat-4{
            background: #fc6161;
            background: linear-gradient(45deg, #fc6161, #e33c3c);
        }

        .dashboard-stat .stat{
            position:absolute;
            top:0; left:0; bottom:0; right:0;
            z-index: 2;
            padding: 15px;
            /* color: #fff; */
            color: #f1f4f6;
        }
        .dashboard-stat .stat .count{
            font-size: 190%;
            font-weight: 500;
            text-shadow: 3px 1px 1px rgb(0 0 0 / 20%);
        }
        .dashboard-stat .stat .text{
            font-size: 120%;
            margin-top: 5px;
            text-shadow: 1px 1px 2px rgb(0 0 0 / 70%);
        }
        .campaign-group-panel .panel-heading{
            padding: 10px 25px;
        }
        .campaign-group-panel .panel-heading .filter{
            text-transform: initial;
        }
        .campaign-group-panel .panel-heading .filter>.tag_select{
            min-width: 150px;
            max-width: 400px;
        }
        .campaign-group-panel .panel-heading .filter>.date{
            width: 220px;
        }
        .campaign-hourly-stat-btn{
            cursor: pointer;
            opacity: .5;
        }
        .campaign-hourly-stat-btn:hover{
            opacity: 1;
        }
        .hourly-row-cell{
            background: #fafbfb;
        }

        .campaign-group-panel>.panel-heading{
            background: #fff;
            position: sticky;
            top: 0;
            z-index: 3;
        }
        .campaign-group-panel .hourly-row-cell table thead th{
            background: #f7f9fa;
            position: sticky;
            top: 57px;
            z-index: 2;
        }
    </style>
@endpush

@section('content')
    <div class="flex-box gap-30 dashboard-stats-area flex-nowrap justify-center">
        <div class="col">
            <div class="dashboard-stat dashboard-stat-1">
                <div class="chart-svg-wrapper">
                    <img class="chart-svg" src="{{asset('img/chart-svg/line-chart-white.svg')}}">
                </div>
                <div class="stat">
                    <div class="count total_click_stat">0</div>
                    <div class="text">Total Click</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-stat dashboard-stat-3">
                <div class="chart-svg-wrapper">
                    <img class="chart-svg" src="{{asset('img/chart-svg/area-chart-smooth-white.svg')}}">
                </div>
                <div class="stat">
                    <div class="count total_revenue_stat">0</div>
                    <div class="text">Total Revenue</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-stat dashboard-stat-3">
                <div class="chart-svg-wrapper">
                    <img class="chart-svg" src="{{asset('img/chart-svg/line-chart-smooth-white.svg')}}">
                </div>
                <div class="stat">
                    <div class="count total_epc_stat">0</div>
                    <div class="text">EPC</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-stat dashboard-stat-4">
                <div class="chart-svg-wrapper">
                    <img class="chart-svg" src="{{asset('img/chart-svg/bar-chart-white.svg')}}">
                </div>
                <div class="stat">
                    <div class="count total_pending_amount">0</div>
                    <div class="text">Pending Amount</div>
                </div>
            </div>
        </div>

    </div>

    <div class="flex-box flex-column gap-20">
        @foreach ($campaignGroupUsers as $campaignGroupUser)
            @php
                $group = $campaignGroupUser->campaignGroup;
            @endphp
            <div class="panel panel-default campaign-group-panel" data-group-id="{{ $group->id }}">
                <div class="panel-heading flex-box gap-10 align-center"> 
                    <div>
                        <a href="#" data-perform="panel-collapse"><i class="ti-minus m-0"></i></a>
                    </div>
                    <div class="panel-title flex-grow">
                        {{ $group->name }}
                    </div>
                    <form class="filter flex-box gap-5 align-center campaign-group-filter-form">
                        <div class="flex-none tag_select">
                            <select name="tags[]" class="default-selectpicker" multiple title="Select Tags">
                                @php
                                    $tagUniqueByCampaign = [];
                                @endphp
                                @foreach ($group->campaigns as $campaign)
                                    @foreach ($campaign->tags as $tag)
                                        @if(empty($tagUniqueByCampaign[$tag->id]))
                                            @php
                                                $tagUniqueByCampaign[$tag->id] = true;
                                            @endphp
                                            <option value="{{$tag->id}}">{{$tag->name}}</option>
                                        @endif
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-none date">
                            <input type="text" class="date_range_picker form-control" name="date">
                        </div>
                        <div class="flex-none">
                            <input type="hidden" name="group_id" value="{{$group->id}}">
                            <button class="btn btn-info submit_btn" type="submit">Search</button>
                        </div>
                    </form>
                  
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <table class="table table-bordered stats-table">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Clicks</th>
                                    <th>Revenue</th>
                                    <th>EPC</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
              </div>
        @endforeach
    </div>
@endsection

@push('js')

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    <script>
        var updatedPendingInvoiceAmount = false;
        formSubmission['campaign-group-add-campaign-success'] = function(response, form){
            var data = response.data;
            if(data){
                var group = data.group;
                if(group != null){
                    var campaignsHtml = `
                        <div class="item_title">Campaigns (`+(group.campaigns.length)+`):</div>
                        <ul class="list">`;
                            for(var item of group.campaigns){
                                campaignsHtml += `<li class="item" title="`+item.tracker_auth.tracker_user.tracker.name+` (`+item.tracker_auth.name+`) `+item.camp_id+`">`+item.name+` (`+item.tracker_auth.name+`)</li>`;
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

        $(function(){
            function campaignReportSuccessCallback(resp, el){
                console.log(resp);
                if(typeof el.form !== "undefined"){
                    el.form.find('.submit_btn').find('.loading-btn').remove();
                }
                if(typeof resp !== "undefined" && resp.data != null && resp.data.groupStats != null){
                    var groupStats = resp.data.groupStats;
                    for(var x in groupStats){
                        if(groupStats.hasOwnProperty(x)){
                            var groupDom = $('.campaign-group-panel[data-group-id="'+x+'"]');
                            var tbody = ``;
                            var tfoot = ``;
                            var campaigns = groupStats[x].campaigns;
                            var total = groupStats[x].total;
                            for(var y in campaigns){
                                if(campaigns.hasOwnProperty(y)){
                                    var stats = campaigns[y].stats;
                                    var tags = [];
                                    for(var item of campaigns[y].tags){
                                        tags.push(item.name);
                                    }
                                    tbody += `
                                        <tr class="main-row" data-id="`+campaigns[y].id+`">
                                            <td><span class="ti-plus campaign-hourly-stat-btn m-r-5" data-loaded="false"></span> `+campaigns[y].name+`</td>
                                            <td>`+stats.clicks+`</td>
                                            <td>$`+stats.revenue+`</td>
                                            <td>$`+stats.epc+`</td>
                                            <td>`+(tags.join(', '))+`</td>
                                        </tr>
                                        <tr class="hourly-row hidden" data-id="`+campaigns[y].id+`">
                                            <td colspan="5" class="hourly-row-cell"></td>
                                        </tr>
                                    `;
                                }
                            }
                            if(groupStats[x].credit){
                                tfoot += `
                                    <tr>
                                        <th>Credit</th>
                                        <th>-</th>
                                        <th colspan="3">$`+groupStats[x].credit+`</th>
                                    </tr>
                                `;      
                            }
                            tfoot += `
                                <tr>
                                    <th>Total</th>
                                    <th>`+total.clicks+`</th>
                                    <th>$`+total.revenue+`</th>
                                    <th>$`+total.epc+`</th>
                                    <th></th>
                                </tr>
                            `;  
                            groupDom.find('.stats-table tbody').html(tbody);
                            groupDom.find('.stats-table tfoot').html(tfoot);
                        }
                    }
                    if(el.groupSelection == 'all'){
                        var allTotals = resp.data.totals;
                        if(allTotals != null){
                            $('.dashboard-stats-area .total_click_stat').text(allTotals.clicks);
                            $('.dashboard-stats-area .total_revenue_stat').text('$'+allTotals.revenue);
                            $('.dashboard-stats-area .total_epc_stat').text('$'+allTotals.epc);
                            $('.dashboard-stats-area .total_pending_amount').text('$'+resp.data.pending_amount);
                        }
                    }
                }
            }
            ajax({
                url: listUrls.getAllCampaignGroupStats,
                type: 'POST',
                data: {
                    _token: csrfToken,
                    group: 'all',
                },
                successCallback: campaignReportSuccessCallback
            },{groupSelection: 'all'});

            var serverTime = moment().tz("America/New_York");
            var start = serverTime;
            var end = serverTime;
            var daterangeOptions = {
                startDate: start,
                endDate: end,
                locale: {
                    format: 'YYYY/MM/DD'
                },
                autoApply: true,
                ranges: {
                'Today': [serverTime, serverTime],
                'Yesterday': [serverTime.subtract(1, 'days'), serverTime.subtract(1, 'days')],
                'Last 7 Days': [serverTime.subtract(6, 'days'), serverTime],
                'Last 30 Days': [serverTime.subtract(29, 'days'), serverTime],
                'This Month': [serverTime.startOf('month'), serverTime.endOf('month')],
                'Last Month': [serverTime.subtract(1, 'month').startOf('month'), serverTime.subtract(1, 'month').endOf('month')]
                }
            };
            
            $('.date_range_picker').daterangepicker(daterangeOptions);

            $(document).on('submit', '.campaign-group-filter-form', function(e){
                e.preventDefault();
                var form = $(this);
                var tags = form.find('select[name="tags[]"]').val();
                var date = form.find('input[name="date"]').val();
                var groupId = form.find('input[name="group_id"]').val();
                console.log(tags, date, groupId);
                var dateArray = date.split('-');
                if(dateArray.length == 2){
                    var from = dateArray[0].trim();
                    var to = dateArray[1].trim();
                    if(tags == null) tags = [];
                    form.find('.submit_btn').prepend('<span class="loading-btn fa fa-spin fa-circle-o-notch m-r-5"></span>');
                    ajax({
                        blockUi: false,
                        url: listUrls.getAllCampaignGroupStats,
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            group: groupId,
                            dateFrom: from,
                            dateTo: to,
                            tags: tags
                        },
                        successCallback: campaignReportSuccessCallback
                    }, {groupSelection: 'single', form: form});
                }
            });
            
            function campaignHourlyReportSuccessCallback(resp, el){
                if(typeof resp !== "undefined" && resp.data != null && resp.data.hourly_data){
                    var html = `
                        <table class="table table-bordered m-0">
                            <thead>
                                <tr>
                                    <th>Hour</th>
                                    <th>Clicks</th>
                                    <th>Revenue</th>
                                    <th>EPC</th>
                                </tr>
                            </thead>
                            <tbody>`;
                                for(var item of resp.data.hourly_data){
                                    html += `
                                        <tr>
                                            <td>`+item.name+`</td>
                                            <td>`+item.clicks+`</td>
                                            <td>$`+item.revenue+`</td>
                                            <td>$`+item.epc+`</td>
                                        </tr>
                                    `;
                                }
                                html += `
                            </tbody>
                        </table>
                    `;
                    el.el.addClass('open ti-minus').removeClass('ti-plus').attr('data-loaded', 'true');
                    el.hourlyRow.find('.hourly-row-cell').html(html);
                    el.hourlyRow.removeClass('hidden');
                }
            }

            $(document).on('click', '.campaign-hourly-stat-btn', function(e){
                e.preventDefault();
                var el = $(this);
                var groupDom = el.closest('.campaign-group-panel');
                var form = groupDom.find('.campaign-group-filter-form');
                var date = form.find('input[name="date"]').val();
                var campaignId = el.closest('.main-row').attr('data-id');
                var loaded = el.attr('data-loaded');
                var open = el.hasClass('open');
                var hourlyRow = groupDom.find('.stats-table .hourly-row[data-id="'+campaignId+'"]');
                if(open){
                    hourlyRow.addClass('hidden');
                    el.removeClass('ti-minus open').addClass('ti-plus');
                }
                else{
                    if(loaded == "true"){
                        hourlyRow.removeClass('hidden');    
                        el.removeClass('ti-plus').addClass('ti-minus open');
                    }
                    else{
                        var dateArray = date.split('-');
                        if(dateArray.length == 2){
                            var from = dateArray[0].trim();
                            var to = dateArray[1].trim();
                            ajax({
                                blockUi: true,
                                url: listUrls.getCampaignHourlyStats,
                                type: 'POST',
                                data: {
                                    _token: csrfToken,
                                    campaignId: campaignId,
                                    dateFrom: from,
                                    dateTo: to,
                                },
                                successCallback: campaignHourlyReportSuccessCallback
                            }, {groupDom: groupDom, hourlyRow:hourlyRow, el:el});
                        }
                    }
                    
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