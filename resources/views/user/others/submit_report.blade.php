@include('user.partials.__header')
@include('user.partials.__nav')

<style>
    .custom-checkbox {
        appearance: none; 
        width: 20px;
        height: 20px;
        border: 2px solid grey; 
        border-radius: 4px;
        cursor: pointer;
        position: relative;
        box-shadow: 0 0 8px grey;
        transition: box-shadow 0.3s ease, background-color 0.3s ease; 
    }

    .custom-checkbox:checked {
        box-shadow: none; 
        background-color: grey; 
        border-color: grey; 
    }

    .custom-checkbox:not(:checked) {
        outline: none; 
        border: 2px solid grey; 
        box-shadow: 0 0 8px grey; 
    }

    .custom-checkbox:checked::after {
        content: '\2713';
        color: white;
        font-size: 14px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .custom-checkbox:focus:not(:checked) {
        outline: none; 
        box-shadow: 0 0 8px grey;
    }

    .modal-content {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    background-color: #f0f8f0;
    border-bottom: 2px solid #28a745;
}

.modal-body {
    padding: 20px;
    font-size: 16px;
    color: #333;
}

.modal-body h1 {
    color: #dc3545; /* Red text for disease name, like "STEM BLEEDING DETECTED" */
    font-size: 24px;
    margin-bottom: 15px;
}

.progress {
    height: 20px;
    margin-bottom: 10px;
}

.progress-bar {
    background-color: #dc3545; /* Red bar for probabilities */
}

</style>


<main id="main" class="main">
    <section class="section reports">

        <h2 class="py-2 mt-2 text-center"><strong>Diagnostic Report</strong></h2>
        @if (session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="card mb-2 p-0" style="background-color: #f0f8f0; border: 1px solid #28a745;">
    <div class="card-body">
        <p class="card-text text-truncate">
            <strong>Farm Location :</strong> {{ $farm->address }}<br>
            <strong>District :</strong> {{ $farm->district }}<br>
            <strong>Farm Size :</strong> {{ $farm->hectares }}<br>
            <strong>No. of Coconut Trees :</strong> {{ $farm->planted_coconut }}<br>
            <strong>Coconut Variety :</strong> {{ $farm->variety }}<br>
        </p>
        <form id="report-form-{{ $farm->id }}" action="/user/farmer-report" method="POST">
            @csrf
            <!-- Hidden inputs for farm data -->
            <input type="hidden" name="farm_id" value="{{ $farm->id }}">
            <input type="hidden" name="farm_location" value="{{ $farm->address ?? 'Laguna' }}">
            <input type="hidden" name="farm_size" value="{{ $farm->hectares }}">
            <input type="hidden" name="coconut_trees" value="{{ $farm->planted_coconut }}">
            <input type="hidden" name="coconut_variety" value="{{ $farm->variety }}">
            <!-- Assuming farmer_name and contact_no come from auth or elsewhere -->
            <input type="hidden" name="farmer_name" value="{{ auth()->user()->full_name }}">
            <input type="hidden" name="contact_no" value="{{ auth()->user()->contact }}">

            <strong>Check Symptoms:</strong>
            <div class="card" style="background-color: #fce9d8; max-height: 500px;">
                <h5 class="fs-6 p-3" for="symptoms">
                    <strong>Check the symptoms you see on your coconut trees: 
                        <br>(Lagyan ng check ang mga sintomas na nakikita mo sa iyong mga punong niyog.)</strong>
                </h5>
                <div class="symptom-checklist p-3" style="height: 100%; overflow-y: auto; border: 1px solid #ccc;">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="yellowing_leaves_{{ $farm->id }}" name="symptoms[]" value="yellowing_leaves">
                        <label class="form-check-label" for="yellowing_leaves_{{ $farm->id }}">Leaves are turning yellow. - (Dilaw ang mga dahon.)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="dying_leaves_{{ $farm->id }}" name="symptoms[]" value="dying_leaves">
                        <label class="form-check-label" for="dying_leaves_{{ $farm->id }}">Leaves are dying - (Pagkamatay ng mga dahon)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="dropping_fruits_{{ $farm->id }}" name="symptoms[]" value="dropping_fruits">
                        <label class="form-check-label" for="dropping_fruits_{{ $farm->id }}">Fruits are dropping. - (Pagbagsak ng mga bunga.)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="dying_tree_{{ $farm->id }}" name="symptoms[]" value="dying_tree">
                        <label class="form-check-label" for="dying_tree_{{ $farm->id }}">The tree is dying - (Pagkamatay ng puno.)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="circular_spots_{{ $farm->id }}" name="symptoms[]" value="circular_spots">
                        <label class="form-check-label" for="circular_spots_{{ $farm->id }}">Small Circular spots with yellow edges appear on the leaves. - (Paglitaw ng maliliit na bilog na mantsa na may dilaw ng gilid sa mga dahon.)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="black_or_brown_spots_{{ $farm->id }}" name="symptoms[]" value="black_or_brown_spots">
                        <label class="form-check-label" for="black_or_brown_spots_{{ $farm->id }}">The spots often turn black or brown. - (Ang mga mantsa ay kadalasang nagiging itim o kayumanggi.)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="lost_leaves_{{ $farm->id }}" name="symptoms[]" value="lost_leaves">
                        <label class="form-check-label" for="lost_leaves_{{ $farm->id }}">Leaves are lost. - (Pagkawala ng mga dahon.)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="reduced_yield_{{ $farm->id }}" name="symptoms[]" value="reduced_yield">
                        <label class="form-check-label" for="reduced_yield_{{ $farm->id }}">Yield is reduced. - (Pagbaba ng ani.)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="reddish_brown_{{ $farm->id }}" name="symptoms[]" value="reddish_brown">
                        <label class="form-check-label" for="reddish_brown_{{ $farm->id }}">Reddish-brown liquid is exuded from the openings on the tree trunk. - (Naglalabas ng reddish-brown na likido mula sa mga butas sa katawan ng puno.)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="cracks_or_wounds_{{ $farm->id }}" name="symptoms[]" value="cracks_or_wounds">
                        <label class="form-check-label" for="cracks_or_wounds_{{ $farm->id }}">Cracks or wounds are present on the trunk. - (Pagkakaroon ng mga bitak o sugat sa katawan.)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="stem_decay_{{ $farm->id }}" name="symptoms[]" value="stem_decay">
                        <label class="form-check-label" for="stem_decay_{{ $farm->id }}">Decay of the internal stem. - (Pagkabulok ng panloob na tangkay.)</label>
                    </div>
                </div>
                <button type="submit" id="identify-btn-{{ $farm->id }}" class="btn btn-success m-3 identify-btn" data-farm-id="{{ $farm->id }}" disabled>Identify</button>            </div>
        </form>

        <!-- Modal for diagnosis -->
        <div class="modal fade" id="diagnosis-modal-{{ $farm->id }}" tabindex="-1" aria-labelledby="diagnosisModalLabel-{{ $farm->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background-color: #f0f8f0; border: 5px solid #28a745;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="diagnosisModalLabel-{{ $farm->id }}"><strong>Diagnosis Result</strong></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center" id="diagnosis-text-{{ $farm->id }}"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal" style="background-color: #28a745; color: white;">OKAY</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        
    
    
    
    </section>
</main>

@include('user.partials.__footer')

{{--
<script>

    document.addEventListener('DOMContentLoaded', function() {
        const identifyButtons = document.querySelectorAll('.identify-btn');
        identifyButtons.forEach(button => {
            const farmId = button.getAttribute('data-farm-id');
            const modalElement = document.getElementById(`diagnosis-modal-${farmId}`);
            const diagnosisModal = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true
            });
            const diagnosisText = document.getElementById(`diagnosis-text-${farmId}`);

            button.addEventListener('click', function() {
                const card = button.closest('.card');
                const checkedSymptoms = Array.from(card.querySelectorAll('.custom-checkbox:checked'))
                    .map(checkbox => checkbox.value);

                // Define symptom-to-disease mappings without fixed base probabilities
                const diseaseSymptoms = {
                    'Stem Bleeding Disease': ['reddish_brown', 'cracks_or_wounds', 'stem_decay'],
                    'Coconut Leaf Spot': ['circular_spots', 'black_or_brown_spots', 'lost_leaves', 'reduced_yield'],
                    'Lethal Yellowing': ['yellowing_leaves', 'dying_leaves', 'dropping_fruits', 'dying_tree']
                };

                const diseaseProbabilities = {};
                for (const [disease, symptoms] of Object.entries(diseaseSymptoms)) {
                    const matchedSymptoms = symptoms.filter(symptom => checkedSymptoms.includes(symptom));
                    const totalSymptoms = symptoms.length;
                    let probability = 0;

                    if (matchedSymptoms.length > 0) {
                        // Calculate probability based solely on the proportion of matched symptoms (scaled to 100%)
                        const matchRatio = matchedSymptoms.length / totalSymptoms;
                        probability = matchRatio * 100; // Probability as a percentage based on matches
                    }
                    diseaseProbabilities[disease] = probability;
                }
                const sortedDiseases = Object.entries(diseaseProbabilities)
                    .sort((a, b) => b[1] - a[1])
                    .map(([disease]) => disease);

                let diagnosisHtml = '';

                if (checkedSymptoms.length === 0) {
                    diagnosisHtml = '<p>No symptoms selected. Please check at least one symptom.</p>';
                } else if (sortedDiseases.length === 0 || diseaseProbabilities[sortedDiseases[0]] === 0) {
                    diagnosisHtml = '<p>No disease identified. Please check more symptoms or consult an expert.</p>';
                } else {
                    const topDisease = sortedDiseases[0];
                    const topProbability = Math.round(diseaseProbabilities[topDisease]);
                    diagnosisHtml = `<h1><strong>${topDisease} DETECTED</strong> (${topProbability}%)</h1>`;
                    diagnosisHtml += `<p>Based on the symptoms you provided, there is an <strong>${topProbability}%</strong> chance this matches <strong>${topDisease}</strong> disease.</p>`;
                    // diagnosisHtml += '<p>Below the highest are other possible matches:</p>';
                    for (const disease of sortedDiseases) {
                        const probability = Math.round(diseaseProbabilities[disease]);
                        if (probability > 0) {
                            if (disease !== sortedDiseases[0]) {
                                if (!diagnosisHtml.includes('Other possible matches:')) {
                                    diagnosisHtml += '<br><p>Other possible matches:</p>';
                                }
                            } 
                            diagnosisHtml += `
                                <div class="d-flex align-items-center">
                                    <span class="me-2">${disease}</span>
                                    <div class="progress w-100">
                                        <div class="progress-bar" role="progressbar" style="width: ${probability}%;" aria-valuenow="${probability}" aria-valuemin="0" aria-valuemax="100">${probability}%</div>
                                    </div>
                                </div>`;
                        } 
                    }
                }
                diagnosisText.innerHTML = diagnosisHtml;
                diagnosisModal.show();
            });
        });
    });

</script>--}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('[id^="report-form-"]');
    forms.forEach(form => {
        const farmId = form.id.replace('report-form-', '');
        const modalElement = document.getElementById(`diagnosis-modal-${farmId}`);
        const diagnosisModal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true
        });
        const diagnosisText = document.getElementById(`diagnosis-text-${farmId}`);
        const identifyBtn = form.querySelector(`#identify-btn-${farmId}`);
        const checkboxes = form.querySelectorAll('.custom-checkbox');
        // Function to toggle button state
        function toggleButtonState() {
            identifyBtn.disabled = !Array.from(checkboxes).some(checkbox => checkbox.checked);
        }
        toggleButtonState();

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', toggleButtonState);
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent traditional form submission

            const formData = new FormData(this);
            const checkedSymptoms = formData.getAll('symptoms[]');

            // Client-side diagnosis logic
            const diseaseSymptoms = {
                'Stem Bleeding': ['reddish_brown', 'cracks_or_wounds', 'stem_decay'],
                'Coconut Leaf Spot': ['circular_spots', 'black_or_brown_spots', 'lost_leaves', 'reduced_yield'],
                'Lethal Yellowing': ['yellowing_leaves', 'dying_leaves', 'dropping_fruits', 'dying_tree']
            };

            const diseaseProbabilities = {};
            for (const [disease, symptoms] of Object.entries(diseaseSymptoms)) {
                const matchedSymptoms = symptoms.filter(symptom => checkedSymptoms.includes(symptom));
                const totalSymptoms = symptoms.length;
                let probability = matchedSymptoms.length > 0 ? (matchedSymptoms.length / totalSymptoms) * 100 : 0;
                diseaseProbabilities[disease] = probability;
            }

            const sortedDiseases = Object.entries(diseaseProbabilities)
                .sort((a, b) => b[1] - a[1])
                .map(([disease]) => disease);

            let diagnosisHtml = '';
            let topDisease = '';
            let topProbability = 0;

            if (checkedSymptoms.length === 0) {
                diagnosisHtml = '<p>No symptoms selected. Please check at least one symptom.</p>';
            } else if (sortedDiseases.length === 0 || diseaseProbabilities[sortedDiseases[0]] === 0) {
                diagnosisHtml = '<p>No disease identified. Please check more symptoms or consult an expert.</p>';
            } else {
                topDisease = sortedDiseases[0];
                topProbability = Math.round(diseaseProbabilities[topDisease]);
                diagnosisHtml = `<h1><strong>${topDisease} DETECTED</strong> (${topProbability}%)</h1>`;
                diagnosisHtml += `<p>Based on the symptoms you provided, there is an <strong>${topProbability}%</strong> chance this matches <strong>${topDisease}</strong> disease.</p>`;
   
                // for (const disease of sortedDiseases) {
                //     const probability = Math.round(diseaseProbabilities[disease]);
                //     if (probability > 0 && disease !== sortedDiseases[0]) {
                //         if (!diagnosisHtml.includes('Other possible matches:')) {
                //             diagnosisHtml += '<br><p>Other possible matches:</p>';
                //         }
                //         diagnosisHtml += `
                //             <div class="d-flex align-items-center">
                //                 <span class="me-2">${disease}</span>
                //                 <div class="progress w-100">
                //                     <div class="progress-bar" role="progressbar" style="width: ${probability}%;" aria-valuenow="${probability}" aria-valuemin="0" aria-valuemax="100">${probability}%</div>
                //                 </div>
                //             </div>`;
                //     }
                // }

                for (const disease of sortedDiseases) {
                    const probability = Math.round(diseaseProbabilities[disease]);
                    if (probability > 0) {
                        if (disease !== sortedDiseases[0]) {
                            if (!diagnosisHtml.includes('Other possible matches:')) {
                                diagnosisHtml += '<br><p>Other possible matches:</p>';
                            }
                        } 
                        diagnosisHtml += `
                            <div class="d-flex flex-column align-items-center">
                                <span class="me-2"><strong>${disease}</strong></span>
                                <div class="progress w-100">
                                    <div class="progress-bar" role="progressbar" style="width: ${probability}%;" aria-valuenow="${probability}" aria-valuemin="0" aria-valuemax="100">${probability}%</div>
                                </div>
                            </div>`;
                    } 
                }
            }

            diagnosisText.innerHTML = diagnosisHtml;
            diagnosisModal.show();

            // Add the diagnosed disease to formData for submission
            formData.append('disease', topDisease || 'Unknown');

            // AJAX submission
            fetch('/user/farmer-report', {
                method: 'POST',
                body: formData,
                headers: {
                    // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Report saved successfully');
                    // Optionally update modal with a success message
                    diagnosisText.innerHTML += '<p class="mt-5 text-success"><strong>Disease indentified, Report submitted successfully!</strong></p>';
                } else {
                    console.error('Error:', data.message);
                    diagnosisText.innerHTML += '<p class="mt-3 text-danger"><strong>Error saving report: ' + data.message + '</strong></p>';
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                diagnosisText.innerHTML += '<p class="mt-3 text-danger"><strong>Network error, report may not have been saved.</strong></p>';
            });
        });
    });
});
</script>