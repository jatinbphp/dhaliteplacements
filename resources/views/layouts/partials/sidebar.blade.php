<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link" wire:navigate>
      <img src="{{asset('adminlte/img/businessman.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" wire:navigate>
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->is('candidate', 'visa-candidate') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-user"></i>
                        <p>
                            Candidate
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('candidate') }}" class="nav-link {{ request()->is('candidate') ? 'active' : '' }}" wire:navigate>
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Candidate</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('visa-candidate') }}" class="nav-link {{ request()->is('visa-candidate') ? 'active' : '' }}" wire:navigate>
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Visa Candidate</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item {{ request()->is('l-company', 'c-company', 'p-company', 'our-company') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-classic fa-solid fa-building"></i>
                        <p>
                            Companies
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('l-company') }}" class="nav-link {{ request()->is('l-company') ? 'active' : '' }}" wire:navigate>
                                <i class="far fa-circle nav-icon"></i>
                                <p>L Company</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('b-company') }}" class="nav-link {{ request()->is('b-company') ? 'active' : '' }}" wire:navigate>
                                <i class="far fa-circle nav-icon"></i>
                                <p>B Company</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('p-company') }}" class="nav-link {{ request()->is('p-company') ? 'active' : '' }}" wire:navigate>
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pv Company</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('our-company') }}" class="nav-link {{ request()->is('our-company') ? 'active' : '' }}" wire:navigate>
                                <i class="far fa-circle nav-icon"></i>
                                <p>Our Company</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('visa') }}" class="nav-link {{ request()->is('visa') ? 'active' : '' }}" wire:navigate>
                        <i class="nav-icon fa fa-passport"></i>
                        <p>Visa</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('time-sheet') }}" class="nav-link {{ request()->is('time-sheet') ? 'active' : '' }}" wire:navigate>
                        <i class="nav-icon fa fa-clock"></i>
                        <p>Time Sheet</p>
                    </a>
                </li>

                <!-- Users -->
                <!-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li> -->

                <!-- Reports -->
                <!-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Reports</p>
                    </a>
                </li> -->

                <!-- Settings -->
               <!--  <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Settings</p>
                    </a>
                </li> -->

                <!-- Logout -->
                <!-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li> -->

            </ul>
        </nav>
    </div>
</aside>
