@include('user.partials.__header')
@include('user.partials.__nav')
<main id="main" class="main">
    <section class="section services">
        <div class="d-flex flex-column" style="height:85vh;">
           <div class="text-center d-flex pb-3">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary align-self-start px-4">
                    <i class="fas fa-arrow-left w-4"></i> 
                </a>
            </div>
            <div class="card shadow-sm p-3" style="border: 1px solid #188754; border-radius: 8px; background-color: #f9f9f9;">
                <h5 class="card-title text-center pt-0" style="font-weight: bold;">Schedule Details</h5>
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
                    <textarea name="note" class="form-control text-muted" rows="4" disabled>Hello!, It's your Technician from PCA. I've set an schedule for visiting your farm at {{ \Carbon\Carbon::parse($requestDetails->visit_date)->format('F d, Y | h:i A') }}. Please confirm your availability.
                    </textarea>
                </div>
                <div class="mb-3">
                    <label for="schedule_visit"><strong>Technician's Schedule of Visit :</strong> <span class="text-muted fw-bold" style="font-size: 13px;">( Reschedule for your availability or Confirm this scheduled set by your technician ? )</span></label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="visit_date" id="visit_date"
                                   value="{{ \Carbon\Carbon::parse($requestDetails->visit_date)->format('Y-m-d') }}"
                                   required {{ $requestDetails->status === 'Confirmed by Farmer' ? 'disabled' : '' }}>
                        </div>
                        <div class="col-md-6">
                            <input type="time" class="form-control" name="visit_time" id="visit_time"
                                   value="{{ \Carbon\Carbon::parse($requestDetails->visit_date)->format('H:i') }}"
                                   required {{ $requestDetails->status === 'Confirmed by Farmer' ? 'disabled' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-5">
                    <button type="button" id="confirmProceedBtn" class="btn {{ $requestDetails->status === 'Confirmed by Farmer'  
                            ? 'btn-success' : 'btn-outline-success' }} fw-bold px-3 w-35 btn-block rounded-pill" 
                            data-status="Confirmed"
                            data-message="Schedule of farm visitation was Confirmed by Farmer"
                            data-bs-toggle="modal" 
                            data-bs-target="#actionModal">
                        <i class="bi bi-check-circle"></i> 
                        <span id="confirmProceedBtnText">
                            {{ $requestDetails->status === 'Confirmed' ? 'Confirmed' : 'Confirm & Proceed' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
        @include('user.others.request_action')
    </section>
</main>
@include('user.partials.__footer')

<script>
    
    document.addEventListener('DOMContentLoaded', function () {
        const visitDate = document.getElementById('visit_date');
        const visitTime = document.getElementById('visit_time');
        const btnText = document.getElementById('confirmProceedBtnText');
        const originalDate = "{{ \Carbon\Carbon::parse($requestDetails->visit_date)->format('Y-m-d') }}";
        const originalTime = "{{ \Carbon\Carbon::parse($requestDetails->visit_date)->format('H:i') }}";
        let isRescheduled = false;

        function updateButtonText() {
            if (
                visitDate.value !== originalDate ||
                visitTime.value !== originalTime
            ) {
                btnText.textContent = 'Reschedule & Proceed';
                isRescheduled = true;
            } else {
                btnText.textContent = 'Confirm & Proceed';
                isRescheduled = false;
            }
        }

        visitDate && visitDate.addEventListener('change', updateButtonText);
        visitTime && visitTime.addEventListener('change', updateButtonText);


        $('#actionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);  
            var status = button.data('status');   
            var message = button.data('message'); 
            var modal = $(this);
            if (!isRescheduled) {
                  modal.find('.modal-title').html('<strong>Confirm</strong>');
            }else{
                modal.find('.modal-title').html('<strong>Reschedule</strong>');
            }
            modal.find('#actionModalBody').html(
                `Yes i want to <strong>${btnText.textContent}</strong>.`
            );
            var requestId = "{{ $requestDetails->id }}"; 
            var formAction = `/user/farmer/notifications/${requestId}/${status}/${message}/request-details`;
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
    });
</script>
