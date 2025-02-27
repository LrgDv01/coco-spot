@include('user.partials.__header')
@include('user.partials.__nav')

<main id="main" class="main">
    <section class="section reports">
        <div class="position-fixed bg-white d-flex align-items-end justify-content-center" 
             style="top:5%; right:0; left:0; z-index: 995; height: 80px; width: 100%;">
            <h2 class="py-2 mt-2"><strong>Reports</strong></h2>
        </div>
        <div style="margin-top: 100px;"></div>
        @foreach($farms as $farm)
            <div class="card mb-2 p-0" style="background-color: #f0f8f0; border: 1px solid #28a745;">
                <a href="{{ route('user.submit-report', $farm->id) }}" class="btn btn-outline-success" >
                    <div class="card-body text-start">
                        <h5 class="card-title p-0 d-flex flex-row justify-content-between">
                            <strong>{{ $farm->name }}</strong> </h5><hr>
                        <p class="card-text text-truncate">
                            <strong>Address :</strong> {{ $farm->address }}<br>
                            <strong>Variety :</strong> {{ $farm->variety }}<br>
                            <strong>Hectares :</strong> {{ $farm->hectares }}<br>
                            <strong>Tree Age :</strong> {{ $farm->tree_age }}<br>
                            <strong>Planted Coconut :</strong> {{ $farm->planted_coconut }}<br>
                        </p>
                    </div>
                </a>
            </div>
        @endforeach
    </section>
</main>

@include('user.partials.__footer')


