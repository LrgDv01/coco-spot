 
@include('admin.partials.__header')
@include('admin.partials.__nav')

<main id="main" class="main">
    <div class="pagetitle mb-3">
        <h1 class="fs-3"><strong>Reports</strong></h1>
    </div>
    <section class="section report-list">
        <div class="container mt-2 mb-5">
    <header class="mb-4 pt-2">
        <h1 class="display-6 fw-bold text-muted">Disease Reports</h1>
        <p class="text-muted">Filter and view reports submitted.</p>
    </header>

    {{-- Filter Form --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body bg-light p-4">
            <form method="GET" action="{{ url()->current() }}">
                <div class="row g-3">
                    {{-- Date Range Filter --}}
                    <div class="col-md-6 col-lg-3">
                        <label for="date_from" class="form-label fw-semibold">Date From:</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label for="date_to" class="form-label fw-semibold">Date To:</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                    </div>

                    {{-- Disease Filter --}}
                    <div class="col-md-6 col-lg-3">
                        <label for="disease_filter" class="form-label fw-semibold">Disease:</label>
                        <select name="disease_filter" id="disease_filter" class="form-select form-select-sm">
                            <option value="">All Diseases</option>
                            <option value="Lethal Yellowing" {{ request('disease_filter') == 'Lethal Yellowing' ? 'selected' : '' }}>Lethal Yellowing</option>
                            <option value="Stem Bleeding" {{ request('disease_filter') == 'Stem Bleeding' ? 'selected' : '' }}>Stem Bleeding</option>
                            <option value="Leaf Spot" {{ request('disease_filter') == 'Leaf Spot' ? 'selected' : '' }}>Leaf Spot</option>
                            {{-- Add more diseases as needed --}}
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-md-6 col-lg-3">
                        <label for="status_filter" class="form-label fw-semibold">Status:</label>
                        <select name="status_filter" id="status_filter" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="Reported" {{ request('status_filter') == 'Reported' ? 'selected' : '' }}>Reported</option>
                            <option value="Verified" {{ request('status_filter') == 'Verified' ? 'selected' : '' }}>Verified</option>
                            <option value="Treated" {{ request('status_filter') == 'Treated' ? 'selected' : '' }}>Treated</option>
                            <option value="UnVerified" {{ request('status_filter') == 'UnVerified' ? 'selected' : '' }}>UnVerified</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end align-items-center">
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle me-1" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                        Clear
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel-fill me-1" viewBox="0 0 16 16">
                            <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2z"/>
                        </svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reports Table --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0"> {{-- Added table-striped, table-hover for better visuals --}}
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="small text-uppercase">ID</th>
                        <th scope="col" class="small text-uppercase">Farmer Name</th>
                        <th scope="col" class="small text-uppercase">Disease</th>
                        <th scope="col" class="small text-uppercase">Assigned Technician</th>
                        <th scope="col" class="small text-uppercase">Status (Tech)</th>
                        <th scope="col" class="small text-uppercase">Remarks (Report)</th>
                        <th scope="col" class="small text-uppercase">Date Reported</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td class="align-middle">{{ $report->id }}</td>
                            <td class="align-middle">{{ $report->farmer_name }}</td>
                            <td class="align-middle">
                                {{ $report->farmerReports->disease ?? $report->technicianReports->diagnosis_disease ?? 'N/A' }}
                            </td>
                            <td class="align-middle">
                                {{ $report->technicians->full_name ?? 'N/A' }}
                            </td>
                            <td class="align-middle">
                                @php
                                    $status = $report->technicianReports->status ?? 'UnVerified';
                                    $statusClass = '';
                                    if (strtolower($status) === 'verified') $statusClass = 'bg-success';
                                    elseif (strtolower($status) === 'treated') $statusClass = 'bg-primary';
                                    elseif (strtolower($status) === 'reported') $statusClass = 'bg-warning text-dark';
                                    else $statusClass = 'bg-secondary'; // UnVerified or other
                                @endphp
                                <span class="badge rounded-pill {{ $statusClass }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="align-middle">
                                {{ 'Report ' . ($report->status ?? 'N/A') }}
                            </td>
                            <td class="align-middle small">
                                {{ $report->created_at instanceof \Carbon\Carbon ? $report->created_at->format('M d, Y H:i A') : $report->created_at }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-table text-muted mb-2" viewBox="0 0 16 16">
                                  <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"/>
                                </svg>
                                <p class="fw-bold mb-1">No reports found.</p>
                                <p class="text-muted small">Try adjusting your filters or check back later.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Links --}}
    @if ($reports instanceof \Illuminate\Pagination\LengthAwarePaginator && $reports->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $reports->appends(request()->query())->links() }} {{-- Ensure Bootstrap pagination is default in AppServiceProvider --}}
    </div>
    @endif

</div>
       
    </section>
</main>

@include('admin.partials.__footer')