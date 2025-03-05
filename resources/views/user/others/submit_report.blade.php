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
        color: #dc3545; 
        font-size: 24px;
        margin-bottom: 15px;
    }

    .progress {
        height: 20px;
        margin-bottom: 10px;
    }

    .progress-bar {
        background-color: #dc3545; 
    }

    /* for solution content */
    .solution-content {
        line-height: 1.6;
    }

    .solution-content h2 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: #fff;
        border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        padding-bottom: 0.5rem;
    }

    .solution-content ul {
        padding-left: 1.5rem;
        margin-bottom: 0;
    }

    .solution-content li {
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    .solution-content li strong {
        color: #e9ecef;
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
        <form id="report-form-{{ $farm->id }}" action="/user/farmer-report" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Hidden inputs for farm data -->
            <input type="hidden" name="farm_id" value="{{ $farm->id }}">
            <input type="hidden" name="farm_location" value="{{ $farm->address ?? 'Laguna' }}">
            <input type="hidden" name="farm_size" value="{{ $farm->hectares }}">
            <input type="hidden" name="coconut_trees" value="{{ $farm->planted_coconut }}">
            <input type="hidden" name="coconut_variety" value="{{ $farm->variety }}">
            <input type="hidden" name="farmer_name" value="{{ auth()->user()->full_name }}">
            <input type="hidden" name="contact_no" value="{{ auth()->user()->contact }}">

            <strong>Check Symptoms:</strong>
            <div class="card" style="background-color: #fce9d8; max-height: 500px;">
                <h5 class="fs-6 p-2" for="symptoms">
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
                        <label class="form-check-label" for="circular_spots_{{ $farm->id }}">Small circular spots with yellow edges appear on the leaves. - (Paglitaw ng maliliit na bilog na mantsa na may dilaw ng gilid sa mga dahon.)</label>
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
                <div class="form-group mt-3 px-2">
                    <label for="image_proof_{{ $farm->id }}"><strong>Upload Image as Proof:</strong></label>
                    <input type="file" class="form-control" id="image_proof_{{ $farm->id }}" name="image_proof" accept="image/*">
                </div>
                <button type="submit" id="identify-btn-{{ $farm->id }}" 
                        class="btn btn-success m-3 identify-btn" 
                        data-farm-id="{{ $farm->id }}" disabled>
                    <i class="fas fa-search"></i> Identify
                </button>
                {{--  <button type="submit" id="identify-btn-{{ $farm->id }}" class="btn btn-success m-3 identify-btn" 
                    data-farm-id="{{ $farm->id }}" disabled><i></i> Identify</button></div>  --}}
        </form>
    </div>
</div>
        
<div class="modal fade" 
    id="diagnosis-modal-{{ $farm->id }}" tabindex="-1" 
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    role="dialog"
    aria-labelledby="diagnosisModalLabel-{{ $farm->id }}" 
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background-color: #f0f8f0; border: 2px solid green;">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="diagnosisModalLabel-{{ $farm->id }}"><strong>Diagnosis Result</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="diagnosis-text-{{ $farm->id }}">
            </div>
            <div class="modal-footer d-flex flex-column">
                <div class="d-flex flex-row justify-content-end">
                    <button type="button" class="btn btn-outline-secondary me-2" 
                            onclick="viewSolution(event, {{ $farm->id }})">View Solution</button>
                    <button type="submit" class="btn btn-outline-success"
                            onclick="submitDiagnosis(event, {{ $farm->id }})">Report</button>
                </div>
                <div class="d-flex flex-row justify-content-center">
                    <a class="farm-status d-none btn btn-secondary border-0 bg-transparent text-secondary" href="{{route('user.services')}}">
                        <i class="fas fa-arrow-left"></i> See farm status</a>
                </div>
            </div>
        </div>
    </div>
</div>

</section>
</main>

@include('user.partials.__footer')

 <script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('[id^="report-form-"]');
        forms.forEach(form => {
            const farmId = form.id.replace('report-form-', '');
            const modalElement = document.getElementById(`diagnosis-modal-${farmId}`);
            const diagnosisModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static', 
                keyboard: false,
                role: 'dialog'
            });
            const diagnosisText = document.getElementById(`diagnosis-text-${farmId}`);
            const identifyBtn = form.querySelector(`#identify-btn-${farmId}`);
            const checkboxes = form.querySelectorAll('.custom-checkbox');
            function toggleButtonState() {
                identifyBtn.disabled = !Array.from(checkboxes).some(checkbox => checkbox.checked);
            }
            toggleButtonState();
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleButtonState);
            });

            function viewSolution(event, farmId) {
                event.stopPropagation(); 
                const diagnosisText = document.getElementById(`diagnosis-text-${farmId}`);
                
                const topDiseaseElement = diagnosisText.querySelector('h1 strong');
                const topDisease = topDiseaseElement ? topDiseaseElement.textContent.split(' DETECTED')[0] : 'Unknown';

                const diseaseSolutions = {
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
                    'Coconut Leaf Spot': `
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

                let solutionContent = `<h2>No Solutions Available</h2><p>Please identify a disease first to view its solutions.</p>`;
                if (topDisease && diseaseSolutions[topDisease]) {
                    solutionContent = diseaseSolutions[topDisease];
                }
                diagnosisText.innerHTML += `
                    <div id="hide" class="position-fixed d-flex flex-column rounded rounded-3 text-white px-2 pe-2 overflow-auto shadow-lg" 
                        style="background-color:rgb(26, 59, 31); top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; height: 70vh; max-width: 400px; z-index: 1050;">
                        <button class="btn btn-secondary bg-transparent border-0 btn-lg close-solution-btn align-self-end px-3 fs-1" data-farm-id="${farmId}">
                                <i class="fas fa-times"></i>
                        </button>
                        <div style="height: 100%; overflow-y: auto;">
                            <div class="d-flex justify-content-center align-items-center my-3">
                                <h3 class="m-0 text-danger"><strong>${topDisease ? topDisease + ' <span class="text-success">(Solutions)</span>' : 'Solutions'}</strong></h3>
                            </div>
                            <div class="solution-content text-start">
                                ${solutionContent}
                            </div>
                        </div>
                        <button class="btn btn-secondary border-1 btn-sm align-self-center px-3 my-2 download-pdf-btn rounded rounded-3" 
                                data-farm-id="${farmId}">
                            <i class="fas fa-file"></i> Download Solutions (PDF)
                        </button>
                    </div>
                `;
                const closeButton = diagnosisText.querySelector('.close-solution-btn');
                closeButton.addEventListener('click', function(e) {
                    closeSolution(e, farmId);
                });
                const downloadButton = diagnosisText.querySelector('.download-pdf-btn');
                downloadButton.addEventListener('click', function() {
                    const confirmModalHtml = `
                        <div class="modal fade" id="confirmDownloadModal" tabindex="-1" aria-labelledby="confirmDownloadModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="background-color: #f0f8f0; border: 2px solid green;">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-success" id="confirmDownloadModalLabel"><strong>Solution is Ready to Download</strong></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        Your PDF solution file has been generated successfully. Click the button below to download.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-success download-confirm-btn" data-farm-id="${farmId}">Download PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', confirmModalHtml);
                    const confirmModal = new bootstrap.Modal(document.getElementById('confirmDownloadModal'));
                    confirmModal.show();
                    const confirmDownloadBtn = document.querySelector('.download-confirm-btn');
                    confirmDownloadBtn.addEventListener('click', function() {
                        confirmModal.hide();
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF({
                            orientation: 'portrait',
                            unit: 'mm',
                            format: 'a4'
                        });
                        doc.setFontSize(20);
                        doc.setFont('helvetica', 'bold');
                        doc.text(`${topDisease || 'Unknown'} - Solutions`, 105, 15, { align: 'center' });
                        doc.setFontSize(12);
                        doc.setFont('helvetica', 'normal');
                        doc.text('Recommended Actions', 105, 25, { align: 'center' });
                        const plainTextSolutions = solutionContent
                            .replace(/<ul>|<\/ul>/g, '')
                            .replace(/<li>/g, '')
                            .replace(/<\/li>/g, '\n\n') 
                            .replace(/<br>/g, '\n')   
                            .replace(/<[^>]+>/g, '');
                        const solutionGroups = plainTextSolutions.split('\n\n').filter(group => group.trim().length > 0);
                        let yPosition = 35;
                        doc.setFontSize(11);
                        solutionGroups.forEach((group, index) => {
                            const lines = group.split('\n').filter(line => line.trim().length > 0);
                            if (lines.length > 0) {
                                if (index > 0) {
                                    yPosition += 4; 
                                }
                                const mainSolutionLines = doc.splitTextToSize(lines[0].trim(), 180);
                                mainSolutionLines.forEach((line, i) => {
                                    if (i === 0) {
                                        doc.text('•', 15, yPosition); 
                                    }
                                    doc.text(line, 20, yPosition);
                                    yPosition += 6;
                                });
                                if (lines[1] && lines[1].trim().startsWith('(')) {
                                    const translationLines = doc.splitTextToSize(lines[1].trim(), 180);
                                    translationLines.forEach(line => {
                                        doc.text(line, 20, yPosition);
                                        yPosition += 6;
                                    });
                                }
                                if (yPosition > 270) { 
                                    doc.addPage();
                                    yPosition = 20;
                                }
                            }
                        });
                        doc.setFontSize(10);
                        doc.setTextColor(100);
                        doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 10, 285);
                        doc.text('Page 1', 190, 285, { align: 'right' });
                        doc.save(`${topDisease || 'Unknown'}_Solutions.pdf`);
                        confirmModal.hide();
                        document.getElementById('confirmDownloadModal').remove();
                    });
                });
            }
            function closeSolution(event, farmId) {
                event.stopPropagation(); 
                event.preventDefault();  
                const diagnosisText = document.getElementById(`diagnosis-text-${farmId}`);
                const solutionDiv = diagnosisText.querySelector('#hide');
                if (solutionDiv) {
                    solutionDiv.remove();
                }
            }
            const viewButton = modalElement.querySelector('.btn-outline-secondary');
            viewButton.addEventListener('click', function(event) {
                viewSolution(event, farmId);
            });
            form.addEventListener('submit', function(e) {
                e.preventDefault(); 
                const formData = new FormData(this);
                const checkedSymptoms = formData.getAll('symptoms[]');
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
                    diagnosisHtml += `<p>Based on the symptoms you provided, there is an 
                                    <strong>${topProbability}%</strong> chance this matches <strong>${topDisease}</strong> disease.</p>`;
                    for (const disease of sortedDiseases) {
                        const probability = Math.round(diseaseProbabilities[disease]);
                        if (probability > 0) {
                            if (disease !== sortedDiseases[0]) {
                                if (!diagnosisHtml.includes('Other possible matches:')) {
                                    diagnosisHtml += '<br><p>Other possible matches:</p>';
                                }
                            } 
                            diagnosisHtml += `
                                <div class="d-flex flex-column align-items-center mb-3">
                                    <div class="progress w-100 p-0 m-0">
                                        <div class="progress-bar p-0 m-0" role="progressbar" style="width: ${probability}%;" aria-valuenow="${probability}" aria-valuemin="0" aria-valuemax="100">${probability}%</div>
                                    </div>
                                    <span class="me-2 p-0"><strong>${disease}</strong></span>
                                </div>`;
                        } 
                    }
                }
                diagnosisText.innerHTML = diagnosisHtml;
                diagnosisModal.show();
                formData.append('disease', topDisease || 'Unknown');
                async function submitDiagnosis(event, farmId) {
                    event.preventDefault();
                    try {
                        diagnosisText.innerHTML += '<img src="/img/loading.gif" alt="Loading..." id="loading-gif" class="mt-3">';
                        const response = await fetch('/user/farmer-report', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            console.log('Report saved successfully');
                            diagnosisText.innerHTML += '<p class="mt-5 text-success">Disease identified, Report submitted successfully!</p>';
                            reportButton.classList.remove('btn-outline-success');
                            reportButton.classList.add('btn-success');
                            reportButton.textContent = 'Reported';
                            farmStatusButton.classList.remove('d-none');
                            // add icon for button (optional)
                            reportButton.disabled = true;
                        } else {
                            console.error('Error:', data.message);
                            diagnosisText.innerHTML += '<p class="mt-3 text-danger"><strong>Error saving report: ' + data.message + '</strong></p>';
                        }
                    } catch (error) {
                        console.error('AJAX error:', error);
                        diagnosisText.innerHTML += '<p class="mt-3 text-danger"><strong>Network error, report may not have been saved.</strong></p>';
                    } finally {
                        const loadingGif = document.getElementById('loading-gif');
                        if (loadingGif) {
                            loadingGif.remove();
                        }
                    }
                }
                const reportButton = modalElement.querySelector('.btn-outline-success'); 
                reportButton.addEventListener('click', submitDiagnosis);
                const farmStatusButton = modalElement.querySelector('.farm-status');
            });
        });
    });
</script>

