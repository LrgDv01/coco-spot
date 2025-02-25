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

</style>

{{--<style>
    .custom-checkbox {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        position: relative;
        box-shadow: 0 0 8px green;
        transition: box-shadow 0.3s ease, background-color 0.3s ease;
    }

    .custom-checkbox:checked {
        box-shadow: none;
        background-color: green;
        border-color: green;
    }

    .custom-checkbox:not(:checked) {
        box-shadow: none;
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
        box-shadow: 0 0 0 2px rgba(0, 0, 255, 0.5);
    }
</style>--}}

<main id="main" class="main">
    <section class="section reports">
        <div class="position-fixed bg-white text-center" 
             style="top:8%; right:0; left:0; z-index: 995; width: 100%;">
            <h2 class="py-2 mt-2"><strong>Reports</strong></h2>
        </div>
        <div style="margin-top: 50px;"></div>
        <div id="farm-list">
            @foreach($farms as $farm)
                <div class="card mb-2 p-0">
                    <div class="card-body">
                        <p class="card-text text-truncate">
                            <strong>Farm Location :</strong> {{ $farm->address }}<br>
                            <strong>District :</strong> {{ $farm->district }}<br>
                            <strong>Farm Size :</strong> {{ $farm->hectares }}<br>
                            <strong>No. of Coconut Trees :</strong> {{ $farm->planted_coconut }}<br>
                            <strong>Coconut Variety :</strong> {{ $farm->variety }}<br>
                            <strong>Check Symptoms:</strong>
                            <div class="card" style="background-color: #fce9d8; max-height: 500px;">
                                <h5 class="fs-6 p-3" for="symptoms">
                                    <strong>Check the symptoms you see on your coconut trees: <br>
                                    (Lagyan ng check ang mga sintomas na nakikita mo sa iyong mga punong niyog.)</strong>
                                </h5>

                                <div class="symptom-checklist p-3" style="height: 100%; overflow-y: auto; border: 1px solid #ccc;">
                                    <!-- Leaves -->
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input custom-checkbox" id="yellowing_leaves_{{ $farm->id }}" name="symptoms[]" value="yellowing_leaves">
                                        <label class="form-check-label" for="yellowing_leaves_{{ $farm->id }}">
                                            <strong>Leaves are turning yellow.</strong> - (Dilaw ang mga dahon.) <br>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input custom-checkbox" id="wilting_{{ $farm->id }}" name="symptoms[]" value="wilting">
                                        <label class="form-check-label" for="wilting_{{ $farm->id }}">
                                            <strong>Leaves are drying up</strong> - (Natutuyo ang dahon) <br> 
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input custom-checkbox" id="spots_{{ $farm->id }}" name="symptoms[]" value="spots">
                                        <label class="form-check-label" for="spots_{{ $farm->id }}">
                                            <strong>Small brown, black, or yellow spots on leaves.</strong> - (May maliliit na batik sa dahon.) <br>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input custom-checkbox" id="stunted_growth_{{ $farm->id }}" name="symptoms[]" value="stunted_growth">
                                        <label class="form-check-label" for="stunted_growth_{{ $farm->id }}">
                                            <strong>Leaves are falling off too early</strong> - (Maagang nalalagas ang dahon.) <br>
                                        </label>
                                    </div>

                                    <!-- Trunk -->
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input custom-checkbox" id="rotting_trunk_liquid_{{ $farm->id }}" name="symptoms[]" value="rotting_trunk_liquid">
                                        <label class="form-check-label" for="rotting_trunk_liquid_{{ $farm->id }}">
                                            <strong>Dark or reddish liquid is coming out from the trunk</strong> - (May lumalabas na maitim o pulang dagta sa katawan.) <br>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input custom-checkbox" id="rotting_trunk_soft_{{ $farm->id }}" name="symptoms[]" value="rotting_trunk_soft">
                                        <label class="form-check-label" for="rotting_trunk_soft_{{ $farm->id }}">
                                            <strong>The wood inside feels soft and rotting</strong> - (Lumalambot at nabubulok ang loob ng katawan ng puno.) <br>
                                        </label>
                                    </div>

                                    <!-- Growth & Production -->
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input custom-checkbox" id="weak_production_{{ $farm->id }}" name="symptoms[]" value="weak_production">
                                        <label class="form-check-label" for="weak_production_{{ $farm->id }}">
                                            <strong>The tree is weak and producing fewer coconuts</strong> - (Mahina ang puno at kaunti ang bunga.) <br>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input custom-checkbox" id="tree_dying_{{ $farm->id }}" name="symptoms[]" value="tree_dying">
                                        <label class="form-check-label" for="tree_dying_{{ $farm->id }}">
                                            <strong>The tree is drying up completely</strong> - (Natuyo at namatay ang puno.) <br>
                                        </label>
                                    </div>
                                </div>

                                <button type="button" id="identify-btn-{{ $farm->id }}" class="btn btn-success m-3 identify-btn" data-farm-id="{{ $farm->id }}">Identify</button>

                                <div class="modal fade" id="diagnosis-modal-{{ $farm->id }}" tabindex="-1" aria-labelledby="diagnosisModalLabel-{{ $farm->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-center text-white bg-success">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="diagnosisModalLabel-{{ $farm->id }}"><strong>Diagnosis Result</strong></h5>
                                            </div>
                                            <div class="modal-body text-center">
                                                <p id="diagnosis-text-{{ $farm->id }}">No disease identified. Please check more symptoms or consult an expert.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-success bg-white text-success fw-bold" data-bs-dismiss="modal">OK</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</main>

