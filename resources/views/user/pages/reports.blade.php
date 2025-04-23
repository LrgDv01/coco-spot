@include('user.partials.__header')
@include('user.partials.__nav')

<main id="main" class="main">
    <section class="section reports">
        <div class="position-fixed d-flex align-items-end justify-content-center rounded-3" 
             style="top:5%; right:0; left:0; z-index: 995; height: 80px; width: 100%; background-color: #134125;">
            <h2 class="py-2 mt-2 text-white"><strong>Reports</strong></h2>
        </div>
        <div style="margin-top: 70px;"></div>
        @foreach($farms as $farm)
            @php    
                $submitReport = in_array($farm->id, $farmReports);
                $verified = in_array($farm->id, $technicianReport);
            @endphp
            <div class="card mb-2 p-0" style="background-color: #f0f8f0; border: 1px solid #28a745;">
                <a href="{{ $submitReport ? route('user.submitted-report', $farm->id) : route('user.submit-report', $farm->id) }}" class="btn btn-outline-success" >
                    <div class="card-body text-start p-0 m-0">
                        <div class="d-flex flex-column justify-content-between align-items-center p-0 m-0">
                            <span class="text-dark p-1 px-2 rounded align-self-end {{ $submitReport ? '' : 'd-none'}}"
                                style="background-color:rgb(235, 187, 179); font-size: 0.8rem;">
                                <i class="fas fa-disease"></i><i class="fas fa-search"></i> {{ $verified ? 'Disease Verified |' : 'Disease Initially Identified'}} 
                                 <i class="fas fa-check {{ $verified ? '' : 'd-none'}}"></i></span>
                            <h5 class="card-title p-0 mt-2 align-self-start">
                                <strong>{{ $farm->name }}</strong> </h5>
                        </div>
                        <hr class="my-1 p-0">
                        <div class="card-text">
                            <p class="d-flex flex-row mb-0 pb-0"> <span class="fw-bold me-2">Address:</span>  {{ $farm->address }}</p>
                            <p class="d-flex flex-row mb-0 pb-0">  <span class="fw-bold me-2">Variety:</span>   {{ $farm->variety }}</p>
                            <p class="d-flex flex-row mb-0 pb-0"> <span class="fw-bold me-2">Hectares:</span>   {{ $farm->hectares }}</p>
                            <p class="d-flex flex-row mb-0 pb-0">  <span class="fw-bold me-2">Tree Age:</span>   {{ $farm->tree_age }}</p>
                            <p class="d-flex flex-row mb-0 pb-0">   <span class=" fw-bold me-2">Planted Coconut:</span> {{ $farm->planted_coconut }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </section>
</main>
@include('user.partials.__footer')


