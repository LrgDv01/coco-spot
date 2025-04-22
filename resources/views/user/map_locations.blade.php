@include('user.partials.__header')
@include('user.partials.__nav')

<main id="main" class="main">
    <div class="pagetitle">
        <h1><Strong>Map</Strong></h1>
    </div>
    <section class="section map mt-3">
            <div id="drawer" class="card shadow-sm position-absolute" style="top: 25%; width: 20rem; height:auto; z-index: 11; left: -25rem; transition: left 0.3s;"> <!-- Adjust width -->
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
            <button id="drawer-toggle" class="btn btn-secondary position-absolute" style="top: 17%; left: 7%; z-index: 12;">
                <i class="fas fa-bars"></i>
            </button>
            <div id="map_locations" style="width: 100%; height: 700px;"></div>
  
    </section>
</main>

@include('user.partials.__footer')

<script>
      document.addEventListener('DOMContentLoaded', () => {
        const drawer = document.getElementById('drawer');
        const toggleButton = document.getElementById('drawer-toggle');
        const simulateButton = document.getElementById('simulate-button');

        if (drawer && toggleButton) {
            let toggleIcon = toggleButton.querySelector('i');
            toggleButton.addEventListener('click', () => {
                if (drawer.style.left === '0px') {
                    drawer.style.left = '-25rem';
                    if (toggleIcon) toggleIcon.className = 'fas fa-bars';
                } else {
                    drawer.style.left = '0px';
                    if (toggleIcon) toggleIcon.className = 'fas fa-times';
                }
            });
        }
        if (simulateButton) {
            simulateButton.addEventListener('click', () => {
                if (drawer.style.left === '0px') {
                    drawer.style.left = '-25rem';
                } else {
                    drawer.style.left = '0px';
                }
            });
        }
    });
</script>