@include('user.partials.__footer')


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
                const diseases = {
                    'Yellowing Disease': ['yellowing_leaves', 'weak_production'],
                    'Leaf Spot Disease': ['spots', 'wilting', 'stunted_growth'],
                    'Stem Bleeding Disease': ['rotting_trunk_liquid', 'rotting_trunk_soft', 'tree_dying']
                };
                const diseaseMatches = {};
                for (const [disease, symptoms] of Object.entries(diseases)) {
                    const matchedSymptoms = symptoms.filter(symptom => checkedSymptoms.includes(symptom));
                    diseaseMatches[disease] = matchedSymptoms.length;
                }
                const possibleDiseases = [];
                for (const [disease, matchCount] of Object.entries(diseaseMatches)) {
                    const totalSymptoms = diseases[disease].length;
                    if (matchCount > 0 && matchCount >= totalSymptoms / 3) { 
                        possibleDiseases.push(disease);
                    }
                }
                let diagnosis_text;
                if (checkedSymptoms.length === 0) {
                    diagnosis_text = "No symptoms selected. Please check at least one symptom.";
                } else if (possibleDiseases.length === 0) {
                    diagnosis_text = "No disease identified. Please check more symptoms or consult an expert.";
                } else if (possibleDiseases.length === 1) {
                    // diagnosis = `<strong>Possible Disease</strong>: ${possibleDiseases[0]}`;
                    diagnosis_text = `<h1><strong>${possibleDiseases[0]}</strong></h1>`;
                    if (possibleDiseases[0] === 'Yellowing Disease') {
                        diagnosis_text += "<br><br><strong>\nDescription:</strong> Yellowing and reduced production due to nutrient issues or disease.";
                    } else if (possibleDiseases[0] === 'Leaf Spot Disease') {
                        diagnosis_text += "<br><br><strong>\nDescription:</strong> Fungal infection causing spots, drying, and early leaf fall.";
                    } else if (possibleDiseases[0] === 'Stem Bleeding Disease') {
                        diagnosis_text += "<br><br><strong>\nDescription:</strong> Rotting trunk with liquid oozing, often fungal in origin.";
                    }
                } else if (possibleDiseases.length > 1) {
                    // diagnosis_header = "<strong>Possible Disease</strong>: " + possibleDiseases.join(", ");
                    diagnosis_text = `<h1><strong>${possibleDiseases.join(", ")}</strong></h1>`;
                    if (possibleDiseases.length === 3) {
                        diagnosis_text += "<br><br><strong>\nDescription:</strong> Your coconut tree may have multiple diseases. It is best to consult a technician for an accurate diagnosis.";
                    } else {
                        diagnosis_text += "<br><br><strong>\nDescription:</strong> Symptoms match multiple conditions; further inspection is recommended.";
                    }
                }
                diagnosisText.innerHTML  = diagnosis_text;
                diagnosisModal.show();
            });
        });
    });
</script>