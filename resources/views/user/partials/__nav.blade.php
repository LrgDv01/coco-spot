<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="{{ url('/user/home') }}" class="logo d-flex align-items-center justify-content-center">
        {{-- <img src="{{ URL::asset('img/admin-profile.png') }}" alt=""> --}}
        {{--<span class="d-none d-lg-block">{{ isset($appInfo->app_name) ? $appInfo->app_name : "Coco-Spot" }} Coco-Spot</span>--}}
        <span class="d-none d-lg-block"> Coco-Spot </span>

      </a>
      <i class="text-dark bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <div class="d-flex align-items-center">
              @php
                $prof_img = Auth::user()->profile_img ? URL::asset('storage/' . Auth::user()->profile_img) 
                            : (Auth::user()->user_type === '1' ? URL::asset('img/farmer.png') 
                            : URL::asset('img/technician.png'));
              @endphp
            <a class="nav-link bg-transparent border border-0 text-dark nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
              <img src="{{ $prof_img }}" alt="Profile" class="img-fluid rounded-circle" style="width: 37px; height: 40px;">
              <span class="d-md-block dropdown-toggle ps-2">{{ Auth::user()->user_name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
              <li class="dropdown-header"> 
                <h6>{{ Auth::user()->full_name }}</h6>
                <span>{{ auth()->user()->isFarmer() ? "Farmer" : "Technician"  }}</span>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->isFarmer() ? url('/user/farmer/settings') 
                          : url('/user/technician/settings')  }}">  <i class="text-dark bi bi-gear"></i>
                  <span>Settings</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              {{--   <li>
                <a class="dropdown-item d-flex align-items-center" 
                  data-bs-toggle="modal"
                  data-bs-target="#forgotpasswordmodal" href="#forgotpassword">
                  <i class="text-dark bi bi-question-circle"></i>
                  <span>Forgot Password?</span>
                </a>
              </li>--}}
              <li class="">
                <button type="button"  class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" 
                        data-bs-target="#logoutModal">
                  <i class="text-dark bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
                </button>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </nav>
</header>

<style>
  .nav-item .nav-link.active {
    background-color: #f8f3f2;
    color: darkgreen;
    font-weight: bold;
    border-radius:20px;
  }
  .nav-item .nav-link.active .bi,
  .nav-item .nav-link.active:hover,
  .nav-item .nav-link.active:hover .bi {
    color: darkgreen;
  }
  .nav-item .nav-link.inactive {
    background-color: transparent;
    border-radius:20px;
    color: #f8f3f2;
  }
  .nav-item .nav-link.inactive .bi,
  .nav-item .nav-link.inactive:hover .bi {
    color: #f8f3f2;
  }
  .nav-item .nav-link.inactive:hover {
    background-color: #84a090;
    border-radius:20px;
    color: #f8f3f2;
  }
</style>

<aside id="sidebar" class="sidebar d-flex flex-column justify-content-between" style="background-color:#134125">
  <ul class="sidebar-nav" id="sidebar-nav">
    @if (auth()->user()->isFarmer())
      <li class="nav-item"> 
        <a class="nav-link border border-0 {{ Request::is('user/farm') ? 'active' : 'inactive' }}" href="{{ url('/user/farm') }}">
          <i class="bi bi-house-fill"></i>  
          <span>My Farm</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/farmer/notifications') ? 'active' : 'inactive' }}" href="{{ url('/user/farmer/notifications') }}">
          <i class="bi bi-bell-fill"></i>
          @if ($unreadCount > 0)
              <span id="notification-count" 
                class="position-absolute text-white text-center translate-middle bg-danger border border-light"
                style="border-radius: 50%; width:25px; height:auto;">
                  {{ $unreadCount }}
              </span>
          @endif
          <span>Notifications</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('forums') ? 'active' : 'inactive' }}" href="{{ url('/forums') }}">
          <i class="bi bi-chat-dots-fill"></i>
          <span>Forums</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/map') ? 'active' : 'inactive' }}" href="{{ url('/user/map') }}">
        <i class="bi bi-map-fill"></i>  
          <span>Map</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/simulation') ? 'active' : 'inactive' }}" href="{{ url('/user/simulation') }}">
        <i class="bi bi-geo-fill"></i> 
          <span>Simulation</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/farmer/reports') ? 'active' : 'inactive' }}" href="{{ url('/user/farmer/reports') }}">
        <i class="bi bi-clipboard-data-fill"></i>  
          <span>Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/farmer/settings') ? 'active' : 'inactive' }}" href="{{ url('/user/farmer/settings') }}">
          <i class="bi bi-gear-fill"></i>  
          <span>Settings</span>
        </a>
      </li>
    @endif
    @if (auth()->user()->isTechnician())
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/notifications') ? 'active' : 'inactive' }}" href="{{ url('/user/notifications') }}">
          <i class="bi bi-bell-fill"></i>
          @if ($unreadCount > 0)
              <span id="notification-count" 
                class="position-absolute text-white text-center translate-middle bg-danger border border-light"
                style="border-radius: 50%; width:25px; height:auto;">
                  {{ $unreadCount }}
              </span>
          @endif
          <span>Notifications</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('forums') ? 'active' : 'inactive' }}" href="{{ url('/forums') }}">
          <i class="bi bi-chat-dots-fill"></i>
          <span>Forums</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/map') ? 'active' : 'inactive' }}" href="{{ url('/user/map') }}">
        <i class="bi bi-map-fill"></i>  
          <span>Map</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/simulation') ? 'active' : 'inactive' }}" href="{{ url('/user/simulation') }}">
        <i class="bi bi-geo-fill"></i> 
          <span>Simulation</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/reports') ? 'active' : 'inactive' }}" href="{{ url('/user/reports') }}">
          <i class="bi bi-clipboard-data-fill"></i>  
          <span>Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/monitor') ? 'active' : 'inactive' }}" href="{{ url('/user/monitor') }}">
        <i class="bi bi-display-fill"></i>  
          <span>Monitoring</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/technician/settings') ? 'active' : 'inactive' }}" href="{{ url('/user/technician/settings') }}">
          <i class="bi bi-gear-fill"></i>  
          <span>Settings</span>
        </a>
      </li>
    @endif
  </ul>
  <div class="sidebar-nav">
      <li class="nav-item">
        <button 
          type="button" 
          class="nav-link border border-0 {{ Request::is('logout') ? 'active' : 'inactive' }}" href="{{ url('/logout') }}"
          data-bs-toggle="modal" 
          data-bs-target="#logoutModal">
            <i class="text-white bi bi-arrow-bar-left"></i>
            <span>Logout</span>
        </button>
      </li>
  </div> 
</aside>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel"><strong>Logout</strong></h5>
      </div>
      <div class="modal-body">
        Are you sure you want to log out ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Cancel
        </button>
        <form action="{{ route('logout') }}" method="POST" onsubmit="disableSubmitButton(this)">
          @csrf
          <button type="submit" class="btn btn-success" id="logoutSubmitButton">Yes</button>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  function disableSubmitButton(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true; 
    submitButton.innerHTML = 'Loading . .'; 
  }
</script> 