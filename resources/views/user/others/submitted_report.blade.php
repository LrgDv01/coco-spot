@include('user.partials.__header')
@include('user.partials.__nav')

<main id="main" class="main">
    <div class="pagetitle mb-3 text-center">
        <h1><strong>Farm Status</strong></h1>
    </div>
    <section class="section notications">
         <div class="d-flex flex-row justify-content-between align-items-center mb-2">
            <a href="{{url()->previous()}}" class="btn btn-outline-secondary "><i class="fas fa-arrow-left"></i></a>
            <button class="btn {{$verified ? 'btn-success' : ($assigned ? 'btn-primary' : 'btn-secondary') }} disabled">
                @if ($verified)
                    <i class="fas fa-check"></i> | Disease Confirmed 
                @elseif ($assigned)
                    Technician Assigned . . 
                @else
                    Awaiting Confirmation . . 
                @endif
            </button>
        </div>

        <div class="card mb-2 p-0 {{ $assigned ? '' : 'd-none' }}" style="background-color:rgb(170, 202, 221);">
            <div class="card-body">
                <h5 class="card-title p-0"><strong>Assigned Technician</strong></h5><hr>
                <p class="card-text">
                    <strong>Name :</strong> {{ $reported->technician->full_name}}<br>
                    <strong>Address :</strong> {{ $reported->technician->address }}<br>
                    <strong>Contact No. :</strong> {{ $reported->technician-> contact }}<br> 
                </p>
            </div>
        </div>
        <div class="card mb-2 p-0 {{ $assigned ? '' : 'd-none' }}" style="background-color:rgb(170, 202, 221);">
            <div class="card-body">
                <h5 class="card-title p-0"><strong>Treatment Recommendations</strong></h5><hr>
                <p class="card-text text-muted ps-3">
                  {{ $treatmentRecommendation }}
                </p>
            </div>
        </div>
        <div class="card mb-2 p-0" style="background-color:rgb(218, 180, 182);">
            <div class="card-body">
                <h5 class="card-title p-0">
                    <strong>System Status</strong> </h5><hr> 
                    <p class="text-muted">Initial disease indenfication complete. A technician has been automatically assigned for verification.</p>
                    <p class="card-text mb-0">
                    <strong>Detected Disease: </strong><span class="text-danger"><strong>{{$reported->disease}}</strong></span></p>
            </div>
        </div>
        <div class="card mb-2 p-0" style="background-color:rgb(178, 214, 178);">
            <div class="card-body">
                <h5 class="card-title p-0">
                    <strong>{{ $reported->farms->name}}</strong> </h5><hr>
                    <p class="card-text">
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
                <h5 class="card-title p-0"><strong>Observed Symptoms</strong></h5><hr>
                @if ($reported->symptoms)
                    <ul>
                        @foreach (is_array($reported->symptoms) ? $reported->symptoms : json_decode($reported->symptoms, true) as $symptom)
                            <li>{{ $symptomsMap[$symptom] ?? $symptom }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No symptoms reported</p>
                @endif
            </div>
        </div>
       
    </section>
</main>
@include('user.partials.__footer')

