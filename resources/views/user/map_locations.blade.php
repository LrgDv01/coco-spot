@include('user.partials.__header')
@include('user.partials.__nav')

<main id="main" class="main px-0">
    <div class="pagetitle ms-3">
    <h1 class="text-center"><Strong>Map</Strong></h1>
    </div>
    <section class="section map mt-3">
            <button id="drawer-toggle" class="btn btn-success position-absolute ms-3" style="top: 22%; z-index: 10;">
                <i class="fas fa-bars"></i>
            </button>
    
        <div id="drawer" class="position-absolute" style="top: 19%; width: 100%; height:30rem; z-index: 11; left: -30rem; transition: left 0.3s;"> <!-- Adjust width -->
            <div class="d-flex flex-row">
                <div class="card shadow-sm mb-2" style="width: 90%; height:auto;">
                    <div class="card-header bg-success text-white d-flex justify-content-center">
                        <h5 class="mb-0"><strong>Filter</strong></h5>
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
                <button id="drawer-toggle-close" class="btn btn-success ms-2 align-self-start">
                        <i class="fas fa-times fs-2" id="toggle-icon"></i>
                    </button>
            </div>
            <div class="card shadow-sm" style="width: 100%; height:auto;">
                    <div class="card-header bg-success text-white d-flex justify-content-center">
                        <h5 class="mb-0"><strong>Farm Details</strong></h5>
                    </div>
                    <div class="card-body mb-0 pb-0">
                        <div id="details" class="mt-2 alert alert-info">
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
            <div id="map_locations" style="width: 100%; height: 80vh;"></div>
        </div>
    </section>
</main>

@include('user.partials.__footer')

<script>
      document.addEventListener('DOMContentLoaded', () => {
        const drawer = document.getElementById('drawer');
        const toggleButton = document.getElementById('drawer-toggle');
        const toggleButtonClose = document.getElementById('drawer-toggle-close');

        if ((drawer && toggleButton) || (drawer && toggleButtonClose)) {
            let toggleIcon = toggleButton.querySelector('i');
            toggleButton.addEventListener('click', () => {
                if (drawer.style.left === '0px') {
                    drawer.style.left = '-30rem';
                    if (toggleIcon) toggleIcon.className = 'fas fa-bars';
                } else {
                    drawer.style.left = '0px';
                    if (toggleIcon) toggleIcon.className = 'fas fa-times';
                }
            });
            toggleButtonClose.addEventListener('click', () => {
                if (drawer.style.left === '0px') {
                    drawer.style.left = '-30rem';
                    // if (toggleIcon) toggleIcon.className = 'fas fa-bars';
                } else {
                    drawer.style.left = '0px';
                    // if (toggleIcon) toggleIcon.className = 'fas fa-times';
                }
            });
        }
    });
</script>