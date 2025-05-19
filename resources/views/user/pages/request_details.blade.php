@include('user.partials.__header')
@include('user.partials.__nav')
<main id="main" class="main">
    <section class="section services">
        <div class="d-flex flex-column justify-content-between" style="height:85vh;">
            <div class="card shadow-sm p-3" style="border: 1px solid #188754; border-radius: 8px; background-color: #f9f9f9;">
                <h5 class="card-title text-center pt-0" style="font-weight: bold;">Schedule Details</h5>
                {{-- dd($requestDetails->status); --}}
                <div class="mb-2">
                    <strong>Your Report ID : </strong> {{ $requestDetails->request_id }}
                </div>
                <div class="mb-2">
                    <strong>Date/Time : </strong> {{ \Carbon\Carbon::parse($requestDetails->created_at)->format('F d, Y | h:i A') }}
                </div>
                <div class="mb-2">
                    <strong>Variety : </strong> {{ $requestDetails->userFarms->variety }} 
                </div>
                <div class="mb-2">
                    <strong>Tree Age : </strong> {{ $requestDetails->userFarms->tree_age }} 
                </div>
                <div class="mb-2">
                    <strong>Planted Coconut : </strong> {{ $requestDetails->userFarms->planted_coconut }} 
                </div>
                <div class="mb-2">
                    <strong>Farm Location : </strong><i class="bi bi-geo-alt-fill" style="color: red;"></i> {{ $requestDetails->farmerReports->farm_location }} 
                </div>
                <div class="mb-3">
                    <strong>Technician's Initial Message : </strong>
                    <textarea name="note" class="form-control" rows="4" 
                        placeholder="Hello!, It's your Technician from PCA. I've set an schedule for visiting your farm at {{ \Carbon\Carbon::parse($requestDetails->updated_at)->format('F d, Y | h:i A') }}. Please confirm your availability." 
                        disabled></textarea>
                </div>
                <div class="mb-3">
                    <strong>Schedule Visit :</strong>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="visit_date" id="visit_date"
                                   value="{{ \Carbon\Carbon::parse($requestDetails->updated_at)->format('Y-m-d') }}"
                                   required {{ $requestDetails->status === 'Acknowledged' ? 'disabled' : '' }}>
                        </div>
                        <div class="col-md-6">
                            <input type="time" class="form-control" name="visit_time" id="visit_time"
                                   value="{{ \Carbon\Carbon::parse($requestDetails->updated_at)->format('H:i') }}"
                                   required {{ $requestDetails->status === 'Acknowledged' ? 'disabled' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-5">
                    <button type="button" class="btn {{ $requestDetails->status === 'Acknowledged'  
                            ? 'btn-success' : 'btn-outline-success' }} fw-bold px-3 w-35 btn-block rounded-pill" 
                            data-status="Confirmed"
                            data-message="Confirmed"
                            data-bs-toggle="modal" 
                            data-bs-target="#actionModal">
                        <i class="bi bi-check-circle"></i> {{ $requestDetails->status === 'Confirmed'  
                            ? 'Confirmed' : 'Confirm & Proceed' }}
                    </button>
                </div>
            </div>
              <!-- Back Button -->
            <div class="text-center d-flex">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary align-self-start px-4">
                    <i class="fas fa-arrow-left w-4"></i> 
                </a>
            </div>
        </div>
        @include('user.others.request_action')
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
            `Yes i want to <strong>Confirm</strong> this schedule visit from technician.`
        );
        var requestId = "{{ $requestDetails->id }}"; 
        var formAction = `/user/farmer/notifications/${requestId}/${status}/request-details`;
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
