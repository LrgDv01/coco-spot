@include('admin.partials.__header')
@include('admin.partials.__nav')

<main id="main" class="main">
    <div class="pagetitle">
        <h1><Strong>Map</Strong></h1>
    </div>
    <section class="section map mt-4">
        <div class="container d-flex flex-row justify-content-between">  
            <div class="d-flex flex-column align-items-center" style="width: 30%; height: 700px;">
                <div class="card shadow-sm" style="width: 20rem; height: auto;"> 
                    <div class="card-header bg-success text-white d-flex justify-content-center">
                        <h5 class="mb-0"><strong>Filter</strong></h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mt-3">
                            <select id="location" class="form-select">
                                <option value="All Municipal"selected >All Municipal</option>
                            </select>
                        </div>
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
                <div class="card shadow-sm" style="width: 20rem; height:auto;">
                    <div class="card-header bg-success text-white d-flex justify-content-center">
                        <h5 class="mb-0"><strong>Farm Details</strong></h5>
                    </div>
                    <div class="card-body">
                        <div id="details" class="mt-4 alert alert-info">
                            <p><strong>Farm Name:</strong> <span id="farm_name"></span> </p>
                            <p><strong>Location:</strong> <span id="address"></span> </p>
                            <p><strong>District:</strong> <span id="district"></span></p>
                            <p><strong>Hectares:</strong> <span id="hectares"></span> </p>
                            <p><strong>Tree Age:</strong> <span id="tree_age"></span> </p>
                            <p><strong>Planted Coconut:</strong> <span id="planted_coconuts"></span> </p>
                            <p><strong>Variety:</strong> <span id="variety"></span> </p>
                            {{-- <p><strong>Soil Type:</strong> <span id="soil_type"></span> </p>  --}}
                        </div>
                    </div>
                </div>
            </div>
            <div id="map_locations" style="width: 70%; height: 700px;"></div>
        </div>
    </section>
</main>
@include('admin.partials.__footer')
 