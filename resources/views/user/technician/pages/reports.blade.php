@include('user.partials.__header')
@include('user.partials.__nav')

<style>
    .is-invalid {
        border: 1px solid #dc3545;
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

</style>

<main id="main" class="main">
    <section class="section reports">
        <div class="container p-0">
            @if(!$report)
                <div class="alert alert-secondary text-center" role="alert">
                    <p class="text-secondary">- No new report to submit -</p>
                </div>
            @else
                <div class="row g-3">
                    <!-- Farm Information Card -->
                    <div class="col-lg-6">
                        <div class="card card-custom">
                            <div class="card-body info-section">
                                <p class="text-end mb-1 text-muted fw-bold">Report ID: {{ $report->request_id }}</p>
                                <h6 class="card-title pb-0 mb-0"><strong>Farmer Details</strong></h6>
                                <div class="info-section pb-0 mb-0">
                                    <p><strong>Name:</strong> {{ $report->farmer_name }}</p>
                                    <p><strong>Contact:</strong> {{ $report->user->contact }}</p>
                                    <p><strong>Recipient:</strong> {{ $recipient }}</p>
                                </div>
                                <hr>
                                <h6 class="card-title pb-0 mb-0"><strong>Farm Details</strong></h6>
                                <div class="info-section pb-0 mb-0">
                                    <p><strong>Location:</strong> {{ $report->userFarms->address }}</p>
                                    <p><strong>Size:</strong> {{ $report->userFarms->hectares }} hectares</p>
                                    <p><strong>Coconut Trees:</strong> {{ $report->userFarms->planted_coconut }}</p>
                                      <p><strong>Variety:</strong> {{ $report->userFarms->variety }}</p>
                                    <p><strong>Tree Disease:</strong> {{ $report->farmerReports->disease }}</p>
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
                                @foreach([
                                    'farm_id' => $report->userFarms->id,
                                    'farmer_name' => $report->farmer_name,
                                    'contact_info' => $report->user->contact,
                                    'recipient' => $recipient,
                                    'farm_location' => $report->userFarms->address,
                                    'district' => $report->userFarms->district,
                                    'farm_size' => $report->userFarms->hectares,
                                    'coconut_trees' => $report->userFarms->planted_coconut,
                                    'coconut_variety' => $report->userFarms->variety
                                ] as $name => $value)
                                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                                @endforeach
                                <div class="card-body form-section">
                                    <div class="mb-3">
                                        <label class="form-label">Actual Disease Diagnosis :</label>
                                        <select name="diagnosis_disease" 
                                                id="diagnosis_disease" 
                                                class="form-select @error('diagnosis_disease') is-invalid @enderror" 
                                                required>
                                            <option value="" disabled selected hidden>Select Disease</option>
                                            @foreach(['Stem Bleeding', 'Lethal Yellowing', 'Leaf Spot'] as $disease)
                                                <option value="{{ $disease }}">{{ $disease }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select a disease diagnosis</div>
                                    </div>

                                    <div class="mb-3 alert alert-info">
                                        <label class="form-label">Treatment Recommendation :</label>
                                        <div id="treatment_recommendation" class="card-body pe-2 p-0 m-0" style="max-height: 200px; overflow-y: auto;">
                                        </div>
                                        {{--        <textarea name="treatment_recommendation" 
                                                id="treatment_recommendation"
                                                class="form-control @error('treatment_recommendation') is-invalid @enderror" 
                                                rows="3" 
                                                placeholder="Your treatment recommendations..."
                                                required></textarea>
                                        <div class="invalid-feedback">Please provide treatment recommendations</div> --}}
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Upload Image as Proof :</label>
                                        <input type="file" 
                                               class="form-control @error('image_proof') is-invalid @enderror" 
                                               id="image_proof" 
                                               name="image_proof" 
                                               accept="image/*" 
                                               required>
                                        <div class="invalid-feedback">Please upload an image proof</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status Update :</label>
                                        <select name="status" 
                                                class="form-select @error('status') is-invalid @enderror" 
                                                required>
                                            <option value="" disabled selected hidden>Select Status</option>
                                            @foreach(['Verified', 'False Detection'] as $status)
                                                <option value="{{ $status }}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select a status</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">No. of Infected Trees :</label>
                                        <input type="number" 
                                               class="form-control @error('no_of_infected') is-invalid @enderror" 
                                               id="no_of_infected" 
                                               name="no_of_infected" 
                                               required>
                                        <div class="invalid-feedback">Please input a number of infected trees</div>
                                    </div>
                                    <div id="infectedTreesContainer"></div>
                                </div>
                                <div class="card-footer text-center">
                                    <button type="submit" 
                                            class="btn btn-outline-success fw-bold px-4 rounded-pill" 
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
    function openMapApp() {
        const isAndroid = /android/i.test(navigator.userAgent);
        const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
        if (isIOS) {
            // Opens Apple Maps on iOS
            window.location.href = 'maps://';
        } 
        else if (isAndroid) {
            // Opens Google Maps on Android
            window.location.href = 'geo:0,0'; // Opens at user's current location
        } 
        else {
            // Fallback for other devices
            window.open('https://maps.google.com', '_blank');
        }
    }

    function getUserLocation() {
        openMapApp();
    }

    const treatments = {
            'Stem Bleeding': `
                <ul>
                    <li>Carve the affected tissue and then burn it.<br>(Pag-ukit ng apektadong tissue at pagkatapos ay sunugin ito.)</li>
                    <li>Spray using a copper-based fungicide (captan).<br>(Pagspray gamit ang copper-based na fungicide (captan).)</li>
                    <li>Paint the treated wounds with coal tar.<br>(Pagpipinta ng ginamot na sugat gamit ang coal tar.)</li>
                    <li>Ensure adequate drainage to prevent changes in soil moisture.<br>(Ang sapat na drainage upang maiwasan ang pagbabago sa moisture ng lupa.)</li>
                </ul>
            `,
            'Lethal Yellowing': `
                <ul>
                    <li>Remove and dispose of infected trees.<br>(Pag-alis at pagkakalat ng infected na puno.)</li>
                    <li>Avoid overcrowding of coconut trees to prevent rapid disease spread.<br>(Iwasan ang overcrowding ng mga puno ng niyog upang maiwasan ang mabilis na pagkalat ng sakit.)</li>
                    <li>Regularly inspect the trees to promptly detect early symptoms of Lethal Yellowing.<br>(Regular na inspeksyunin ang mga puno upang madaliang matukoy ang mga unang sintomas ng Lethal Yellowing.)</li>
                    <li>Improve overall tree health.<br>(Pagpapabuti ng kalusugan ng puno.)</li>
                    <li>Consult local agronomists or coconut experts for proper measures and technical support in managing Lethal Yellowing.<br>(Makipag-ugnayan sa mga lokal na agronomist o mga eksperto sa niyog para sa mga tamang hakbang at teknikal na suporta sa pamamahala ng Lethal Yellowing.)</li>
                </ul>
            `,
            'Leaf Spot': `
                <ul>
                    <li>Regularly remove affected leaves and dispose of them properly to prevent disease spread.<br>(Regular na alisin ang mga apektadong dahon at itapon ang mga ito nang maayos upang maiwasan ang pagkalat ng sakit.)</li>
                    <li>Do not dispose of them on the plantation soil to avoid re-infection by fungi.<br>(Huwag itong itapon sa lupa ng taniman upang hindi maging sanhi ng muling pagkalat ng fungi.)</li>
                    <li>Spray with fungicides such as copper-based fungicides.<br>(Pag-spray ng mga fungicide tulad ng copper-based fungicides.)</li>
                    <li>Ensure that the coconut trees receive proper nutrition and care to boost their resistance against diseases.<br>(Siguraduhin na ang mga puno ng niyog ay may tamang nutrisyon at sapat na pag-aalaga upang mapalakas ang kanilang resistensya laban sa mga sakit.)</li>
                    <li>Clean the area around the coconut trees from fallen leaves, branches, and other plant debris that may harbor fungal spores.<br>(Linisin ang paligid ng puno ng niyog mula sa mga natirang dahon, sanga, at iba pang plant debris na maaaring maglaman ng mga spora ng fungus.)</li>
                    <li>Regularly inspect the trees to detect early symptoms of the disease.<br>(Regular na inspeksyunin ang mga puno upang makita ang mga maagang sintomas ng sakit.)</li>
                </ul>
            `
    };

    const treatmentText = document.getElementById('treatment_recommendation');
    treatmentText.innerHTML = `<p class="mt-1 text-center">Select a disease type for treatment recommendations.</p>`;

    document.getElementById('diagnosis_disease').addEventListener('change', (event) => {
        const selectedDisease = event.target.value;
        treatmentText.innerHTML = treatments[selectedDisease] || `<p class="mt-1 text-center">Select a disease type for treatment recommendations.</p>`;
    });

    document.getElementById('no_of_infected').addEventListener('input', function() {
        const container = document.getElementById('infectedTreesContainer');
        container.innerHTML = `<small class="form-text text-muted">Tap "Find tree", click the area where the tree is planted to get coordinates.</small>`;
        const count = parseInt(this.value);
        
        if (!isNaN(count) && count > 0) {
            for (let i = 0; i < count; i++) {
                $(document).ready(function() {
                    $(`#location_${i}`).on('input', function() {
                        let value = $(this).val();
                        value = value.replace(/[°NEWS]/g, '')
                                .replace(/\s*,\s*/g, ',')
                                .trim();
                        $(this).val(value);
                        if ($(this).val()) {
                            $(`#find_tree_${i}`).prop('disabled', true);
                            $(this).removeClass('is-invalid');
                            $(`.invalid-feedback_${i}`).hide();
                        } else {
                            $(`#find_tree_${i}`).prop('disabled', false);
                        }
                    });
                });
                
                const div = document.createElement('div');
                div.classList.add('mb-3');
                div.innerHTML = `
                    <label for="location_${i}" class="form-label my-0">Tree ${i + 1} :</label><br>
                    <div class="input-group">
                        <input type="text" 
                            class="form-control @error('coordinates_${i}') is-invalid @enderror" 
                            id="location_${i}" 
                            name="locations[]" 
                            required>
                        <button type="button" 
                                class="btn btn-outline-success fw-bold" 
                                id="find_tree_${i}" 
                                onclick="getUserLocation()">Find tree</button>
                    </div>
                    <div class="invalid-feedback invalid-feedback_${i}">
                        Please provide tree coordinates
                    </div>
                `;
                container.appendChild(div);
            }
        }
    });

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
            const infected = document.getElementById('no_of_infected');
            const locationInputs = document.getElementsByName('locations[]'); // Get all location inputs

            // Validate each field
            let isValid = true;
            
            // Existing validations
            if (!diagnosis.value || diagnosis.value === '') {
                diagnosis.classList.add('is-invalid');
                isValid = false;
            } else {
                diagnosis.classList.remove('is-invalid');
            }

            // if (!treatment.value.trim()) {
            //     treatment.classList.add('is-invalid');
            //     isValid = false;
            // } else {
            //     treatment.classList.remove('is-invalid');
            // }

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
            if (!infected.value || infected.value === '') {
                infected.classList.add('is-invalid');
                isValid = false;
            } else {
                infected.classList.remove('is-invalid');
            }
            // Validate dynamic location inputs
            locationInputs.forEach((input, index) => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    // Update the invalid feedback message if it exists
                    const feedback = document.querySelector(`.invalid-feedback_${index}`);
                    if (feedback) {
                        feedback.style.display = 'block';
                    }
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                    const feedback = document.querySelector(`.invalid-feedback_${index}`);
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                }
            });

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
                location.reload();
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