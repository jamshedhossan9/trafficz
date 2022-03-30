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
            /* padding-top: 35%; */
            height: 100%;
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
            position: relative;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            z-index: 2;
            padding: 15px;
            /* color: #fff; */
            color: #f1f4f6;
            display: flex;
            flex-direction: column;
            gap: 15px;
            justify-content: space-between;
            height: 100%;
        }
        .dashboard-stat .stat .count{
            font-size: 190%;
            font-weight: 500;
            text-shadow: 3px 1px 1px rgb(0 0 0 / 20%);
        }
        .dashboard-stat .stat .text{
            font-size: 120%;
            /* margin-top: 5px; */
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
        .all-campaign-hourly-stat-btn:hover,
        .all-campaign-hourly-stat-btn:active,
        .all-campaign-hourly-stat-btn:focus{
            opacity: 1 !important;
            padding-left: 0;
            padding-right: 31px;
            margin-left: -7px;
        }
    </style>
@endpush

@section('page-menu-right')
    <li class="menu-right">
        <form class="filter flex-box align-center campaign-group-filter-form for-all-group page-menu-search">
            <div class="flex-grow date relative">
                <input type="text" class="date_range_picker form-control" name="date">
            </div>
            <div class="flex-none">
                <button class="btn btn-info submit_btn" type="submit">Search</button>
            </div>
            <div class="flex-none">
                <button class="btn btn-info btn-outline all-campaign-hourly-stat-btn" type="button">By Hour</button>
            </div>
        </form>
    </li>
@endsection

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
            <div class="dashboard-stat dashboard-stat-2">
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
                    <div class="count total_pending_amount" data-amount="{{$pendingInvoiceAmount['amount']}}">${{ number_format($pendingInvoiceAmount['amount'], 2) }}</div>
                    <div class="text">Pending Amount</div>
                </div>
            </div>
        </div>

    </div>

    <div class="flex-box flex-column gap-20">
        <div class="panel panel-default all-campaign-hourly-stats-panel m-0 hidden">
            <div class="panel-heading flex-box gap-10 align-center"> 
                <div>
                    <a href="#" data-perform="panel-collapse"><i class="ti-minus m-0"></i></a>
                </div>
                <div class="panel-title flex-grow">
                    Overall Hourly Stats
                </div>
                <div class="flex-none">
                    <a href="#" class="panel-close"><i class="ti-close"></i></a>
                </div>
            </div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <table class="table table-bordered all-campaign-hourly-stats-table">
                        <thead>
                            <tr>
                                <th>Hour</th>
                                <th>Clicks</th>
                                <th>Revenue</th>
                                <th>EPC</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @foreach ($campaignGroupUsers as $campaignGroupUser)
            @php
                $group = $campaignGroupUser->campaignGroup;
            @endphp
            <div class="panel panel-default campaign-group-panel m-0" data-group-id="{{ $group->id }}">
                <div class="panel-heading flex-box gap-10 align-center"> 
                    <div>
                        <a href="#" data-perform="panel-collapse"><i class="ti-minus m-0"></i></a>
                    </div>
                    <div class="panel-title flex-grow">
                        {{ $group->name }}
                    </div>

                    @php
                        $tagUniqueByCampaign = [];
                    @endphp
                    @foreach ($group->campaigns as $campaign)
                        @foreach ($campaign->tags as $tag)
                            @if(empty($tagUniqueByCampaign[$tag->id]))
                                @php
                                    $tagUniqueByCampaign[$tag->id] = $tag;
                                @endphp
                            @endif
                        @endforeach
                    @endforeach

                    <form class="filter flex-box align-center campaign-group-filter-form for-one-group @if(empty($tagUniqueByCampaign)) hidden  @endif">
                        <div class="flex-none tag_select">
                            <select name="tags[]" class="default-selectpicker" multiple title="Select Tags">            
                                @foreach ($tagUniqueByCampaign as $tag)
                                    <option value="{{$tag->id}}">{{$tag->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-none date hidden">
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
                    if(el.form.hasClass('for-all-group')){
                        var dates = el.form.find('[name="date"]').val();
                        var singleForms = $('.campaign-group-filter-form.for-one-group');
                        singleForms.find('[name="date"]').val(dates);
                        singleForms.find('select[name="tags[]"]').val([]).change();
                    }
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
                                    var pull = campaigns[y].pull;
                                    for(var item of campaigns[y].tags){
                                        tags.push(item.name);
                                    }
                                    var revenue = _parseFloat(stats.revenue);
                                    var showCampaign = true;
                                    if(!pull){
                                        showCampaign = false;
                                    }
                                    if(revenue > 0){
                                        showCampaign = true;
                                    }
                                    tbody += `
                                        <tr class="main-row `+(showCampaign ? '' : 'hidden')+`" data-id="`+campaigns[y].id+`">
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
                                    <tr class="credit-row">
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
                            @if (!$isAdmin)
                            if(_parseInt(total.clicks) == 0){
                                groupDom.addClass('hidden');
                            }
                            else{
                                groupDom.removeClass('hidden');
                            }
                            @endif
                        }
                    }
                    if(el.groupSelection == 'all'){
                        var allTotals = resp.data.totals;
                        if(allTotals != null){
                            $('.dashboard-stats-area .total_click_stat').text(allTotals.clicks);
                            $('.dashboard-stats-area .total_revenue_stat').text('$'+allTotals.revenue);
                            $('.dashboard-stats-area .total_epc_stat').text('$'+allTotals.epc);
                            // $('.dashboard-stats-area .total_pending_amount').text('$'+resp.data.pending_amount);
                        }
                        if(resp.data.today_amount != null){
                            var oldAmount = $('.dashboard-stats-area .total_pending_amount').attr('data-amount');
                            oldAmount = _parseFloat(oldAmount);
                            var todayAmount = resp.data.today_amount;
                            todayAmount = _parseFloat(todayAmount);
                            $('.dashboard-stats-area .total_pending_amount').text(globalNumberFormatter.USD.format(oldAmount + todayAmount));
                        }
                    }
                    hideGroupsWithoutCampaign();
                }
            }
            ajax({
                @if (isAdmin())
                    url: listUrls.adminGetAllCampaignGroupStats({{$user->id}}),
                @else
                    url: listUrls.getAllCampaignGroupStats,
                @endif
                type: 'POST',
                data: {
                    _token: csrfToken,
                    group: 'all',
                },
                successCallback: campaignReportSuccessCallback
            },{groupSelection: 'all'});

            var start = moment();
            var end = moment();
            var daterangeOptions = {
                startDate: start,
                endDate: end,
                locale: {
                    format: 'YYYY/MM/DD'
                },
                autoApply: true,
                ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            };
            
            $('.date_range_picker').daterangepicker(daterangeOptions);

            $('.campaign-group-filter-form.for-all-group .date_range_picker').on('change', function(){
                var el = $(this);
                var singleForms = $('.campaign-group-filter-form.for-one-group');
                    singleForms.find('[name="date"]').val(el.val());
            });

            $(document).on('submit', '.campaign-group-filter-form', function(e){
                e.preventDefault();
                var form = $(this);
                var tags = form.find('select[name="tags[]"]').val();
                var date = form.find('input[name="date"]').val();
                var groupDom = form.find('input[name="group_id"]');
                var groupId = groupDom.length ? form.find('input[name="group_id"]').val() : 'all';
                console.log(tags, date, groupId);
                var dateArray = date.split('-');
                if(dateArray.length == 2){
                    var from = dateArray[0].trim();
                    var to = dateArray[1].trim();
                    if(tags == null) tags = [];
                    form.find('.submit_btn').prepend('<span class="loading-btn fa fa-spin fa-circle-o-notch m-r-5"></span>');
                    ajax({
                        blockUi: false,
                        @if (isAdmin())
                            url: listUrls.adminGetAllCampaignGroupStats({{$user->id}}),     
                        @else
                            url: listUrls.getAllCampaignGroupStats,
                        @endif
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            group: groupId,
                            dateFrom: from,
                            dateTo: to,
                            tags: tags
                        },
                        successCallback: campaignReportSuccessCallback
                    }, {groupSelection: groupId, form: form});
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
                                            <td class="font-normal">`+item.name+`</td>
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
                                @if (isAdmin())
                                    url: listUrls.adminGetCampaignHourlyStats({{$user->id}}),     
                                @else
                                    url: listUrls.getCampaignHourlyStats,
                                @endif
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

            $(document).on('click', '.all-campaign-hourly-stat-btn', function(e){
                e.preventDefault();
                var el = $(this);
                var form = $('.campaign-group-filter-form.for-all-group');
                var date = form.find('input[name="date"]').val();
                loadBtn(el);
                var dateArray = date.split('-');
                if(dateArray.length == 2){
                    var from = dateArray[0].trim();
                    var to = dateArray[1].trim();
                    ajax({
                        blockUi: false,
                        @if (isAdmin())
                            url: listUrls.adminGetAllCampaignHourlyStats({{$user->id}}),     
                        @else
                            url: listUrls.getAllCampaignHourlyStats,
                        @endif
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            dateFrom: from,
                            dateTo: to,
                        },
                        _success: function(resp, prop){
                            unloadBtn(prop.el);
                            if(resp.data != null && resp.data.hourly_data != null){
                                var hourlyData = resp.data.hourly_data;
                                var panel = $('.all-campaign-hourly-stats-panel');
                                var table = $('.all-campaign-hourly-stats-table');

                                var html = ``;
                                for(var x in hourlyData){
                                    if(hourlyData.hasOwnProperty(x)){
                                        var hour = _parseInt(x);
                                        var name = hour+':00 - '+(hour+1)+':00';
                                        html += `
                                            <tr>
                                                <td class="font-normal">`+name+`</td>
                                                <td>`+hourlyData[x].clicks+`</td>
                                                <td>$`+hourlyData[x].revenue+`</td>
                                                <td>$`+hourlyData[x].epc+`</td>
                                            </tr>
                                        `;
                                    }
                                }
                                table.find('tbody').html(html);
                                panel.removeClass('hidden');
                                panel.find('>.panel-heading [data-perform="panel-collapse"] i').removeClass('ti-plus').addClass('ti-minus');
                                panel.find('>.panel-wrapper').addClass('in').slideDown();
                            }
                        },
                        _error: function(resp, prop){
                            unloadBtn(prop.el);
                        },
                    }, {el:el});
                }
                else{
                    unloadBtn(el);
                }
                
                    
                
                
                
            });

            $(document).on('click', '.panel>.panel-heading .panel-close', function(e){
                e.preventDefault();
                var el = $(this);
                el.closest('.panel').addClass('hidden');
            });
        });

        function hideGroupsWithoutCampaign(){
            var panels = $('.campaign-group-panel');
            panels.each(function(){
                var panel = $(this);
                var visibleCampaign = panel.find('.stats-table>tbody>.main-row:not(.hidden)');
                var creditRow = panel.find('.stats-table>tfoot>.credit-row');
                if(visibleCampaign.length || creditRow.length){
                    panel.removeClass('hidden');
                }
                else{
                    panel.addClass('hidden');
                }
            });
        }
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