@include('user.partials.__header')
@include('user.partials.__nav')

<main id="main" class="main">
    <section class="section monitoring">
        <div class="position-fixed d-flex flex-column align-items-center justify-content-end rounded-3" 
                style="top:6%; right:0; left:0; z-index: 995; height: 70px; width: 100%; background-color: #134125;">
            <h1 class="fs-3 mt-3 text-white"><strong>Monitoring</strong></h1>
        </div>
        <div style="margin-top: 70px;"></div>
        @if($reports->isEmpty())
            <div class="alert alert-secondary text-center" role="alert">
                <p class="text-secondary text-muted"> - No reports to monitor at the moment -</p>
            </div>
        @else
            <div class="card bg-transparent border border-0 shadow-none">
                <div class="card-body text-truncate p-0" style="height: 100%; overflow-y: auto;">
                    <ul class="list-group">
                        @foreach ($reports as $report)
                            <li class="list-group-item d-flex flex-column align-items-start shadow-sm p-3 mb-2" 
                                style="border: 1px solid #188754; border-radius: 8px;">
                                <h5 class="card-title p-0 m-0">
                                    <strong>{{ $report->farms->name }}</strong> 
                                </h5>
                                <hr class="bg-secondary px-3 w-100">
                                <div class="card-text text-muted w-100">
                                    <p class="d-flex flex-row mb-0 pb-0">
                                        <span class="fw-bold me-2">Farmer Name:</span> {{ $report->farmer_name }}
                                    </p>
                                    <div class="mb-2 w-100">
                                        <span class="fw-bold me-2">Farmer Location:</span>
                                        <textarea class="form-control w-100" readonly style="resize: both;">{{ $report->farm_location}}</textarea>
                                    </div>
                                      <p class="d-flex flex-row mb-0 pb-0">
                                        <span class="fw-bold me-2">Date/Time:</span> {{ $report->created_at->format('F d, Y | h:i A') }}
                                    </p>
                                </div>
      
                                {{--             <div class="mt-3 align-self-end">
                                    <a href="{{ route('user.viewReportDetails', $report->id) }}" 
                                        class="btn fw-bold {{ $notifs->status === 'Acknowledged' || $notifs->status === 'Verified' ? 'btn-outline-secondary disabled' : 'btn-outline-success'}}">
                                    {{ $notifs->status === 'Acknowledged' || $notifs->status === 'Verified' ? $notifs->status : 'View Details' }} 
                                    </a>
                                </div> --}}
                                <div class="mt-3 align-self-end">
                                    <a href="{{ route('user.viewReportDetails', $report->services->id) }}" 
                                        class="btn fw-bold btn-outline-secondary">
                                        View Details
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>  
        @endif
    </section>
</main>
@include('user.partials.__footer')