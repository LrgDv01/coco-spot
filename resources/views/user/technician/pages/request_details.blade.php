@include('user.partials.__header')
@include('user.partials.__nav')
<main id="main" class="main">
    <section class="section services">
        <div class="d-flex flex-column justify-content-between" style="height:85vh;">
            <div class="card shadow-sm p-3" style="border: 1px solid #188754; border-radius: 8px; background-color: #f9f9f9;">
                <h5 class="card-title text-center pt-0" style="font-weight: bold;">Assigned Farm Details</h5>
                {{-- dd($requestDetails->status); --}}
                <div class="mb-2">
                    <strong>Report ID : </strong> {{ $requestDetails->request_id }}
                </div>
                <div class="mb-2">
                    <strong>Date/Time : </strong> {{ \Carbon\Carbon::parse($requestDetails->created_at)->format('F d, Y | h:i A') }}
                </div>
                <div class="mb-2">
                    <strong>Farmer Name : </strong> {{ $requestDetails->farmer_name }} 
                </div>
                <div class="mb-2">
                    <strong>Contact No. : </strong> {{ $requestDetails->user->contact }} 
                </div>
                <div class="mb-2">
                    <strong>Farm Location : </strong><i class="bi bi-geo-alt-fill" style="color: red;"></i> {{ $requestDetails->farmerReports->farm_location }} 
                </div>
                <div class="mb-2">
                    <strong>Detected Disease : </strong> {{ $requestDetails->farmerReports->disease }}
                </div>
                <div class="mb-3">
                    <strong>Admin's Instructions : </strong>
                    <textarea name="note" class="form-control" rows="2" placeholder="Visit this farm for actual confirmation with the reported disease" disabled></textarea>
                </div>
                <div class="d-flex justify-content-center gap-5">
                    <button type="button" class="btn {{ $requestDetails->status === 'Acknowledged'  
                            ? 'btn-success' : 'btn-outline-success' }} fw-bold px-3 w-35 btn-block rounded-pill" 
                            data-status="Acknowledged"
                            data-message="Acknowledged"
                            data-bs-toggle="modal" 
                            data-bs-target="#actionModal">
                        <i class="bi bi-check-circle"></i> {{ $requestDetails->status === 'Acknowledged'  
                            ? 'Acknowledged' : 'Acknowledge & Proceed' }}
                    </button>
                </div>
            </div>
              <!-- Back Button -->
            <div class="text-center d-flex">
                <a href="{{ route('user.notifications') }}" class="btn btn-outline-secondary align-self-start px-4">
                    <i class="fas fa-arrow-left w-4"></i> 
                </a>
            </div>
        </div>
        @include('user.technician.others.request_action')
    </section>
</main>
@include('user.partials.__footer')

<script>
   $('#actionModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);  
        var status = button.data('status');   
        var message = button.data('message'); 
        var modal = $(this);
        modal.find('.modal-title').html('<strong>Confirm</strong>');
        modal.find('#actionModalBody').html(
            `Yes i <strong>${message}</strong> this report and visit
            <strong>{{ $requestDetails->farmer_name }}</strong> farm.`
        );
        var requestId = "{{ $requestDetails->id }}"; 
        var formAction = `/user/notifications/${requestId}/${status}/request-details`;
        modal.find('#actionForm').attr('action', formAction);
        var buttonClass = 'btn-success';
        var buttonIcon = 'bi-check-circle';
        modal.find('#actionSubmitBtn')
            .removeClass('btn-outline-success btn-danger')
            .addClass(buttonClass);
        modal.find('#actionSubmitBtn i')
            .removeClass('bi-check-circle bi-x-circle')
            .addClass(buttonIcon);
    });
</script>
