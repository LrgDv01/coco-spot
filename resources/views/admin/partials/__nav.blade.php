


<header id="header" class="header fixed-top d-flex align-items-center py-4">

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{ url('/admin') }}" class="logo d-flex align-items-center justify-content-center">
        <img src="{{ URL::asset('img/logo.png') }}" alt="">
        <span class="d-none d-lg-block fs-5">Coco-Spot</span>
      </a>
      <i class="text-dark bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
       @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <div class="d-flex align-items-center">
            
      <!-- Notification Bell -->
      <a href="#notification"
        data-bs-toggle="modal"
        data-bs-target="#notificationmodal"
        id="notification-bell"
        class="me-5 mt-2 position-relative">
          <i class="text-success bi bi-bell-fill fs-3"></i>
          @if ($unreadCount > 0)
              <span id="notification-count" 
                class="position-absolute text-white text-center translate-middle bg-danger border border-light"
                style="border-radius: 50%; width:25px; height:auto;">
                  {{ $unreadCount }}
              </span>
          @endif
      </a>

            <a class="nav-link bg-transparent border border-0 text-dark nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
              <img src="{{ Auth::user()->profile_img != '' ? asset('storage/'.Auth::user()->profile_img) : URL::asset('img/admin-profile.png') }}" alt="Profile" class="rounded-circle">
              <span class="d-none d-md-block dropdown-toggle ps-2">{{ auth()->user()->isAdmin() ? "Admin" : "Farmer" }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
              <li class="dropdown-header">
                <h6>{{ auth()->user()->isAdmin() ? "Administrator" : "Personel" }}</h6>
                <span>{{ auth()->user()->isAdmin() ? "Super Admin" : "Sub Admin" }}</span>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
    
              @if (auth()->user()->isAdmin())
                <li>
                  <a class="dropdown-item d-flex align-items-center" href="{{ url('/admin/settings') }}">
                    <i class="text-dark bi bi-gear"></i>
                    <span>Settings</span>
                  </a>
                </li>
              @endif
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center" 
                  data-bs-toggle="modal"
                  data-bs-target="#forgotpasswordmodal" href="#forgotpassword">
                  <i class="text-dark bi bi-question-circle"></i>
                  <span>Forgot Passwword?</span>
                </a>
              </li>
              {{-- <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ url('/admin/help') }}">
                  <i class="text-dark bi bi-question-circle"></i>
                  <span>Need Help?</span>
                </a>
              </li> --}}
              <li>
                <hr class="dropdown-divider">
              </li>
  
              <li>
                <button form="logoutform" type="submit" class="dropdown-item d-flex align-items-center">
                  <i class="text-dark bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
                </button>
              </li>
  
            </ul><!-- End Profile Dropdown Items -->
          </div><!-- End Profile Iamge Icon -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

</header>
  
<style>

  .modal-dialog-notif {
    position: fixed;
    top: 7%; /* Distance from the top */
    right: 10%; /* Distance from the right */
    margin: 0; /* Remove default centering margins */
    width: 500px;
    height: 70vh;
    max-width: 400px; /* Optional: Adjust width as needed */
  }
 
  @media (max-width: 576px) {
    .modal-dialog-notif {
      top: 10px;
      right: 10px;
      max-width: 90%; /* Responsive width */
    }
  }

</style>



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
  <ul class="sidebar-nav flex-column" id="sidebar-nav">
    @if (auth()->user()->isAdmin())
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('admin') ? 'active' : 'inactive' }}" href="{{ url('/admin') }}">
          <i class="bi bi-grid-fill"></i>
          <span>Dashboard</span>
        </a>
      </li>  
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('admin/map') ? 'active' : 'inactive' }}" href="{{ url('/admin/map') }}">
          <i class="bi bi-map-fill"></i>
          <span>Map</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('admin/map_simulation') ? 'active' : 'inactive' }}" href="{{ url('/admin/map_simulation') }}">
          <i class="bi bi-geo-fill"></i> 
          <span>Simulation</span>
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


<!-- Notification Modal -->
<div class="modal fade"
     id="notificationmodal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false"
     role="dialog"
     aria-labelledby="notificationmodalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-notif modal-dialog-scrollable rounded" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationmodalTitle"><strong>Notifications</strong></h5>
                <button type="button" class="btn-close fs-1" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #f0f8f0; border: 1px solid #28a745;">
                <div class="list-group list-group-flush bg-transparent" id="notification-list">
                    @if ($notif->isNotEmpty())
                        @foreach ($notif as $n)
                        @if ($n->status === 'unread')
                          <div class="rounded rounded-4 mb-3">
                            <a href="{{route('admin.notifications.read', $n->id)}}"
                               class="list-group-item list-group-item-action flex-column align-items-start"
                               style= "background-color: #f9ecdb;"
                               data-id="{{ $n->id }}">
                                    <span class="badge bg-danger mb-3">New Disease Alert</span>
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-danger"><strong>{{ $n->disease }}</strong></h5>
                                    <small class="text-muted"><strong>{{ $n->created_at->format('M/d/Y H:i') }}</strong> </small>
                                </div>
                            </a>
                            </div>
                            @else ()
                              <div class="rounded rounded-4 mb-3">
                              <a href="{{route('admin.notifications.read', $n->id)}}"
                                class="list-group-item list-group-item-action flex-column align-items-start"
                                style="background-color: white;"
                                data-id="{{ $n->id }}">
                                  <div class="d-flex w-100 justify-content-between">
                                      <h5 class="mb-1 text-secondary"><strong>{{ $n->disease }}</strong></h5>
                                      <small class="text-muted"><strong>{{ $n->created_at->format('M/d/Y H:i') }}</strong> </small>
                                  </div>
                              </a>
                              </div>
                            @endif 
                        @endforeach
                    @else
                        <div class="text-center">No Notifications</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


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
          <button type="submit" class="btn btn-success" id="logoutSubmitButton">
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