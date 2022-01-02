<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
        <ul class="nav" id="side-menu">
            @if (isSuperAdmin())
                <li>
                    <a href="{{ route('superAdmin.users.index') }}" class="waves-effect {{ menuActive('superAdmin.users.index') }}">
                        <i class="linea-icon linea-basic fa-fw" data-icon="v"></i> 
                        <span class="hide-menu"> Dashboard <span class="fa arrow"></span> </span>
                    </a>
                </li>    
            @elseif (isAdmin())
                <li>
                    <a href="{{ route('admin.users.index') }}" class="waves-effect {{ menuActive('admin.users.index') }}">
                        <i class="icon-people"></i> 
                        <span class="hide-menu"> Users </span>
                    </a>
                </li>    
                <li>
                    <a href="{{ route('admin.trackers.index') }}" class="waves-effect {{ menuActive('admin.trackers.index') }}">
                        <i class="icon-globe"></i> 
                        <span class="hide-menu"> Trackers </span>
                    </a>
                </li>    
                </li>    
                <li>
                    <a href="{{ route('admin.campaigns.index') }}" class="waves-effect {{ menuActive('admin.campaigns.index') }}">
                        <i class="icon-feed"></i> 
                        <span class="hide-menu"> Campaigns </span>
                    </a>
                </li>    
            @else
                <li>
                    <a href="{{ route('user.dashboard.index') }}" class="waves-effect {{ menuActive('user.dashboard.index') }}">
                        <i class="icon-layers"></i> 
                        <span class="hide-menu"> Dashboard </span>
                    </a>
                </li>    
            @endif
        </ul>
    </div>
</div>