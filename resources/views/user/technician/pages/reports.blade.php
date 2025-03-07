@include('user.partials.__header')
@include('user.partials.__nav')

<style>
    .is-invalid {
        border: 2px solid #dc3545;
        outline: none;
    }
    .card-custom {
        max-height: 80vh;
        overflow-y: auto;
        background-color: #f0f8f0;
        border: 1px solid #28a745;
        border-radius: 0.5rem;
    }
    .info-section {
        padding: 1rem;
    }
    .form-section {
        padding: 1rem;
    }
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
    }
</style>

<main id="main" class="main">
    <section class="section reports">
        <div class="container p-0">
            @if(!$report)
                <div class="alert alert-info text-center" role="alert">
                    You have no new reports to submit
                </div>
            @else
                <div class="row g-3">
                    <!-- Farm Information Card -->
                    <div class="col-lg-6">
                        <div class="card card-custom">
                            <div class="card-body info-section">
                                <p class="text-end mb-1 text-muted">Request ID: {{ $report->request_id }}</p>
                                <h6 class="card-title"><strong>Farmer Details</strong></h6>
                                <div class="info-section ps-2">
                                    <p><strong>Name:</strong> {{ $report->farmer_name }}</p>
                                    <p><strong>Contact:</strong> {{ $report->user->contact }}</p>
                                    <p><strong>Recipient:</strong> {{ $recipient }}</p>
                                </div>
                                <h6 class="card-title"><strong>Farm Details</strong></h6>
                                <div class="info-section ps-2">
                                    <p><strong>Location:</strong> {{ $report->userFarms->address }}</p>
                                    <p><strong>Size:</strong> {{ $report->userFarms->hectares }} hectares</p>
                                    <p><strong>Coconut Trees:</strong> {{ $report->userFarms->planted_coconut }}</p>
                                    <p><strong>Variety:</strong> {{ $report->userFarms->variety }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Form Card -->
                    <div class="col-lg-6">
                        <div class="card card-custom">
                            <form action="{{ route('user.technician-report') }}" 
                                  method="POST" 
                                  id="submitForm" 
                                  enctype="multipart/form-data">
                                @csrf
                                <!-- Hidden Inputs -->
                                @foreach([
                                    'farm_id' => $report->userFarms->id,
                                    'farmer_name' => $report->farmer_name,
                                    'contact_info' => $report->user->contact,
                                    'recipient' => $recipient,
                                    'farm_location' => $report->userFarms->address,
                                    'farm_size' => $report->userFarms->hectares,
                                    'coconut_trees' => $report->userFarms->planted_coconut,
                                    'coconut_variety' => $report->userFarms->variety
                                ] as $name => $value)
                                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                                @endforeach

                                <div class="card-body form-section">
                                    <div class="mb-3">
                                        <label class="form-label">Actual Disease Diagnosis:</label>
                                        <select name="diagnosis_disease" 
                                                id="diagnosis_disease" 
                                                class="form-select @error('diagnosis_disease') is-invalid @enderror" 
                                                required>
                                            <option value="" selected disabled>Select Disease</option>
                                            @foreach(['Stem Bleeding', 'Yellowing', 'Leaf Spot'] as $disease)
                                                <option value="{{ $disease }}">{{ $disease }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select a disease diagnosis</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Treatment Recommendation:</label>
                                        <textarea name="treatment_recommendation" 
                                                id="treatment_recommendation"
                                                class="form-control @error('treatment_recommendation') is-invalid @enderror" 
                                                rows="3" 
                                                placeholder="Your treatment recommendations..."
                                                required></textarea>
                                        <div class="invalid-feedback">Please provide treatment recommendations</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Upload Image as Proof:</label>
                                        <input type="file" 
                                               class="form-control @error('image_proof') is-invalid @enderror" 
                                               id="image_proof" 
                                               name="image_proof" 
                                               accept="image/*" 
                                               required>
                                        <div class="invalid-feedback">Please upload an image proof</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status Update:</label>
                                        <select name="status" 
                                                class="form-select @error('status') is-invalid @enderror" 
                                                required>
                                            <option value="" selected disabled>Select Status</option>
                                            @foreach(['Verified', 'False Detection'] as $status)
                                                <option value="{{ $status }}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select a status</div>
                                    </div>
                                </div>

                                <div class="card-footer text-center">
                                    <button type="submit" 
                                            class="btn btn-success px-4 rounded-pill" 
                                            id="submitButton">
                                        <i class="bi bi-check-circle me-2"></i>Submit Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @include('user.technician.others.submit_report')
            @endif
        </div>
    </section>
</main>

@include('user.partials.__footer')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('submitForm');
    const submitButton = document.getElementById('submitButton');
    
    submitButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Check all required fields
        const diagnosis = document.getElementById('diagnosis_disease');
        const treatment = document.getElementById('treatment_recommendation');
        const image = document.getElementById('image_proof');
        const status = document.querySelector('select[name="status"]');

        // Validate each field
        let isValid = true;
        
        if (!diagnosis.value || diagnosis.value === '') {
            diagnosis.classList.add('is-invalid');
            isValid = false;
        } else {
            diagnosis.classList.remove('is-invalid');
        }

        if (!treatment.value.trim()) {
            treatment.classList.add('is-invalid');
            isValid = false;
        } else {
            treatment.classList.remove('is-invalid');
        }

        if (!image.files.length) {
            image.classList.add('is-invalid');
            isValid = false;
        } else {
            image.classList.remove('is-invalid');
        }

        if (!status.value || status.value === '') {
            status.classList.add('is-invalid');
            isValid = false;
        } else {
            status.classList.remove('is-invalid');
        }

        // If all fields are valid, show confirmation modal
        if (isValid) {
            $('#actionModal').modal('show');
        }
    });
});

function submitReportForm() {
    const form = document.getElementById('submitForm');
    const formData = new FormData(form);

    fetch('{{ route('user.technician-report') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#actionModal').modal('hide');
            $('#successModal').modal('show');
            document.getElementById('successMessage').innerText = data.message || 'Report submitted successfully!';
        } else {
            $('#actionModal').modal('hide');
            alert('Error: ' + (data.message || 'Submission failed'));
        }
    })
    .catch(error => {
        $('#actionModal').modal('hide');
        console.error('Error:', error);
        alert('An error occurred while submitting the report.');
    });
}
</script>