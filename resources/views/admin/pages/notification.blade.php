@include('admin.partials.__header')
@include('admin.partials.__nav')

<main id="main" class="main">
    <div class="pagetitle mb-5">
        <h1><strong>Notification</strong></h1>
    </div>
    <section class="section notications">
        <div class="card mb-3 p-0" style="background-color:rgb(178, 214, 178);">
            <div class="card-body">
                <h5 class="card-title pb-0">
                    <strong>Farmer Details</strong> </h5><hr>
                    <p class="card-text text-truncate">
                    <strong>Farmer Name :</strong> {{ $notification->farmer_name ?? 'John' }}<br>
                    <strong>Contact Information :</strong> {{ $notification->contact_no }}<br>
                    <strong>Farm Location :</strong> {{ $notification->farm_location }}<br>
                    <strong>Farm Size :</strong> {{ $notification->farm_size }}<br>
                    <strong>No. of Coconut Trees :</strong> {{ $notification->coconut_trees }}<br>
                    <strong>Coconut Variety :</strong> {{ $notification->coconut_variety }}<br>
                </p>
            </div>
        </div>
        <div class="card mb-3 p-0" style="background-color:rgb(218, 180, 182);">
            <div class="card-body">
                <h5 class="card-title pb-0">
                    <strong>System Status</strong> </h5><hr>
                    <p class="text-muted">Initial disease indenfication complete. A technician has been automatically assigned for verification.</p>
                    <p class="card-text text-truncate fs-5 mb-0">
                    <strong>Detected Disease:</strong></p>
                    <h4 class="card-text text-center ms-4 pt-0 text-danger"><strong>{{$notification->disease}}</strong></h4>
            </div>
        </div>
        <div class="card mb-3 p-0" style="background-color:rgb(170, 202, 221);">
         {{--   <div class="card-body">
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
                <h5 class="card-title pb-0"><strong>Observed Symptoms</strong> </h5><hr>
                <ul>
                    @foreach (json_decode($notification->symptoms, true) as $symptom)
                        <li>{{ $symptom }}</li>
                    @endforeach
                </ul> 
            </div>--}}

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
                <h5 class="card-title pb-0"><strong>Observed Symptoms</strong></h5><hr>
                @if ($notification->symptoms)
                    <ul>
                        @foreach (is_array($notification->symptoms) ? $notification->symptoms : json_decode($notification->symptoms, true) as $symptom)
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

@include('admin.partials.__footer')
