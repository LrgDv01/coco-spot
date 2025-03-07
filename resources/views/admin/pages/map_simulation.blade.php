@include('admin.partials.__header')
@include('admin.partials.__nav')

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Simulation</h1>
    </div>
    <section class="section Simulation mt-4">
        <div class="container" >
            <div class="card shadow-sm position-absolute bg-success rounded rounded-3" style="width: 20rem; height: 20rem; z-index:1"> 
                <div class="card-body">
                    <div class="form-group mt-3">
                        <label for="variety" class="form-label fw-bold">Type of Disease</label>
                        <select id="location" class="form-select">
                            <option value="All Municipal"selected >Select Disease</option>
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
                <div class="card-footer text-end">
                    <button class="btn btn-outline-success">Simulate</button>
                </div>
            </div>
            <div id="map_simulation" style="width: 100%; height: 700px;"></div>
        </div>
    </section>

</main>

@include('admin.partials.__footer')
