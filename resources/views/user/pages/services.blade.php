@include('user.partials.__header')
@include('user.partials.__nav')
<main id="main" class="main">
    <section class="section services">
        <div class="container">
            <div class="position-fixed bg-white text-center" 
                style="top:6%; right:0; left:0; z-index: 995; width: 100%;">
                <h2 class="py-2 mt-2">Coconut Technicians</h2>
            </div>
            <div style="margin-top: 50px;">
            </div>
            <div id="technician-list">
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="rounded border-0" onclick="refresh()">
                        <i class="bi bi-arrow-repeat"></i></button>
                </div>
                <ul class="list-unstyled">
                    @foreach($technicians as $technician) 
                        @php
                            if (in_array($technician->id, $hirebySomeone)) {
                                    $buttonEnabled = 'disabled';
                                    $buttonClass = 'btn-outline-primary active';
                                    $buttonText = "Already hired";
                            }
                            else {
                                if($hiredTechnicians->isEmpty()){
                                        $buttonEnabled = '';
                                        $buttonClass = 'btn-outline-success active';
                                        $buttonText = 'Hire me';
                                } else {
                                    foreach($hiredTechnicians as $hired) {
                                        $isHired = $technician->id === $hired->technician_id ? $hired->status : 'Hire me';
                                        $status =  $isHired === 'pending' || $isHired === 'accepted';
                                        $buttonClass = $isHired === 'pending' ? 'btn-outline-secondary active' 
                                            : ($isHired === 'accepted' ? 'btn-outline-success active' 
                                            : 'btn-outline-success active');
                                        $buttonEnabled = $status ? 'disabled' : '';
                                        $buttonText = $isHired === 'accepted' ? 'Hired by you' 
                                            : ($isHired === 'pending' ? 'Service Requested' 
                                            : ($isHired === 'end' ? 'Hire me' 
                                            : $isHired));
                                    }   
                                }
                            }
                        @endphp
                        <li class="card mb-2">
                            <div class="card-body">
                                <div>
                                    <h5 class="card-title p-0">{{ $technician->full_name }}</h5>
                                    <hr>
                                    <p class="card-text text-truncate">
                                        <strong>Address :</strong> {{ $technician->address }}<br>
                                        <strong>Phone # :</strong> {{ $technician->contact }}<br>
                                    </p>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <button type="button" id="btn-hire"
                                        data-bs-toggle="modal"
                                        data-bs-target="#hireTechnicianModal"
                                        data-id="{{ $technician->id }}"
                                        data-name="{{ $technician->full_name }}"
                                        class="btn {{ $buttonClass }} btn-sm rounded rounded-3"
                                        {{ $buttonEnabled }}>
                                   <strong>{{ $buttonText }}</strong> 
                                </button>
                            <button type="button" class="btn btn-info btn-sm rounded rounded-3" 
                                        data-bs-toggle="modal" data-bs-target="#viewMoreInfoModal" 
                                        data-id="{{ $technician->id }}">
                                    <i class="bi bi-person-circle"></i> <strong>View More Info</strong>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @include('user.others.hire_technician')
        @include('user.others.user_info')
    </section>
</main>
@include('user.partials.__footer')

<script>
    function refresh() {
        location.reload();
    }
</script>