<header id="header" class="header fixed-top d-flex align-items-center py-4">

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{ url('/admin') }}" class="logo d-flex align-items-center justify-content-center">
        <img src="{{ URL::asset('img/logo.png') }}" alt="">
        <span class="d-none d-lg-block fs-5">Coco-Spot</span>
      </a>
      <i class="text-dark bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    {{--        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif  --}}

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
            @php
              $prof_img = Auth::user()->profile_img ? URL::asset('storage/' . Auth::user()->profile_img) 
                          : URL::asset('img/admin.png');
            @endphp
            <a class="nav-link bg-transparent border border-0 text-dark nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
              <img src="{{ $prof_img}}" alt="Profile" class="rounded-circle">
              <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->user_name  }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
              <li class="dropdown-header">
                <h6>{{ Auth::user()->full_name }}</h6>
                <span>{{ auth()->user()->isAdmin() ? "Administrator" : "Personel"  }}</span>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              {{--               <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ url('/admin/settings') }}">
                  <i class="text-dark bi bi-gear"></i>
                  <span>Settings</span>
                </a>
              </li>  --}}

              <li>
                <hr class="dropdown-divider">
              </li>
              {{--  <li>
                <a class="dropdown-item d-flex align-items-center" 
                  data-bs-toggle="modal"
                  data-bs-target="#forgotpasswordmodal" href="#forgotpassword">
                  <i class="text-dark bi bi-question-circle"></i>
                  <span>Forgot Passwword?</span>
                </a>
              </li>  --}}
             
              {{-- <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ url('/admin/help') }}">
                  <i class="text-dark bi bi-question-circle"></i>
                  <span>Need Help?</span>
                </a>
              </li> --}}
              <li>
                <button form="logoutform" type="submit" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" 
                        data-bs-target="#logoutModal">  
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
    width: 700px;
    height: 70vh;
    max-width: 700px; /* Optional: Adjust width as needed */
  }
 
  @media (max-width: 576px) {
    .modal-dialog-notif {
      top: 10px;
      right: 10px;
      max-width: 100%; /* Responsive width */
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
          <i class="bi bi-grid-fill"></i><span>Dashboard</span>
        </a>
      </li>  
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('forums') ? 'active' : 'inactive' }}" href="{{ url('/forums') }}">
          <i class="bi bi-chat-dots-fill"></i><span>Forums</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('reports') ? 'active' : 'inactive' }}" href="{{ url('/reports') }}">
            <i class="bi bi-table"></i><span>Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('admin/map') ? 'active' : 'inactive' }}" href="{{ url('/admin/map') }}">
          <i class="bi bi-map-fill"></i><span>Map</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link border border-0 {{ Request::is('admin/map_simulation') ? 'active' : 'inactive' }}" href="{{ url('/admin/map_simulation') }}">
          <i class="bi bi-geo-fill"></i> <span>Simulation</span>
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
  aria-hidden="true" >
    <div class="modal-dialog modal-dialog-notif modal-dialog-scrollable rounded" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notificationmodalTitle"><strong>Notifications</strong></h5>
          <button type="button" class="btn-close fs-1" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="background-color: #f0f8f0; border: 1px solid #28a745;">
          <!-- Horizontal Navigation -->
          <ul class="nav nav-tabs mb-3" id="notificationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-success" id="farmers-tab" data-bs-toggle="tab" data-bs-target="#farmers" type="button" role="tab" aria-controls="farmers" aria-selected="true">Farmers</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-success" id="technicians-tab" data-bs-toggle="tab" data-bs-target="#technicians" type="button" role="tab" aria-controls="technicians" aria-selected="false">Technicians</button>
            </li>
          </ul>

          <div class="tab-content" id="notificationTabsContent">
            <!-- Farmers Tab -->
            <div class="tab-pane fade show active" id="farmers" role="tabpanel" aria-labelledby="farmers-tab">
                <div class="list-group list-group-flush bg-transparent" id="notification-list-farmers">
                @if ($notif->where('user.user_type', '1')->isNotEmpty())
                  @foreach ($notif->where('user.user_type', '1') as $n)
                  @php
                    $prof_img = $n->user->profile_img ? URL::asset('storage/' . $n->user->profile_img) 
                          : URL::asset('img/farmer.png');
                  @endphp
                  <div class="rounded rounded-4 mb-3">
                    <a href="{{route('admin.notifications.read', $n->id)}}"
                      class="list-group-item list-group-item-action flex-column align-items-start"
                      style="{{ $n->status === 'unread' ? 'background-color: #f9ecdb;' : 'background-color: white;' }}"
                      data-id="{{ $n->id }}">
                        <div class="d-flex flex-row">
                        <img src="{{ $prof_img }}" alt="user_photo" class="img-fluid rounded-circle" style="width: 40px; height: 40px;">  
                        <p class="ms-2"><span class="fw-bold">{{ $n->user->full_name}}</span> - reported a disease. </p>
                        </div>
                        <div class="d-flex w-100 justify-content-between mt-3">
                        <h5 class="mb-1 {{ $n->status === 'unread' ? 'text-danger' : 'text-secondary' }}"><strong>{{ $n->disease }}</strong></h5>
                        <small class="text-muted"><strong>{{  $n->created_at->timezone('Asia/Manila')->format('F d, Y | h:i A') }}</strong> </small>
                        </div>
                    </a>
                  </div>
                  @endforeach
                @else
                  <div class="text-center">No Notifications for Farmers</div>
                @endif
                </div>
            </div>

            <!-- Technicians Tab -->
        
            {{--     <div class="tab-pane fade" id="technicians" role="tabpanel" aria-labelledby="technicians-tab">
              @php
              // This array should ideally be passed from your controller.
              // Using the data you provided for this example.
              $technicians_list = [
                  ['id' => 5, 'name' => 'Lovely Labor'],
                  ['id' => 6, 'name' => 'Riza Manalo'],
                  ['id' => 7, 'name' => 'Nora Acueza'],
                  ['id' => 8, 'name' => 'Charlon Pabon'],
                  ['id' => 9, 'name' => 'Rizaldy Acuno'],
                  ['id' => 10, 'name' => 'Ruel de Villa'],
                  ['id' => 11, 'name' => 'Rose Ann Alejandro'],
              ];
              @endphp
            
              <div class="list-group list-group-flush bg-transparent" id="notification-list-technicians">
                @if ($notif->where('user.user_type', '2')->isNotEmpty())
                  @foreach ($notif->where('user.user_type', '2') as $n)
                    @php
                      $prof_img = $n->user->profile_img ? URL::asset('storage/' . $n->user->profile_img) 
                            : URL::asset('img/technician.png');
                    @endphp
                    <div class="rounded rounded-4 mb-3">
                      <a href="{{route('admin.notifications.read', $n->id)}}"
                        class="list-group-item list-group-item-action flex-column align-items-start"
                        style="{{ $n->status === 'unread' ? 'background-color: #f9ecdb;' : 'background-color: white;' }}"
                        data-id="{{ $n->id }}">
                          <div class="d-flex flex-row">
                          <img src="{{ $prof_img }}" alt="user_photo" class="img-fluid rounded-circle" style="width: 40px; height: 40px;">  
                          <p class="ms-2"><span class="fw-bold">{{ $n->user->full_name}}</span> - reported a disease. </p>
                          </div>
                          <div class="d-flex w-100 justify-content-between mt-3">
                          <h5 class="mb-1 {{ $n->status === 'unread' ? 'text-danger' : 'text-secondary' }}"><strong>{{ $n->disease }}</strong></h5>
                          <small class="text-muted"><strong>{{  $n->created_at->timezone('Asia/Manila')->format('F d, Y | h:i A') }}</strong> </small>
                          </div>
                      </a>
                    </div>
                  @endforeach
                @else
                  <div class="text-center">No Notifications for Technicians</div>
                @endif
              </div>
            </div> --}}

            <div class="tab-pane fade" id="technicians" role="tabpanel" aria-labelledby="technicians-tab">
              @php
                $technicians_list = [
                    ['id' => 5, 'name' => 'Lovely Labor'],
                    ['id' => 6, 'name' => 'Riza Manalo'],
                    ['id' => 7, 'name' => 'Nora Acueza'],
                    ['id' => 8, 'name' => 'Charlon Pabon'],
                    ['id' => 9, 'name' => 'Rizaldy Acuno'],
                    ['id' => 10, 'name' => 'Ruel de Villa'],
                    ['id' => 11, 'name' => 'Rose Ann Alejandro'],
                ];
              @endphp

              @if (!empty($technicians_list) && $notif->where('user.user_type', '2')->isNotEmpty())
                  <div class="d-flex align-items-start">
                      <div class="nav flex-column nav-pills me-2" id="v-pills-technician-tab" role="tablist" aria-orientation="vertical" style="min-width: 130px; width: 27%;">
                    @foreach ($technicians_list as $index => $technician)
                        <button class="nav-link text-start {{ $index == 0 ? 'active' : '' }}"
                          id="v-pills-tech-{{ $technician['id'] }}-tab"
                          data-bs-toggle="pill"
                          data-bs-target="#v-pills-tech-{{ $technician['id'] }}"
                          type="button" role="tab"
                          aria-controls="v-pills-tech-{{ $technician['id'] }}"
                          aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                      {{ $technician['name'] }}
                        </button>
                    @endforeach
                      </div>
                      <div class="vr"></div> <!-- Separation line -->
                      <div class="tab-content flex-grow-1" id="v-pills-technician-tabContent">
                        @foreach ($technicians_list as $index => $technician)
                            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                          id="v-pills-tech-{{ $technician['id'] }}"
                          role="tabpanel"
                          aria-labelledby="v-pills-tech-{{ $technician['id'] }}-tab">
                          
                          <div class="list-group list-group-flush bg-transparent">
                              @php
                            // Filter notifications for the current technician
                            $technician_notifications = $notif->where('user.user_type', '2')->where('user_id', $technician['id']);
                              @endphp

                              @if ($technician_notifications->isNotEmpty())
                            @foreach ($technician_notifications as $n)
                                @php
                              $prof_img = $n->user->profile_img ? URL::asset('storage/' . $n->user->profile_img) 
                                      : URL::asset('img/technician.png');
                                @endphp
                                <div class="rounded rounded-4 mb-3 ms-2">
                              <a href="{{route('admin.notifications.read', $n->id)}}"
                                class="list-group-item list-group-item-action flex-column align-items-start"
                                style="{{ $n->status === 'unread' ? 'background-color: #f9ecdb;' : 'background-color: white;' }}"
                                data-id="{{ $n->id }}">
                                  <div class="d-flex flex-row align-items-center">
                                <img src="{{ $prof_img }}" alt="user_photo" class="img-fluid rounded-circle me-2" style="width: 40px; height: 40px;"> 
                                <p class="mb-0"><span class="fw-bold">{{ $n->user->full_name}}</span> - reported a disease.</p>
                                  </div>
                                  <div class="d-flex w-100 justify-content-between mt-2">
                                <h6 class="mb-1 {{ $n->status === 'unread' ? 'text-danger' : 'text-secondary' }}"><strong>{{ $n->disease }}</strong></h6>
                                <small class="text-muted"><strong>{{ $n->created_at->timezone('Asia/Manila')->format('F d, Y | h:i A') }}</strong></small>
                                  </div>
                              </a>
                                </div>
                            @endforeach
                              @else
                            <div class="text-center p-3">No notifications for {{ $technician['name'] }}.</div>
                              @endif
                          </div>
                            </div>
                        @endforeach
                      </div>
                  </div>
              @else
                  {{-- This condition handles two cases:
                      1. The $technicians_list is empty.
                      2. There are no notifications for any technician (user_type 2). --}}
                  @if($notif->where('user.user_type', '2')->isEmpty())
                      <div class="text-center">No Notifications for Technicians.</div>
                  @else
                      {{-- This case implies $technicians_list was empty but there might have been notifications if list was populated --}}
                      <div class="text-center">Technician list is not available or no technicians found.</div>
                  @endif
              @endif
          </div>
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