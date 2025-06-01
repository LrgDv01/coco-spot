   
@include('user.partials.__header')
@include('user.partials.__nav')
<main id="main" class="main">
   <section class="section notifications">
        <div class="position-fixed d-flex flex-column align-items-center justify-content-end rounded-3" 
                style="top:6%; right:0; left:0; z-index: 995; height: 70px; width: 100%; background-color: #134125;">
            <h1 class="fs-3 mt-3 text-white"><strong>Notifications</strong></h1>
        </div>
        <div style="margin-top: 70px;"></div>
        @if($notifications->isEmpty())
            <div class="alert alert-secondary text-center" role="alert">
                <p class="text-secondary text-muted"> - No new notifications at the moment -</p>
            </div>
        @else
            <div class="card bg-transparent border border-0 shadow-none">
                <div class="card-body text-truncate p-0" style="height: 100%; overflow-y: auto;">
                    <ul class="list-group">
                        @foreach ($notifications as $notifs)
                            <li class="list-group-item d-flex flex-column align-items-start shadow-sm p-3 mb-2" 
                                style="border: 1px solid #188754; border-radius: 8px;">
                                <div class="mb-2">
                                    <h5 class="mb-1 fw-bold text-truncate" style="font-size: 100%;">Your technician set a schedule for visit</h5>
                                    <p class="mb-1">
                                        <strong>Assigned ID:</strong> {{ $notifs->request_id }}<br>
                                        <strong>Date/Time:</strong> {{ $notifs->created_at->timezone('Asia/Manila')->format('F d, Y | h:i A')  }}
                                    </p>
                                </div>
                                 <div class="mt-3 align-self-end">
                                    <a href="{{ route('user.farmer.viewDetails', $notifs->id) }}" 
                                        class="btn fw-bold {{ $notifs->status === 'Confirmed' || $notifs->status === 'Verified' ? 'btn-outline-secondary disabled' : 'btn-outline-success'}}">
                                        {{ $notifs->status === 'Confirmed' || $notifs->status === 'Verified' ? $notifs->status : 'View Details' }} 
                                    </a>
                                </div> 
                              {{--  <div class="mt-3 align-self-end">
                                    <a href="{{ route('user.farmer.viewDetails', $notifs->id) }}" 
                                        class="btn fw-bold btn-outline-secondary">
                                            View Details
                                    </a>
                                </div>  --}}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>  
        @endif
    </section>
 </main>
@include('user.partials.__footer')