<!-- top navigation -->
@php
    $adminUser = Auth::guard('admin')->user();
@endphp
<div class="top_nav">
    <div class="nav_menu">
        <div class="nav toggle">
            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
        </div>
        <nav class="nav navbar-nav">
            <ul class=" navbar-right">
                <li class="nav-item dropdown open" style="padding-left: 15px;">
                    <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown"
                        data-toggle="dropdown" aria-expanded="false">
                        <img src="{{ $adminUser ? $adminUser->getAvatarUrl() : asset('assets/clients/img/default-avatar.png') }}"
                            alt="">{{ $adminUser ? $adminUser->name : 'Admin' }}
                    </a>
                    <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('admin.profile') }}">Tài khoản</a>
                        <a class="dropdown-item" href="{{ route('admin.logout') }}"><i
                                class="fa fa-sign-out pull-right"></i>Đăng xuất</a>
                    </div>
                </li>
                <li role="presentation" class="nav-item dropdown open">
                    <a href="javascript:;" class="dropdown-toggle info-number" id   ="navbarDropdown1"
                        data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-envelope-o"></i>
                        <span class="badge bg-green">{{ count($messages) }}</span>
                    </a>
                    <ul class="dropdown-menu list-unstyled msg_list" role="menu" aria-labelledby="navbarDropdown1">
                        @for ($i = 0; $i < min(count($messages), 5); $i++)
                            <li class="nav-item">
                                <a class="dropdown-item">
                                    <span class="image"><img src="{{ asset('assets/admin/images/User.jpg') }}"
                                            alt="Profile Image" /></span>
                                    <span>
                                        <span>{{ $messages[$i]->name }}</span>
                                        <span class="time">{{ $messages[$i]->created_at->diffForHumans() }}</span>
                                    </span>
                                    <span class="message custom-message-top">
                                        {{ $messages[$i]->message }}
                                    </span>
                                </a>
                            </li>
                        @endfor
                        <li class="nav-item">
                            <div class="text-center">
                                <a class="dropdown-item" href="{{ route('admin.contacts.index') }}">
                                    <strong>Xem tất cả liên hệ</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown open" style="margin-right: 10px;">
                    <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-bell-o"></i>
                        <span class="badge bg-green">{{ count($notifications) }}</span>
                    </a>
                    <ul class="dropdown-menu list-unstyled msg_list" role="menu">
                        @for ($i = 0; $i < min(count($notifications), 5); $i++)
                            <li class="nav-item">
                                <a class="dropdown-item" href="{{ route('admin.notifications.index') }}">
                                    <span class="image"><img src="{{ asset('assets/admin/images/bell.png') }}"
                                            alt="Profile Image" /></span>
                                    <span>
                                            {{ $notifications[$i]->title }}
                                        <span
                                            class="time">{{ $notifications[$i]->created_at->diffForHumans() }}</span>
                                    </span>
                                    <span class="message custom-message-top">
                                        {{ Str::limit($notifications[$i]->message, 25) }}
                                    </span>
                                </a>
                            </li>
                        @endfor
                        <li class="nav-item">
                            <div class="text-center">
                                <a class="dropdown-item" href="{{ route('admin.notifications.index') }}">
                                    <strong>Xem tất cả thông báo</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->
