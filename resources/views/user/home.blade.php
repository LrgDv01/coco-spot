@include('user.partials.__header')
@include('user.partials.__nav')

<style>
    .sticky-alert {
        position: fixed;
        top: 20px;
        left: -100%; 
        transform: translateY(0);
        z-index: 1050;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        animation: slideInFromLeft 0.3s ease-in-out forwards;  
    }

    @keyframes slideInFromLeft {
        0% {
            left: -100%;  
            opacity: 0;
        }
        100% {
            left: 50%; 
            opacity: 1;
            transform: translateX(-50%);
        }
    }

    @keyframes slideOutToRight {
        0% {
            left: 50%; 
            opacity: 1;
        }
        100% {
            left: 100%;  
            opacity: 0;
        }
    }

    .form-check-input.background-green {
        background-color: #198754; 
        border-color: #198754;     
    }
    .form-check-input.background-red {
        background-color: #dc3545;
        border-color: #dc3545;   
    }

    .form-check-input.shadow-green {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25); 
        outline-offset: 1px; 
        outline: 1px solid rgba(25, 135, 84, 0.6); 
        transition: outline-color 0.3s ease; 
    }

    .form-check-input.shadow-red {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25); 
        outline-offset: 1px;
        outline: 1px solid rgba(220, 53, 69, 0.6); 
        transition: outline-color 0.3s ease; 
    }

    .input-group {
        flex-wrap: nowrap;
    }
    .btn-primary {
        white-space: nowrap;
    }
    .map-container {
        width: 100%;
        height: 0;
        opacity: 0;
        margin-top: 0px;
        transition: all 0.5s ease-in-out;
        overflow: hidden;
    }
    .map-container.visible {
        height: 300px;
        opacity: 1;
        margin-top: 10px;
    }
    @media (max-width: 576px) {
        .form-control, .btn {
            font-size: 14px;
        }
        .map-container.visible {
            height: 200px;
        }
    }
</style>



<main id="main" class="main">
    <!-- Flash Messages -->
    @if (session('accepted'))
        <div class="alert alert-success alert-dismissible fade show sticky-alert" role="alert" id="flashMessage">
            {{ session('accepted') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('declined'))
        <div class="alert alert-warning alert-dismissible fade show sticky-alert" role="alert" id="flashMessage">
            {{ session('declined') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show sticky-alert" role="alert" id="flashMessage">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (auth()->user()->isFarmer())
        <section class="section farms">
            <div class="position-fixed d-flex flex-column align-items-center justify-content-end rounded-3" 
                    style="top:6%; right:0; left:0; z-index: 995; height: 120px; width: 100%; background-color: #134125;">
                <h1 class="fs-3 mt-3 text-white"><strong>My Farm</strong></h1>
                <div class="d-flex flex-start px-4 mb-3 align-self-start">
                    <button type="button" class="btn btn-outline-success rounded rounded-3 text-white border-white" data-bs-toggle="modal" data-bs-target="#addFarmModal">
                        <strong>Add Farm</strong>
                    </button>
                </div>
            </div>
            <div style="margin-top: 130px;"></div>
            @foreach($farms as $farm)
                <div class="card mb-2">
                    <div class="card-body" style="max-height: 100vh; overflow-y: auto;">
                        <h5 class="card-title p-0">
                            <strong>{{ $farm->name }}</strong> 
                        </h5>
                        <hr>
                        <div class="card-text text-muted">
                            <p class="d-flex flex-row mb-0 pb-0"> <span class="fw-bold me-2">Address:</span>  {{ $farm->address }}</p>
                            <p class="d-flex flex-row mb-0 pb-0">  <span class="fw-bold me-2">Variety:</span>   {{ $farm->variety }}</p>
                            <p class="d-flex flex-row mb-0 pb-0"> <span class="fw-bold me-2">Hectares:</span>   {{ $farm->hectares }}</p>
                            <p class="d-flex flex-row mb-0 pb-0">  <span class="fw-bold me-2">Tree Age:</span>   {{ $farm->tree_age }}</p>
                            <p class="d-flex flex-row mb-0 pb-0">   <span class=" fw-bold me-2">Planted Coconut:</span> {{ $farm->planted_coconut }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
            @include('user.others.add_farm') 
        </section>
    @endif

    @if (auth()->user()->isTechnician())
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
                                        <h5 class="mb-1 fw-bold">New Farm Assigned you to Visit</h5>
                                        <p class="mb-1">
                                            <strong>Assigned ID:</strong> {{ $notifs->request_id }}<br>
                                            <strong>Date/Time:</strong> {{ $notifs->created_at->format('F d, Y | h:i A') }}
                                        </p>
                                    </div>
                                    <div class="mt-3 align-self-end">
                                        <a href="{{ route('user.viewDetails', $notifs->id) }}" 
                                            class="btn fw-bold {{ $notifs->status === 'Acknowledged' || $notifs->status === 'Verified' ? 'btn-outline-secondary disabled' : 'btn-outline-success'}}">
                                           {{ $notifs->status === 'Acknowledged' || $notifs->status === 'Verified' ? $notifs->status : 'View Details' }} 
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>  
            @endif
        </section>
    @endif
</main>
@include('user.partials.__footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flashMessage');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.classList.remove('show');
                flashMessage.style.animation = 'slideOutToRight 0.5s ease-in-out forwards';  
            }, 3000); 
        }
    });
</script>



