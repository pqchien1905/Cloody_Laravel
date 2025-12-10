<div class="iq-top-navbar">
    <div class="iq-navbar-custom">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                <i class="ri-menu-line wrapper-menu"></i>
                <a href="{{ route('cloody.dashboard') }}" class="header-logo">
                    <img src="{{ asset('assets/images/Cloody.png') }}" class="img-fluid rounded-normal light-logo" alt="logo">
                </a>
            </div>
            <div class="iq-search-bar device-search">
                                <form method="GET" action="{{ request()->routeIs('admin.*') ? route('admin.files.index') : route('cloody.files') }}" onsubmit="return true;">
                    <div class="input-prepend input-append">
                        <div class="btn-group">
                            <label class="dropdown-toggle searchbox" data-toggle="dropdown">
                                                <input name="search" class="dropdown-toggle search-query text search-input" type="text" placeholder="{{ __('navbar.search_placeholder') }}" onkeypress="if(event.keyCode==13){event.stopPropagation(); this.form.submit(); return false;}">
                                <span class="search-replace"></span>
                                <button type="submit" class="search-link" style="border:none; background:none; padding:0;" onclick="event.stopPropagation();">
                                    <i class="ri-search-line"></i>
                                </button>
                                <span class="caret"><!--icon--></span>
                            </label>
                                <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ request()->routeIs('admin.*') 
                                        ? route('admin.files.index', ['type' => 'pdf']) 
                                        : route('cloody.files', ['category' => 'documents']) }}">
                                        <div class="item"><i class="far fa-file-pdf bg-info"></i>{{ __('navbar.pdfs') }}</div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ request()->routeIs('admin.*') 
                                        ? route('admin.files.index', ['category' => 'documents']) 
                                        : route('cloody.files', ['category' => 'documents']) }}">
                                        <div class="item"><i class="far fa-file-alt bg-primary"></i>{{ __('navbar.documents') }}</div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ request()->routeIs('admin.*') 
                                        ? route('admin.files.index', ['category' => 'spreadsheets']) 
                                        : route('cloody.files', ['category' => 'spreadsheets']) }}">
                                        <div class="item"><i class="far fa-file-excel bg-success"></i>{{ __('navbar.spreadsheets') }}</div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ request()->routeIs('admin.*') 
                                        ? route('admin.files.index', ['category' => 'presentations']) 
                                        : route('cloody.files', ['category' => 'presentations']) }}">
                                        <div class="item"><i class="far fa-file-powerpoint bg-danger"></i>{{ __('navbar.presentations') }}</div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ request()->routeIs('admin.*') 
                                        ? route('admin.files.index', ['category' => 'images']) 
                                        : route('cloody.files', ['category' => 'images']) }}">
                                        <div class="item"><i class="far fa-file-image bg-warning"></i>{{ __('navbar.images') }}</div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ request()->routeIs('admin.*') 
                                        ? route('admin.files.index', ['category' => 'videos']) 
                                        : route('cloody.files', ['category' => 'videos']) }}">
                                        <div class="item"><i class="far fa-file-video bg-info"></i>{{ __('navbar.videos') }}</div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ request()->routeIs('admin.*') 
                                        ? route('admin.files.index', ['type' => 'audio']) 
                                        : route('cloody.files', ['category' => 'audio']) }}">
                                        <div class="item"><i class="far fa-file-audio bg-secondary"></i>{{ __('navbar.audio') }}</div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ request()->routeIs('admin.*') 
                                        ? route('admin.files.index', ['category' => 'archives']) 
                                        : route('cloody.files', ['category' => 'archives']) }}">
                                        <div class="item"><i class="far fa-file-archive bg-dark"></i>{{ __('navbar.archives') }}</div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="d-flex align-items-center">
            
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-label="Toggle navigation">
                    <i class="ri-menu-3-line"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto navbar-list align-items-center">
                        <li class="nav-item nav-icon search-content">
                            <a href="#" class="search-toggle rounded" id="dropdownSearch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-search-line"></i>
                            </a>
                            <div class="iq-search-bar iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownSearch">
                                <form method="GET" action="{{ request()->routeIs('admin.*') ? route('admin.files.index') : route('cloody.files') }}" class="searchbox p-2">
                                    <div class="form-group mb-0 position-relative">
                                        <input type="text" name="search" class="text search-input font-size-12" placeholder="{{ __('navbar.search_placeholder') }}">
                                        <button type="submit" class="search-link" style="border:none; background:none; padding:0;">
                                            <i class="las la-search"></i>
                                        </button> 
                                    </div>
                                </form>
                                <ul class="iq-search-suggestion list-unstyled p-3">
                                    <li>
                                        <a href="{{ request()->routeIs('admin.*') 
                                            ? route('admin.files.index', ['type' => 'pdf']) 
                                            : route('cloody.files', ['category' => 'documents']) }}" class="d-flex align-items-center">
                                            <div class="icon-small bg-danger-light rounded mr-3">
                                                <i class="ri-file-pdf-line font-size-18 text-danger"></i>
                                            </div>
                                            <div>{{ __('navbar.pdfs') }}</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ request()->routeIs('admin.*') 
                                            ? route('admin.files.index', ['category' => 'documents']) 
                                            : route('cloody.files', ['category' => 'documents']) }}" class="d-flex align-items-center">
                                            <div class="icon-small bg-primary-light rounded mr-3">
                                                <i class="ri-file-text-line font-size-18 text-primary"></i>
                                            </div>
                                            <div>{{ __('navbar.documents') }}</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ request()->routeIs('admin.*') 
                                            ? route('admin.files.index', ['category' => 'spreadsheets']) 
                                            : route('cloody.files', ['category' => 'spreadsheets']) }}" class="d-flex align-items-center">
                                            <div class="icon-small bg-success-light rounded mr-3">
                                                <i class="ri-file-excel-line font-size-18 text-success"></i>
                                            </div>
                                            <div>{{ __('navbar.spreadsheets') }}</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ request()->routeIs('admin.*') 
                                            ? route('admin.files.index', ['category' => 'presentations']) 
                                            : route('cloody.files', ['category' => 'presentations']) }}" class="d-flex align-items-center">
                                            <div class="icon-small bg-warning-light rounded mr-3">
                                                <i class="ri-slideshow-line font-size-18 text-warning"></i>
                                            </div>
                                            <div>{{ __('navbar.presentations') }}</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ request()->routeIs('admin.*') 
                                            ? route('admin.files.index', ['category' => 'images']) 
                                            : route('cloody.files', ['category' => 'images']) }}" class="d-flex align-items-center">
                                            <div class="icon-small bg-info-light rounded mr-3">
                                                <i class="ri-image-line font-size-18 text-info"></i>
                                            </div>
                                            <div>{{ __('navbar.images') }}</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ request()->routeIs('admin.*') 
                                            ? route('admin.files.index', ['category' => 'videos']) 
                                            : route('cloody.files', ['category' => 'videos']) }}" class="d-flex align-items-center">
                                            <div class="icon-small bg-danger-light rounded mr-3">
                                                <i class="ri-video-line font-size-18 text-danger"></i>
                                            </div>
                                            <div>{{ __('navbar.videos') }}</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ request()->routeIs('admin.*') 
                                            ? route('admin.files.index', ['type' => 'audio']) 
                                            : route('cloody.files', ['category' => 'audio']) }}" class="d-flex align-items-center">
                                            <div class="icon-small bg-info-light rounded mr-3">
                                                <i class="ri-music-line font-size-18 text-info"></i>
                                            </div>
                                            <div>{{ __('navbar.audio') }}</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ request()->routeIs('admin.*') 
                                            ? route('admin.files.index', ['category' => 'archives']) 
                                            : route('cloody.files', ['category' => 'archives']) }}" class="d-flex align-items-center">
                                            <div class="icon-small bg-secondary-light rounded mr-3">
                                                <i class="ri-folder-zip-line font-size-18 text-secondary"></i>
                                            </div>
                                            <div>{{ __('navbar.archives') }}</div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li> 
                        <li class="nav-item nav-icon dropdown">
                            <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-question-line"></i>
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton01">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-0 ">
                                        <div class="p-3">
                                            <a href="#" class="iq-sub-card pt-0"><i class="ri-questionnaire-line"></i>{{ __('navbar.help') }}</a>
                                            <a href="#" class="iq-sub-card"><i class="ri-recycle-line"></i>{{ __('navbar.training') }}</a>
                                            <a href="#" class="iq-sub-card"><i class="ri-refresh-line"></i>{{ __('navbar.updates') }}</a>
                                            <a href="#" class="iq-sub-card"><i class="ri-service-line"></i>{{ __('navbar.terms') }}</a>
                                            <a href="#" class="iq-sub-card"><i class="ri-feedback-line"></i>{{ __('navbar.feedback') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item nav-icon dropdown">
                            <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButtonLang" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-global-line"></i>
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButtonLang">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-0 ">
                                        <div class="p-3">
                                            <h6 class="mb-2 text-muted">{{ __('navbar.language') }}</h6>
                                            @php
                                                // Get current locale - check both session and app
                                                $sessionLocale = session('locale');
                                                $appLocale = app()->getLocale();
                                                $currentLocale = $sessionLocale ?: $appLocale;
                                            @endphp
                                            @if($currentLocale === 'en')
                                                <form method="POST" action="{{ route('locale.switch') }}" class="mb-2">
                                                    @csrf
                                                    <input type="hidden" name="lang" value="vi">
                                                    <button type="submit" class="iq-sub-card w-100 text-left pt-0" style="border:none; background: none; box-shadow:none;">
                                                        <i class="ri-translate-2"></i>
                                                        <span>{{ __('navbar.vietnamese') }}</span>
                                                        <span class="badge badge-primary ml-2">VI</span>
                                                    </button>
                                                </form>
                                                <div class="iq-sub-card" style="opacity: 0.6;">
                                                    <i class="ri-global-line"></i>
                                                    <span>{{ __('navbar.english') }}</span>
                                                    <span class="badge badge-secondary ml-2">EN</span>
                                                </div>
                                            @else
                                                <div class="iq-sub-card mb-2" style="opacity: 0.6;">
                                                    <i class="ri-translate-2"></i>
                                                    <span>{{ __('navbar.vietnamese') }}</span>
                                                    <span class="badge badge-secondary ml-2">VI</span>
                                                </div>
                                                <form method="POST" action="{{ route('locale.switch') }}">
                                                    @csrf
                                                    <input type="hidden" name="lang" value="en">
                                                    <button type="submit" class="iq-sub-card w-100 text-left pt-0" style="border:none; background: none; box-shadow:none;">
                                                        <i class="ri-global-line"></i>
                                                        <span>{{ __('navbar.english') }}</span>
                                                        <span class="badge badge-primary ml-2">EN</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>                                
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item nav-icon dropdown"> 
                            <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton02" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-settings-3-line"></i>
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton02">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-0 ">
                                        <div class="p-3">
                                            <a href="#" class="iq-sub-card pt-0"><i class="ri-settings-3-line"></i> {{ __('navbar.settings') }}</a>
                                            <a href="#" class="iq-sub-card"><i class="ri-hard-drive-line"></i> {{ __('navbar.get_drive') }}</a>
                                            <a href="#" class="iq-sub-card"><i class="ri-keyboard-line"></i> {{ __('navbar.shortcuts') }}</a>
                                        </div>                                
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item nav-icon dropdown caption-content">
                            <a href="#" class="search-toggle dropdown-toggle d-flex align-items-center justify-content-center" id="dropdownMenuButton03" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 0;">
                                @if(Auth::user()->avatar)
                                    <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid #667eea; box-shadow: 0 2px 6px rgba(102, 126, 234, 0.4);">
                                        <img src="{{ Auth::user()->avatar_url }}" alt="user" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\'caption bg-primary line-height\' style=\'width:100%;height:100%;line-height:40px;\'>{{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}</div>';">
                                    </div>
                                @else
                                    <div class="caption bg-primary line-height">{{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}</div>
                                @endif
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton03">
                                <div class="card mb-0">
                                    <div class="card-header d-flex justify-content-between align-items-center mb-0">
                                        <div class="header-title">
                                            <h4 class="card-title mb-0">{{ __('navbar.profile') }}</h4>
                                        </div>
                                        <div class="close-data text-right badge badge-primary cursor-pointer "><i class="ri-close-fill"></i></div>
                                    </div>
                                    <div class="card-body">
                                        <div class="profile-header">
                                            <div class="cover-container text-center">
                                                @if(Auth::user()->avatar)
                                                    <div style="width: 80px; height: 80px; margin: 0 auto;">
                                                        <img src="{{ Auth::user()->avatar_url }}" class="rounded-circle" alt="avatar" style="width: 100%; height: 100%; object-fit: cover; border: 3px solid #667eea; box-shadow: 0 4px 8px rgba(0,0,0,0.15);" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\'rounded-circle profile-icon bg-primary mx-auto d-block\' style=\'width: 80px; height: 80px; line-height: 80px; font-size: 32px;\'>{{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}</div>';">
                                                    </div>
                                                @else
                                                    <div class="rounded-circle profile-icon bg-primary mx-auto d-block" style="width: 80px; height: 80px; line-height: 80px; font-size: 32px;">
                                                        {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="profile-detail mt-3">
                                                    <h5><a href="{{ route('cloody.user.profile') }}">{{ Auth::user()->name ?? 'User Name' }}</a></h5>
                                                    <p>{{ Auth::user()->email ?? 'user@example.com' }}</p>
                                                </div>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">{{ __('navbar.sign_out') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>                     
                </div> 
            </div>
        </nav>
    </div>
</div>
