@include('admin.partials.__header')
@include('admin.partials.__nav')

<main id="main" class="main">

    @if ($notif->user->user_type === '1')
        <div class="pagetitle mb-5">
                <h1><strong>{{ $notif->report->farmer_name }}'s Report</strong></h1>  
        </div>
        <section class="section notications">
            <div class="row p-0 mb-3">
                <form id="verified" action="/admin/verified-report" method="POST">
                    @csrf
                    <input type="hidden" name="farm_id" value="{{ $notif->report->farm_id }}">
                    <input type="hidden" name="farmer_id" value="{{ $notif->report->farmer_id }}">
                    <input type="hidden" name="technician_id" value="{{ $notif->report->technician_id }}">
                    <input type="hidden" name="farmer_name" value="{{ $notif->report->farmer_name }}">
                    <div class="d-flex flex-row justify-content-end align-items-center">
                        {{--  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary fw-bold"><i class="fas fa-arrow-left"></i> </a>
                            --}}
                        <button type="submit" class="btn fw-bold {{ $exist ? 'disabled btn-outline-secondary' : 'btn-outline-success' }}">
                            <i class="fas fa-check"></i> | {{ $exist ? 'Assigned' : 'Verify Assigned' }} </button>
                    </div>
                </form>
            </div>
            <div class="row p-0 m-0">
                <div class="col-lg-6">
                    <div class="row p-0 m-0">
                        <div class="card h-100" style="background-color:rgb(178, 214, 178);">
                            <div class="card-body">
                                <h5 class="card-title text-center"><strong>Assigned Technician</strong></h5>
                                <p class="card-text text-truncate">
                                    <strong>Name :</strong> {{ $notif->report->technician->full_name}}<br>
                                    <strong>Address :</strong> {{ $notif->report->technician->address }}<br>
                                    <strong>Contact No. :</strong> {{ $notif->report->technician->contact }}<br>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row p-0 m-0">
                        <div class="card h-100" style="background-color:rgb(178, 214, 178);">
                            <div class="card-body">
                                <h5 class="card-title text-center"><strong>Reporting Farmer</strong></h5>
                                <p class="card-text text-truncate">
                                    <strong>Name :</strong> {{ $notif->report->farmer_name}}<br>
                                    <strong>Contact No. :</strong> {{ $notif->report->contact_no }}<br>
                                    <strong>Farm Location :</strong> {{ $notif->report->farm_location }}<br>
                                    <strong>Farm Size :</strong> {{ $notif->report->farm_size }}<br>
                                    <strong>No. of Coconut Trees :</strong> {{ $notif->report->coconut_trees }}<br>
                                    <strong>Coconut Variety :</strong> {{ $notif->report->coconut_variety }}<br>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row p-0 m-0">
                        <div class="card m-0" style="background-color:rgb(170, 202, 221);">
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
                                <h5 class="card-title pb-0"><strong>Observed Symptoms</strong></h5>
                                @if ($notif->report->symptoms)
                                    <ul>
                                        @foreach (is_array($notif->report->symptoms) ? $notif->report->symptoms : json_decode($notif->report->symptoms, true) as $symptom)
                                            <li>{{ $symptomsMap[$symptom] ?? $symptom }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>No symptoms reported</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row p-0 m-0">
                        <div class="card mb-3" style="background-color:rgb(218, 180, 182);">
                            <div class="card-body">
                                <h5 class="card-title text-start pb-0">
                                <strong>System Status</strong> </h5>
                                <p class="text-muted">Initial disease indenfication complete. A technician has been automatically assigned for verification.</p>
                                <p class="card-text text-truncate fs-5 mb-0">
                                {{-- dd($notif->disease); --}}
                                <strong>Detected Disease:</strong><span class="text-danger ms-3"><strong>{{$notif->disease}}</strong></span></p>
                                <div class="d-flex flex-row mt-3">
                                    <p class="card-text text-truncate fs-6 mb-1 align-self-start"><strong>Proof Image Taken:</strong></p>
                                    <img class="ms-3 align-self-center p-1 rounded rounded-3 border border-1 border-danger" 
                                        src="{{ URL::asset('storage/'. $notif->report->image_path) }}" width="50%" height="auto" alt="report-disease">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="modal fade" id="confirmVerificationModal" tabindex="-1" aria-labelledby="confirmVerificationModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmVerificationModalLabel"><strong>Confirm assigned</strong></h5>
                            <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Confirm the automated assigned technician on this report ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-success" id="confirmVerifyBtn">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Success Modal -->
            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="successModalLabel"><strong>Success</strong></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Report assigned successfully!
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </section> 
    
    @else
        <div class="pagetitle mb-5">
                <h1><strong>{{ $notif->report->recipient }}'s Report</strong></h1>  
        </div>
        <section class="section notications">
            {{--          <div class="row p-0 mb-3">
                <form id="verified" action="/admin/verified-report" method="POST">
                    @csrf
                    <input type="hidden" name="farm_id" value="{{ $notif->report->farm_id }}">
                    <input type="hidden" name="farmer_id" value="{{ $notif->report->farmer_id }}">
                    <input type="hidden" name="technician_id" value="{{ $notif->report->technician_id }}">
                    <input type="hidden" name="farmer_name" value="{{ $notif->report->farmer_name }}">
                    <div class="d-flex flex-row justify-content-end align-items-center">
                      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary fw-bold"><i class="fas fa-arrow-left"></i> </a>
                        <button type="submit" class="btn fw-bold {{ $exist ? 'disabled btn-outline-secondary' : 'btn-outline-success' }}">
                            <i class="fas fa-check"></i> | {{ $exist ? 'Assigned' : 'Verify Assigned' }} </button>
                    </div>
                </form>
            </div>  --}}
   
            <div class="row p-0 m-0">
                <div class="col-lg-6">
                    <div class="row p-0 m-0">
                        <div class="card h-100" style="background-color:rgb(178, 214, 178);">
                            <div class="card-body">
                                <h5 class="card-title text-center"><strong>Assigned Farm</strong></h5>
                                <p class="card-text text-truncate">
                                    <strong>Owner :</strong> {{ $notif->report->farmer_name}}<br>
                                    <strong>Contact No. :</strong> {{ $notif->report->contact_no }}<br>
                                    <strong>Location :</strong> {{ $notif->report->farm_location }}<br>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row p-0 m-0">
                        <div class="card h-100" style="background-color:rgb(178, 214, 178);">
                            <div class="card-body">
                                <h5 class="card-title text-center"><strong>Reporting Technician</strong></h5>
                                <p class="card-text text-truncate">
                                    <strong>Name :</strong> {{ $notif->user->full_name}}<br>
                                    <strong>Contact No. :</strong> {{ $notif->user->contact }}<br>
                                    <strong>Address :</strong> {{ $notif->user->address }}<br>
                                    <strong>Municipals Covered:</strong> {{ $notif->user->municipal }}<br>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row p-0 m-0">
                        <div class="card m-0" style="background-color:rgb(170, 202, 221);">
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
                                <h5 class="card-title pb-0"><strong>Treatment Recommendation</strong></h5>
                                @if (isset($treatments[$notif->report->diagnosis_disease]))
                                    <p class="card-text text-muted ps-3"> {!! $treatments[$notif->report->diagnosis_disease] !!} </p>
                                @else
                                    <p class="card-text text-muted ps-3">No treatment recommendation available for the reported disease.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row p-0 m-0">
                        <div class="card mb-3" style="background-color:rgb(218, 180, 182);">
                            <div class="card-body">
                                <h5 class="card-title text-start pb-0">
                                <strong>System Status</strong> </h5>
                                <p class="text-muted">Disease indenfication completed. A technician has been confirmed the disease.</p>
                                <p class="card-text text-truncate fs-5 mb-0">
                                <strong>Confirmed Disease:</strong><span class="text-danger ms-3"><strong>{{$notif->disease}}</strong></span></p>
                                <div class="d-flex flex-row mt-3">
                                    <p class="card-text text-truncate fs-6 mb-1 align-self-start"><strong>Proof Image Taken:</strong></p>
                                    <img class="ms-3 align-self-center p-1 rounded rounded-3 border border-1 border-danger" 
                                        src="{{ URL::asset('storage/'. $notif->report->image_proof) }}" width="50%" height="auto" alt="report-disease">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="modal fade" id="confirmVerificationModal" tabindex="-1" aria-labelledby="confirmVerificationModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmVerificationModalLabel"><strong>Confirm assigned</strong></h5>
                            <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Confirm the automated assigned technician on this report ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-success" id="confirmVerifyBtn">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Success Modal -->
            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="successModalLabel"><strong>Success</strong></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Report assigned successfully!
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </section> 

    @endif
    {{-- dd($notif->user); --}}
</main>
@include('admin.partials.__footer')

<script>
    $(document).ready(function () {
        let $form;
        $(document).on('submit', '#verified', function (event) {
            event.preventDefault();
            $form = $(this);
            const $submitButton = $form.find('.btn-success');
            $('#confirmVerificationModal').modal('show');
        });

        $('#confirmVerifyBtn').on('click', function () {
            const $submitButton = $form.find('.btn-success');
            $.ajax({
                url: '/admin/verified-report',
                method: 'POST',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    $submitButton.prop('disabled', true);
                },
                success: function (response) {
                    if (response.success) {
                        $submitButton.prop('disabled', true);
                        $('#confirmVerificationModal').modal('hide');
                        $('#successModal').modal('show');
                        $('#successModal').on('hidden.bs.modal', function () {
                            window.location.href = "{{ url()->previous() }}";
                        });
                    } else {
                        throw new Error(response.message || 'Verification failed');
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Failed to verify report. Please try again.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    console.error('AJAX Error:', xhr.status);
                    alert(errorMessage); 
                },
                complete: function () {
                    if (!this.success) { 
                        $submitButton.prop('disabled', false);
                    }
                    $('#confirmVerificationModal').modal('hide');
                }
            });
        });
    });
</script>
