@include('admin.partials.__header')
@include('admin.partials.__nav')

<main id="main" class="main">

    <div class="pagetitle">
        <h1><Strong>Map</Strong></h1>
    </div>
    <section class="section map mt-4">
        <div class="container d-flex justify-content-between">  
            <div class="card shadow-sm" style="width: 20rem; height:auto;"> <!-- Adjust width -->
                <div class="card-header bg-success text-white d-flex justify-content-center">
                    <h5 class="mb-0"><strong>Municipalities</strong></h5>
                </div>
                <div class="card-body">
                    <!-- Dropdown for Location Selection -->
                    <div class="form-group mt-3">
                        <!-- <label for="location" class="form-label">Choose a Location:</label> -->
                        <select id="location" class="form-select">
                        <option value="All Municipal"selected >All Municipal</option>
                        </select>
                    </div>
                    <!-- Area to Display Selected Location Details -->
                    <div id="details" class="mt-4 alert alert-info" style="display: none;">
                        <h5 class="alert-heading">Location Details</h5>
                        <hr>
                        <p><strong>Location:</strong> <span id="name"></span> municipality</p>
                        <p><strong>Lot Area:</strong> <span id="lot-area"></span> hectares</p>
                        <p><strong>Number of Trees:</strong> <span id="number-of-trees"></span></p>
                        <p><strong>Meters:</strong> <span id="meters"></span> meters</p>
                    </div>
                </div>
            </div>
            <div id="map_locations" style="width: 70%; height: 700px;"></div>
        </div>
    </section>
</main>

@include('admin.partials.__footer')
