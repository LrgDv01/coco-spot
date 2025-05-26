 
@include('admin.partials.__header')
@include('admin.partials.__nav')

<main id="main" class="main">
    <div class="pagetitle mb-5">
        <h1 class="fs-3"><strong>Reports</strong></h1>
    </div>
    <section class="section bhw-list">
        <div class="content">
            <div class="container-fluid">
                {{--           <div class="mb-3 {{ auth()->user() && auth()->user()->isSuperAdmin() ? '' : 'd-none' }}">
                    <a href="{{ route('admin.bhwregistration.index') }}" class="btn btn-success">Add BHW</a>
                </div>  --}}
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Farmer Name</th>
                            <th>Disease</th>
                            <th>Assigned Technician</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Date Reported</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->farmer_name }}</td>
                                <td>{{ $report->farmerReports->disease ?? $report->technicianReports->diagnosis_disease }}</td>
                                <td>{{ $report->technicians->full_name  }}</td>
                                <td>{{ $report->technicianReports->status ?? 'UnVerified' }}</td>
                                <td>{{ 'Report ' . $report->status  }}</td>
                                <td>{{ $report->created_at  }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No BHW users found.</td>
                            </tr>
                        @endforelse
                    </tbody> 
                </table>
            </div>
        </div>
    </section>
</main>

@include('admin.partials.__footer')