@include('user.partials.__header')
@include('user.partials.__nav')

<main id="main" class="main">
    <div class="pagetitle mb-3 text-center">
        <h1 class="text-muted"><strong>Report Status</strong></h1> 
    </div>
    <section class="section notications">
         <div class="d-flex flex-row justify-content-between align-items-center mb-2">
            <a href="{{url()->previous()}}" class="btn btn-outline-secondary "><i class="fas fa-arrow-left"></i></a>
                @if ($verified)
                    <div class="d-flex flex-row justify-content-center">
                        <button id="download-pdf-btn" class="text-center btn btn-secondary fw-bold">Download Report (PDF)</button>
                    </div>
                @elseif ($assigned)
                    <label class="rounded p-2 text-muted fw-bold">
                        A technician was assigned . . 
                    </label>
                @else
                    <label class="rounded p-2 text-muted fw-bold">
                        Awaiting Confirmation . . 
                    </label>
                @endif
        </div>
        <div class="card mb-2 p-0 {{ $assigned ? '' : 'd-none' }}" style="background-color:rgb(170, 202, 221);">
            <div class="card-body">
                <h5 class="card-title p-0 text-muted"><strong>Assigned Technician</strong></h5><hr>
                <p class="card-text text-muted">
                    <strong>Name :</strong> {{ $reported->technician->full_name}}<br>
                    <strong>Address :</strong> {{ $reported->technician->address }}<br>
                    <strong>Contact No. :</strong> {{ $reported->technician->contact }}<br> 
                </p>
            </div>
        </div>
        <div class="card mb-2 p-0 {{ $assigned ? '' : 'd-none' }}" style="background-color:rgb(170, 202, 221);">
            <div class="card-body">
                @php
                    $treatments = [
                        'Stem Bleeding' => '
                            <ul>
                                <li>Carve the affected tissue and then burn it.<br>(Pag-ukit ng apektadong tissue at pagkatapos ay sunugin ito.)</li>
                                <li>Spray using a copper-based fungicide (captan).<br>(Pagspray gamit ang copper-based na fungicide (captan).)</li>
                                <li>Paint the treated wounds with coal tar.<br>(Pagpipinta ng ginamot na sugat gamit ang coal tar.)</li>
                                <li>Ensure adequate drainage to prevent changes in soil moisture.<br>(Ang sapat na drainage upang maiwasan ang pagbabago sa moisture ng lupa.)</li>
                            </ul>
                        ',
                        'Lethal Yellowing' => '
                            <ul>
                                <li>Remove and dispose of infected trees.<br>(Pag-alis at pagkakalat ng infected na puno.)</li>
                                <li>Avoid overcrowding of coconut trees to prevent rapid disease spread.<br>(Iwasan ang overcrowding ng mga puno ng niyog upang maiwasan ang mabilis na pagkalat ng sakit.)</li>
                                <li>Regularly inspect the trees to promptly detect early symptoms of Lethal Yellowing.<br>(Regular na inspeksyunin ang mga puno upang madaliang matukoy ang mga unang sintomas ng Lethal Yellowing.)</li>
                                <li>Improve overall tree health.<br>(Pagpapabuti ng kalusugan ng puno.)</li>
                                <li>Consult local agronomists or coconut experts for proper measures and technical support in managing Lethal Yellowing.<br>(Makipag-ugnayan sa mga lokal na agronomist o mga eksperto sa niyog para sa mga tamang hakbang at teknikal na suporta sa pamamahala ng Lethal Yellowing.)</li>
                            </ul>
                        ',
                        'Leaf Spot' => '
                            <ul>
                                <li>Regularly remove affected leaves and dispose of them properly to prevent disease spread.<br>(Regular na alisin ang mga apektadong dahon at itapon ang mga ito nang maayos upang maiwasan ang pagkalat ng sakit.)</li>
                                <li>Do not dispose of them on the plantation soil to avoid re-infection by fungi.<br>(Huwag itong itapon sa lupa ng taniman upang hindi maging sanhi ng muling pagkalat ng fungi.)</li>
                                <li>Spray with fungicides such as copper-based fungicides.<br>(Pag-spray ng mga fungicide tulad ng copper-based fungicides.)</li>
                                <li>Ensure that the coconut trees receive proper nutrition and care to boost their resistance against diseases.<br>(Siguraduhin na ang mga puno ng niyog ay may tamang nutrisyon at sapat na pag-aalaga upang mapalakas ang kanilang resistensya laban sa mga sakit.)</li>
                                <li>Clean the area around the coconut trees from fallen leaves, branches, and other plant debris that may harbor fungal spores.<br>(Linisin ang paligid ng puno ng niyog mula sa mga natirang dahon, sanga, at iba pang plant debris na maaaring maglaman ng mga spora ng fungus.)</li>
                                <li>Regularly inspect the trees to detect early symptoms of the disease.<br>(Regular na inspeksyunin ang mga puno upang makita ang mga maagang sintomas ng sakit.)</li>
                            </ul>
                        '
                    ];
                @endphp
                <h5 class="card-title p-0 text-muted"><strong>Treatment Recommendations</strong></h5><hr>
                @if (isset($treatments[$treatmentRecommendation]))
                    <p class="card-text text-muted ps-3"> {!! $treatments[$treatmentRecommendation] !!} </p>
                @else
                    <p class="card-text text-muted d-flex flex-row">
                        <i class="fas fa-exclamation-circle text-secondary me-2 fs-4"></i> <span>Yet there's no treatment recommendation available, it will appear here once a technician
                        visit your farm and submitted a report for disease verification.</span>
                    </p>
                @endif
            </div>
        </div>
        <div class="card mb-2 p-0" style="background-color:rgb(218, 180, 182);">
            <div class="card-body">
                <h5 class="card-title p-0 text-muted">
                    <strong>System Status</strong> </h5><hr> 
                    <p class="text-muted">Initial disease indenfication complete. A technician has been automatically assigned for verification.</p>
                    <p class="card-text mb-0 text-muted">
                    <strong>Detected Disease: </strong><span class="text-danger"><strong>{{$reported->disease}}</strong></span></p>
            </div>
        </div>
        <div class="card mb-2 p-0" style="background-color:rgb(178, 214, 178);">
            <div class="card-body">
                <h5 class="card-title p-0 text-muted">
                    <strong>{{ $reported->farms->name}}</strong> </h5><hr>
                    <p class="card-text text-muted">
                    <strong>Farm Location :</strong> {{ $reported->farm_location }}<br>
                    <strong>Farm Size :</strong> {{ $reported->farm_size }}<br>
                    <strong>No. of Coconut Trees :</strong> {{ $reported->coconut_trees }}<br>
                    <strong>Coconut Variety :</strong> {{ $reported->coconut_variety }}<br>
                </p>
            </div>
        </div>
        <div class="card mb-2 p-0" style="background-color:rgb(209, 201, 153);">
            <div class="card-body">
                @php
                    $symptomsMap = [
                        'yellowing_leaves' => 'Leaves are turning yellow. - (Dilaw ang mga dahon.)',
                        'dying_leaves' => 'Leaves are dying - (Pagkamatay ng mga dahon)',
                        'dropping_fruits' => 'Fruits are dropping. - (Pagbagsak ng mga bunga.)',
                        'dying_tree' => 'The tree is dying - (Pagkamatay ng puno.)',
                        'circular_spots' => 'Small circular spots with yellow edges appear on the leaves. - (Paglitaw ng maliliit na bilog na mantsa na may dilaw ng gilid sa mga dahon.)',
                        'black_or_brown_spots' => 'The spots often turn black or brown. - (Ang mga mantsa ay kadalasang nagiging itim o kayumanggi.)',
                        'lost_leaves' => 'Leaves are lost. - (Pagkawala ng mga dahon.)',
                        'reduced_yield' => 'Yield is reduced. - (Pagbaba ng ani.)',
                        'reddish_brown' => 'Reddish-brown liquid is exuded from the openings on the tree trunk. - (Naglalabas ng reddish-brown na likido mula sa mga butas sa katawan ng puno.)',
                        'cracks_or_wounds' => 'Cracks or wounds are present on the trunk. - (Pagkakaroon ng mga bitak o sugat sa katawan.)',
                        'stem_decay' => 'Decay of the internal stem. - (Pagkabulok ng panloob na tangkay.)'
                    ];
                @endphp 
                <h5 class="card-title p-0 text-muted"><strong>Observed Symptoms</strong></h5><hr>
                @if ($reported->symptoms)
                    <ul class="text-muted">
                        @foreach (is_array($reported->symptoms) ? $reported->symptoms : json_decode($reported->symptoms, true) as $symptom)
                            <li>{{ $symptomsMap[$symptom] ?? $symptom }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No symptoms reported</p>
                @endif
            </div>
        </div>
 
        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmDownloadModal" tabindex="-1" aria-labelledby="confirmDownloadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background-color: #f0f8f0; border: 2px solid green;">
                    <div class="modal-header">
                        <h5 class="modal-title text-success" id="confirmDownloadModalLabel"><strong>Report is Ready to Download</strong></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        Your PDF report file has been generated successfully. Click the button below to download.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success download-confirm-btn">Download PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@include('user.partials.__footer')
<!-- Data Script -->
<script>
    const symptomsMap = {
        'yellowing_leaves': 'Leaves are turning yellow. - (Dilaw ang mga dahon.)',
        'dying_leaves': 'Leaves are dying - (Pagkamatay ng mga dahon)',
        'dropping_fruits': 'Fruits are dropping. - (Pagbagsak ng mga bunga.)',
        'dying_tree': 'The tree is dying - (Pagkamatay ng puno.)',
        'circular_spots': 'Small circular spots with yellow edges appear on the leaves. - (Paglitaw ng maliliit na bilog na mantsa na may dilaw ng gilid sa mga dahon.)',
        'black_or_brown_spots': 'The spots often turn black or brown. - (Ang mga mantsa ay kadalasang nagiging itim o kayumanggi.)',
        'lost_leaves': 'Leaves are lost. - (Pagkawala ng mga dahon.)',
        'reduced_yield': 'Yield is reduced. - (Pagbaba ng ani.)',
        'reddish_brown': 'Reddish-brown liquid is exuded from the openings on the tree trunk. - (Naglalabas ng reddish-brown na likido mula sa mga butas sa katawan ng puno.)',
        'cracks_or_wounds': 'Cracks or wounds are present on the trunk. - (Pagkakaroon ng mga bitak o sugat sa katawan.)',
        'stem_decay': 'Decay of the internal stem. - (Pagkabulok ng panloob na tangkay.)'
    };
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
    const farmData = {
        name: {!! json_encode($reported->farms->name) !!},
        location: {!! json_encode($reported->farm_location) !!},
        size: {!! json_encode($reported->farm_size) !!},
        trees: '{{ $reported->coconut_trees }}',
        variety: {!! json_encode($reported->coconut_variety) !!},
        symptoms: {!! json_encode(is_array($reported->symptoms) ? $reported->symptoms : json_decode($reported->symptoms, true)) !!},
        disease: {!! json_encode($reported->disease) !!},
        status: {!! json_encode($verified ? "Disease Confirmed" : ($assigned ? "Technician Assigned" : "Awaiting Confirmation")) !!},
        technician: {
            name: {!! json_encode($reported->technician->full_name ?? "") !!},
            address: {!! json_encode($reported->technician->address ?? "") !!},
            contact: {!! json_encode($reported->technician->contact ?? "") !!}
        },
        treatment: {!! json_encode($treatmentRecommendation) !!}
    };

    document.getElementById('download-pdf-btn').addEventListener('click', function() {
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmDownloadModal'));
        confirmModal.show();
    });

    document.querySelector('.download-confirm-btn').addEventListener('click', function() {
        const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmDownloadModal'));
        confirmModal.hide();

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        // Title
        doc.setFontSize(20);
        doc.setFont("helvetica", "bold");
        doc.text("Farm Status Report", 105, 15, { align: "center" });

        // Farm Details Table
        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        doc.text("Farm Details", 10, 25);
        doc.autoTable({
            startY: 30,
            head: [["Field", "Value"]],
            body: [
                ["Farm Name", farmData.name],
                ["Location", farmData.location],
                ["Size", farmData.size],
                ["Number of Coconut Trees", farmData.trees],
                ["Coconut Variety", farmData.variety]
            ],
            theme: "grid",
            headStyles: { fillColor: [22, 160, 133] }, // Green header
            styles: { fontSize: 10 }
        });
        let y = doc.lastAutoTable.finalY + 10;

        // Observed Symptoms
        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        doc.text("Observed Symptoms", 10, y);
        y += 7;
        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        farmData.symptoms.forEach(symptom => {
            const lines = doc.splitTextToSize("• " + symptomsMap[symptom] ?? symptom , 180);
            lines.forEach(line => {
                doc.text(line, 15, y);
                y += 5;
                if (y > 270) {
                    doc.addPage();
                    y = 20;
                }
            });
        });
        y += 5;

        // Disease Status
        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        doc.text("Disease Status", 10, y);
        y += 7;
        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.text(`Detected Disease: ${farmData.disease}`, 15, y);
        y += 5;
        doc.text(`Status: ${farmData.status}`, 15, y);
        y += 10;

        // Technician Details (if assigned)
        if (farmData.technician.name) {
            doc.setFontSize(12);
            doc.setFont("helvetica", "bold");
            doc.text("Assigned Technician", 10, y);
            doc.autoTable({
                startY: y + 5,
                head: [["Field", "Value"]],
                body: [
                    ["Name", farmData.technician.name],
                    ["Address", farmData.technician.address],
                    ["Contact No.", farmData.technician.contact]
                ],
                theme: "grid",
                headStyles: { fillColor: [22, 160, 133] },
                styles: { fontSize: 10 }
            });
            y = doc.lastAutoTable.finalY + 10;
        }

        // Treatment Recommendations (if available)
        if (farmData.treatment) {
            solutionContent = diseaseSolutions[farmData.treatment];
            doc.setFontSize(12);
            doc.setFont("helvetica", "bold");
            doc.text("Treatment Recommendations", 10, y);
            y += 7;
            doc.setFontSize(10);
            doc.setFont("helvetica", "normal");

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

            // const treatmentLines = doc.splitTextToSize(farmData.treatment, 180);
            // treatmentLines.forEach(line => {
            //     doc.text(line, 15, y);
            //     y += 5;
            //     if (y > 270) {
            //         doc.addPage();
            //         y = 20;
            //     }
            // });
        }

        // Footer with date and page number
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 10, 285);
            doc.text(`Page ${i} of ${pageCount}`, 190, 285, { align: "right" });
        }

        // Save the PDF
        doc.save("Farm_Status_Report.pdf");
    });
</script>

