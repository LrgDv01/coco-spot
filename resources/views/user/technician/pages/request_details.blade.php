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
                <form id="mainRequestForm" method="POST" action=""> @csrf <h5 class="card-title text-center pt-0" style="font-weight: bold;">Assigned Farm Details</h5>
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
                        <textarea name="note" class="form-control text-muted" rows="2" placeholder="Visit this farm for actual confirmation of detected disease." disabled></textarea>
                    </div>
                    <div class="mb-3">
                        <strong> {{ $requestDetails->status === 'Unverified' ? 'Schedule of Visit :' : 'Set your Schedule of Visit :' }} </strong>
                        <div class="row g-2"> 
                            <div class="col-md-6">
                                {{--      <input type="date" class="form-control" name="visit_date" id="visit_date"
                                       value="{{ old('visit_date', $requestDetails->visit_date 
                                            ?? $requestDetails->status === 'Unverified' ?  \Carbon\Carbon::parse($requestDetails->updated_at)->format('Y-m-d')) }}" 
                                       required {{ $requestDetails->status === 'Unverified' ? 'disabled' : '' }}> --}}
                               
                                <input type="date" class="form-control" name="visit_date" id="visit_date"
                                       value="{{ $requestDetails->status === 'Unverified' ? \Carbon\Carbon::parse($requestDetails->updated_at)->format('Y-m-d') : '' }}" 
                                       required {{ $requestDetails->status === 'Unverified' ? 'disabled' : '' }}>
                            </div>
                            <div class="col-md-6">
                              {{--   <input type="time" class="form-control" name="visit_time" id="visit_time"
                                       value="{{ old('visit_time', $requestDetails->visit_time ?? '') }}" 
                                       required {{ $requestDetails->status === 'Unverified' ? 'disabled' : '' }}> --}}
                                <input type="time" class="form-control" name="visit_time" id="visit_time"
                                      value="{{ $requestDetails->status === 'Unverified' ? \Carbon\Carbon::parse($requestDetails->updated_at)->format('H:i') : '' }}" 
                                       required {{ $requestDetails->status === 'Unverified' ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center gap-5">
                        {{--                          <button type="submit" class="btn {{ $requestDetails->status === 'Acknowledged' ? 'btn-success' : 'btn-outline-success' }} fw-bold px-3 w-35 btn-block rounded-pill main-action-button"> 
--}}
                        <button type="submit" 
                                class="btn {{ $requestDetails->status === 'Unverified' ? 'd-none' : 'btn-outline-success' }} 
                                    fw-bold px-3 w-35 btn-block rounded-pill main-action-button" 
                                data-status="Acknowledged"
                                data-message="Acknowledged and Scheduled by Technician">
                            <i class="bi bi-check-circle"></i> {{ $requestDetails->status === 'Acknowledged' ? 'Acknowledged' : 'Acknowledge & Proceed' }}
                        </button>
                    </div>
                </form> </div>
        </div>
        @include('user.technician.others.request_action')
    </section>
</main>
@include('user.partials.__footer')

<script>
    $(document).ready(function() {
        var actionModalElement = document.getElementById('actionModal');
        if (!actionModalElement) {
            console.error('Modal with ID #actionModal not found!');
            return;
        }
        var actionModalInstance = new bootstrap.Modal(actionModalElement);
        var mainRequestFormElement = document.getElementById('mainRequestForm');
        if (!mainRequestFormElement) {
            console.error('Form with ID #mainRequestForm not found!');
        }
        $('.main-action-button').on('click', function(event) {
            event.preventDefault(); 
            var visitDateInput = $('#visit_date');
            if (mainRequestFormElement && !visitDateInput.is(':disabled')) {
                if (!mainRequestFormElement.reportValidity()) {
                    return;
                }
            }
            actionModalInstance.show(this);
        });
        $('#actionModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            if (!button.length && event.relatedTarget) { 
                button = $(event.relatedTarget);
            }
            if (!button.length) {
                console.warn("Modal's relatedTarget is not set or invalid. Attempting to use .main-action-button as fallback.");
                button = $('.main-action-button').first(); 
                if (!button.length) {
                    console.error("Fallback .main-action-button not found. Modal setup might fail.");
                    return; 
                }
            }
            var status = button.data('status');
            var message = button.data('message');
            if (typeof status === 'undefined') status = 'Acknowledged'; 
            if (typeof message === 'undefined') message = 'Acknowledged by Technician';
            var modal = $(this);
            modal.find('.modal-title').html('<strong>Confirm ?</strong>');
            modal.find('#actionModalBody').html(
                `Yes I <strong>${status}</strong> this report and visit
                <strong>{{ $requestDetails->farmer_name }}</strong> farm.`
            );
            var requestId = "{{ $requestDetails->id }}";
            var formAction = `/user/notifications/${requestId}/${status}/${message}/request-details`;
            modal.find('#actionForm').attr('action', formAction);
            var buttonClass = 'btn-success';
            var buttonIcon = 'bi-check-circle';
            modal.find('#actionSubmitBtn')
                .removeClass('btn-outline-success btn-danger btn-warning') 
                .addClass(buttonClass);
            modal.find('#actionSubmitBtn i')
                .removeClass('bi-check-circle bi-x-circle bi-exclamation-triangle') 
                .addClass(buttonIcon);
        });

        $('#actionForm').on('submit', function(event) {
            event.preventDefault();
            var modalForm = $(this);
            var url = modalForm.attr('action');
            var data = $('#mainRequestForm').serialize(); 
            var submitButton = modalForm.find('#actionSubmitBtn');
            var originalButtonText = submitButton.html();

            $.ajax({
                method: 'POST',
                url: url,
                data: data,
                beforeSend: function() {
                    submitButton.prop('disabled', true).html('Processing...');
                },
                success: function(response) {
                    actionModalInstance.hide();
                    $('#successMessage').text(response.message || 'Action completed successfully.');
                    if (response.new_status === 'Acknowledged by Technician') {
                        $('#visit_date').prop('disabled', true);
                        $('#visit_time').prop('disabled', true);
                        $('.main-action-button')
                            .removeClass('btn-outline-success')
                            .addClass('btn-success')
                            .html('<i class="bi bi-check-circle"></i> Acknowledged by Technician')
                            .data('status', 'Acknowledged by Technician');
                    }
                    var successModalEl = document.getElementById('successModal');
                    if (successModalEl) {
                        var successModalInstance = bootstrap.Modal.getInstance(successModalEl) || new bootstrap.Modal(successModalEl);
                        successModalInstance.show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('An error occurred:', error);
                    console.error('Response:', xhr.responseText);
                    let errorMsg = 'An error occurred. Please try again.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.errors) {
                            let errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            errorMsg += "\nDetails:\n" + errors;
                        }
                    }
                    alert(errorMsg); 
                    actionModalInstance.hide();
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        });
    });
</script>