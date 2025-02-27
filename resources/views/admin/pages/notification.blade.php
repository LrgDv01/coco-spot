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
            <div class="card-body">
                <h5 class="card-title pb-0"><strong>Observed Symptoms</strong> </h5><hr>
                <ul>
                @foreach (json_decode($notification->symptoms, true) as $symptom)
                        <li>{{ $symptom }}</li>
                    @endforeach
                </ul> 
            </div>
        </div>
    </section>
</main>

@include('admin.partials.__footer')
