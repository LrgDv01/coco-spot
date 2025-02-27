<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="{{ url('/user/home') }}" class="logo d-flex align-items-center justify-content-center">
        {{-- <img src="{{ URL::asset('img/admin-profile.png') }}" alt=""> --}}
        {{--<span class="d-none d-lg-block">{{ isset($appInfo->app_name) ? $appInfo->app_name : "Coco-Spot" }} Coco-Spot</span>--}}
        <span class="d-none d-lg-block"> Coco-Spot</span>

      </a>
      <i class="text-dark bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <div class="d-flex align-items-center">

            <a class="nav-link bg-transparent border border-0 text-dark nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
              {{-- <img src="{{ Auth::user()->profile_img != '' ? asset('storage/'.Auth::user()->profile_img) : URL::asset('img/admin-profile.png') }}" alt="Profile" class="rounded-circle"> --}}
              <img src="{{ Auth::user()->profile_img ? asset('storage/'.Auth::user()->profile_img) : URL::asset('img/admin-profile.png') }}" alt="Profile" class="rounded-circle">

              <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->user_name }}</span>
            </a><!-- End Profile Iamge Icon -->
  
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
              <li class="dropdown-header">
                <h6>{{ Auth::user()->full_name }}</h6>
                {{--<span>Farmer</span>--}}
              </li>
              <li>
                <hr class="dropdown-divider mb-2">
              </li>
              <li class="">
                <button form="logoutform" type="submit" class="dropdown-item d-flex align-items-center justify-content-center">
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
          <i class="bi bi-house"></i>  
          <span>My Farm</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/services') ? 'active' : 'inactive' }}" href="{{ url('/user/services') }}">
        <i class="bi bi-clipboard-data"></i>  
          <span>Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('user/farmer/settings') ? 'active' : 'inactive' }}" href="{{ url('/user/farmer/settings') }}">
          <i class="bi bi-gear"></i>  
          <span>Settings</span>
        </a>
      </li>
    @endif
    @if (auth()->user()->isTechnician())
      <li class="nav-item">
        <a class="nav-link bg-transparent border border-0 text-white " href="{{ url('/user/notifications') }}">
          <i class="bi bi-bell"></i>  
          <span>Notifications</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link bg-transparent border border-0 text-white " href="{{ url('/user/reports') }}">
          <i class="bi bi-clipboard-data"></i>  
          <span>Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link bg-transparent border border-0 text-white " href="{{ url('/user/technician/settings') }}">
          <i class="bi bi-gear"></i>  
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Cancel
        </button>
        <form action="{{ route('logout') }}" method="POST" onsubmit="disableSubmitButton(this)">
          @csrf
          <button type="submit" class="btn btn-primary" id="logoutSubmitButton">
            Yes
          </button>
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