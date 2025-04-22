@include('user.partials.__header')
@include('user.partials.__nav')
<main id="main" class="main">
    <section class="section services">
        <div class="d-flex flex-column justify-content-between" style="height:85vh;">
            <div class="d-flex flex-row justify-content-start align-items-center">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary fw-bold"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="card shadow-sm p-3 mt-3" style="border: 1px solid #188754; border-radius: 8px; background-color: #f9f9f9;">
                <h5 class="card-title text-center pt-0" style="font-weight: bold;">Monitoring  Details</h5>
                <div class="mb-2">
                    <strong>Farmer Name : </strong> {{ $reportDetails->farms->name }} 
                </div>
                <div class="mb-2">
                    <strong>Location : </strong><i class="bi bi-geo-alt-fill" style="color: red;"></i> {{ $reportDetails->farm_location }} 
                </div>
                <div class="mb-2">
                    <strong>Coconut Trees : </strong> {{ $reportDetails->coconut_trees }}
                </div>
                <div class="mb-2">
                    <strong>Variety : </strong> {{ $reportDetails->coconut_variety }}
                </div>
                <div class="mb-2">
                    <strong>Size : </strong> {{ $reportDetails->farm_size }}
                </div>
            </div>
            <div class="card shadow-sm p-3" style="border: 1px solid #188754; border-radius: 8px; background-color: #f9f9f9;">
                <h5 class="card-title text-center pt-0" style="font-weight: bold;">Report Status</h5>
                <div class="mb-2">
                    <strong>Actual Disease Diagnosis : </strong> <span class="text-danger fw-bold"> {{ $reportDetails->diagnosis_disease }} </span>
                </div>
                <div class="mb-2">
                    <strong>No. Infected Trees : </strong> {{ $reportDetails->no_of_infected }} 
                </div>
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
                <div class="mb-2"><strong>Treatment Applied </strong>
                    @if (isset($treatments[$reportDetails->diagnosis_disease]))
                        <p class="card-text text-muted ps-3"> {!! $treatments[$reportDetails->diagnosis_disease] !!} </p>
                    @else
                        <p class="card-text text-muted ps-3">No treatment recommendation available for the reported disease.</p>
                    @endif
                </div>
                <div class="d-flex flex-row mt-3">
                    <p class="card-text text-truncate fs-6 mb-1 align-self-start"><strong>Uploaded Proof:</strong></p>
                    <img class="ms-3 align-self-center p-1 rounded rounded-3 border border-1 border-danger" 
                        src="{{ URL::asset('storage/'. $reportDetails->image_proof) }}" width="50%" height="auto" alt="report-disease">
                </div>
            </div>
        </div>
    </section>
</main>
@include('user.partials.__footer')

