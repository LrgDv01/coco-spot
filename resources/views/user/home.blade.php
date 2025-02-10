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
        <section class="section farm">
            <div class="container p-0 m-0">
                <div class="position-fixed bg-white text-center" 
                    style="top:7%; right:0; left:0; z-index: 995; width: 100%;">
                    <h2 class="py-2 mt-2">My Farm</h2>
                    <div class="d-flex flex-start px-4 mb-3">
                        <button type="button" class="btn btn-success rounded rounded-3" data-bs-toggle="modal" data-bs-target="#addFarmModal">
                            <strong>Add Farm</strong>
                        </button>
                    </div>
                </div>
                <div style="margin-top: 100px;">
                </div>
                <div id="farm-list">
                    @foreach($farms as $farm)
                        <div class="card mb-2 p-0">
                            <div class="card-body">
                                <h5 class="card-title p-0 d-flex flex-row justify-content-between">
                                    <strong>{{ $farm->name }}</strong> 
                                    <div class="d-flex justify-content-end" style="width:50%;">
                                        <div class="{{ $farm->condition === 'is Healthy' ? 'bg-success' : 'bg-danger' }} 
                                            align-self-center" style="height:0.5px; width:50%;"> </div> 
                                        <span class="{{ $farm->condition === 'is Healthy' ? 'text-success' : 'text-danger' }}" style="font-size: 70%;">
                                            ( <strong>{{ $farm->condition }}</strong> 
                                        </span>
                                    </div>
                            
                                </h5>
                                <hr>
                                <p class="card-text text-truncate">
                                    <strong>Address :</strong> {{ $farm->location }}<br>
                                    <strong>Variety :</strong> {{ $farm->variety }}<br>
                                    <strong>Hectares :</strong> {{ $farm->hectares }}<br>
                                    <strong>Tree Age :</strong> {{ $farm->tree_age }}<br>
                                    <strong>Planted Coconut :</strong> {{ $farm->planted_coconut }}<br>
                                </p>
                            </div>
                            <div class="card-footer d-flex flex-wrap {{ $farm->condition === 'is Healthy' ? 'justify-content-end' : 'justify-content-between' }}">
                                <a href="{{ route('user.services') }}" class="btn btn-success {{ $farm->condition === 'is Healthy' ? 'd-none' : '' }}">
                                   <strong>Hire a Technician</strong> 
                                </a>
                                <button 
                                    type="button" 
                                    data-bs-toggle="modal" 
                                    class="btn btn-primary"
                                    data-bs-target="#updateFarmCondition{{ $farm->id }}">
                                    <strong>Update Condition</strong>
                                </button>
                            </div>  
                        </div>
                        @include('user.others.update_condition', [
                            'farmId' => $farm->id, 
                            'farmCondition' => $farm->condition])
                    @endforeach
                </div>
            </div>
            @include('user.others.add_farm') 
        </section>
    @endif
    @if (auth()->user()->isTechnician())
        <section class="section notifications">
            <div class="container">
                @if($notifications->isEmpty())
                    <p class="text-center">No notifications available at the moment.</p>
                @else
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white sticky-top" style="z-index: 1;">
                            <h4>Notifications</h4>
                        </div>
                        <div class="card-body text-truncate p-0" style="max-height: 80vh; overflow-y: auto;">
                            <ul class="list-group">
                                @foreach ($notifications as $notifs)
                                    <li class="list-group-item d-flex flex-column align-items-start shadow-sm p-3">
                                        <div class="mb-2">
                                            <h5 class="mb-1">New Service Request from Farmer</h5>
                                            <p class="mb-1">
                                                <strong>Request ID:</strong> {{ $notifs->request_id }}<br>
                                                <strong>Date/Time:</strong> {{ $notifs->created_at->format('F d, Y | h:i A') }}
                                            </p>
                                        </div>
                                        <div class="mt-3 align-self-end">
                                            <a href="{{ route('user.viewDetails', $notifs->id) }}" class="btn btn-success">
                                                View Details
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>  
                @endif
            </div>
        </section>
    @endif
</main>
@include('user.partials.__footer')

@if (auth()->user()->isFarmer())
    <script>

        function updateCondition(event, farmId) {
            try {
                const modalButton = event.target;
                const modalId = modalButton.getAttribute('data-bs-target');
                const modal = document.querySelector(modalId);
                if (!modal) {
                    console.error(`Modal with ID ${modalId} not found.`);
                    return;
                }
                const modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            } catch (error) {
                console.error("An error occurred while showing the modal:", error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const updateFarmConditionModals = document.querySelectorAll('.modal[id^="updateFarmCondition"]');
            updateFarmConditionModals.forEach(modal => {
                modal.addEventListener('shown.bs.modal', function () {
                    const checkbox = modal.querySelector('.custom-checkbox');
                    const label = modal.querySelector(`[id^="conditionLabel_"]`);
                    if (checkbox) {
                        checkbox.focus(); 
                    }
                    function updateCheckboxShadow() {
                        if (checkbox.value === 'is Infected') {
                            checkbox.classList.add('shadow-red');
                            checkbox.classList.remove('shadow-green');
                        } else if (checkbox.value === 'is Healthy') {
                            checkbox.classList.add('shadow-green');
                            checkbox.classList.remove('shadow-red');
                        }
                    }
                    updateCheckboxShadow();
                });
            });

            $('.update-btn').prop('disabled', true);
            $('.custom-checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).addClass('shadow-none');
                    if ($(this).val() === 'is Healthy') {
                        $(this).addClass('background-green');
                        $(this).removeClass('shadow-green');
                    } 
                    else if ($(this).val() === 'is Infected'){
                        $(this).addClass('background-red');
                        $(this).removeClass('shadow-red');
                    }
                    $('.update-btn').prop('disabled', false);
                }
                else {
                    $(this).removeClass('shadow-none'); 
                    if ($(this).val() === 'is Healthy') {
                        $(this).addClass('shadow-green');
                        $(this).removeClass('background-green');
                    }
                    else if ($(this).val() === 'is Infected') {
                        $(this).addClass('shadow-red');
                        $(this).removeClass('background-red');
                    }
                    $('.update-btn').prop('disabled', true);
                }
            });
        });
    </script>
@endif

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
