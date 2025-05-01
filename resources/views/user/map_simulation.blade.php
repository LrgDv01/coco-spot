@include('user.partials.__header')
@include('user.partials.__nav')

<main id="main" class="main px-0">
    <div class="pagetitle ms-3">
        <h1 class="text-center"><strong>Simulation</strong></h1>
    </div>
    <section class="section Simulation mt-4 m-0 p-0">
    {{--             <button id="drawer-toggle" class="btn btn-success position-absolute" style="top: 20%; left: 7%; z-index: 12;">
                <i class="fas fa-bars"></i>
            </button>  --}}

            <button id="drawer-toggle" class="btn btn-success position-absolute ms-2" 
        style="top: 22%; z-index: 12;">
    <i class="fas fa-bars" id="toggle-icon"></i>
</button>
            <div class="card position-absolute p-2 m-2 m-2 rounded rounded-3 bg-secondary bg-opacity-75 text-white" 
                    style="right:0%; width: 8rem; height: 7rem; z-index:10; font-size: 11px;">
                <label class="text-center fw-bold">LEGEND</label>
                <hr class="m-1">
                <div class="d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        <span class="rounded rounded-5" style="display: inline-block; width: 15px; height: 13px; background-color: #00FF00; margin-right: 5px;"></span>
                        <span>Healthy Farm </span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="rounded rounded-5 " style="display: inline-block; width: 15px; height: 13px; background-color: yellow; margin-right: 5px;"></span>
                        <span>Recovering Farm </span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="rounded rounded-5" style="display: inline-block; width: 15px; height: 13px; background-color: red; margin-right: 5px;"></span>
                        <span>Infected Farm </span>
                    </div>
                </div>
            
            </div>
            {{--             <div id="drawer" class="card shadow-sm position-absolute rounded rounded-3" style="width: 100%; max-width: 25rem; height: auto; z-index: 12; top: 27%; left: -25rem; transition: left 0.3s;">
                --}}
                <div id="drawer" class="card shadow-sm position-absolute rounded rounded-3" 
                style="width: 100%; max-width: 25rem; height: auto; z-index: 13; top: 19%; left: -30rem; transition: left 0.3s;">

                <button id="drawer-toggle-close" class="btn btn-success position-absolute end-0">
                    <i class="fas fa-times fs-2" id="toggle-icon"></i>
                </button>
            <div class="card-body bg-success">
                    <div class="form-group mt-3">
                        <label for="disease" class="form-label fw-bold text-white">Type of Disease</label>
                        <select id="disease" class="form-select">
                            <option value="All" selected>Select Disease</option>
                            <option value="no_treatment">No Treatment</option>
                            <option value="Stem Bleeding">Stem Bleeding</option>
                            <option value="Lethal Yellowing">Lethal Yellowing</option>
                            <option value="Leaf Spot">Leaf Spot</option>
                        </select>
                    </div>
                    <div class="d-flex flex-column justify-content-between">
                        <div id="description" class="card mt-4 mb-0 alert alert-info me-3" style="width: 100%; height: auto; max-height: 200px;">
                            <label class="alert-text text-center fw-bold">Descriptions</label>
                            <hr>
                            <div id="description-text" class="card-body pe-2 p-0 m-0" style="max-height: 200px; overflow-y: auto;">
                            </div>
                        </div>
                        <div id="treatment" class="card mt-4 mb-0 alert alert-info" style="width: 100%; height: auto; max-height: 200px;">
                            <label class="alert-text text-center fw-bold">Treatment Recommendations</label>
                            <hr>
                            <div id="treatment-text" class="card-body pe-2 p-0 m-0" style="max-height: 200px; overflow-y: auto;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end bg-white">
                    <button id="simulate-button" class="btn btn-outline-danger fw-bold">Simulate</button>
                </div>
            </div>
      
            <div class="card position-absolute" style="font-size: 10px; bottom: 0%; width: 100%; height: 5rem; z-index:11">
                <div class="week-display mt-3 mb-3 d-flex justify-content-between" style="width: 90%; max-width: 700px; margin: 0 auto;">
                </div>
                <div class="progress-bar-container mx-3" style="width: 90%; height: 10px; background: #ddd; position: relative; margin: 0 auto;">
                    <div id="week-progress" style="height: 100%; background: #28a745; width: 0%;"></div>
                </div>
            </div>
            <!-- Map Container -->
            <div id="map_simulation" style="width: 100%; height: 60vh;"></div>
    </section>
</main>
<style>
#drawer.open {
    left: 0 !important; /* Override inline style */
}
</style>
@include('user.partials.__footer')

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const drawer = document.getElementById('drawer');
        const toggleButton = document.getElementById('drawer-toggle');
        const toggleButtonClose = document.getElementById('drawer-toggle-close');
        const simulateButton = document.getElementById('simulate-button');
        // const diseaseSelect = document.getElementById('disease');

        if ((drawer && toggleButton) || (drawer && toggleButtonClose)) {
            // let toggleIcon = toggleButton.querySelector('i');
            toggleButton.addEventListener('click', () => {
                if (drawer.style.left === '0px') {
                    drawer.style.left = '-30rem';
                    // if (toggleIcon) toggleIcon.className = 'fas fa-bars';
                } else {
                    drawer.style.left = '0px';
                    // if (toggleIcon) toggleIcon.className = 'fas fa-times';
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
        if (simulateButton) {
            simulateButton.addEventListener('click', () => {
                if (drawer.style.left === '0px') {
                    drawer.style.left = '-30rem';
                } else {
                    drawer.style.left = '0px';
                }
            });
        }

        // if (diseaseSelect) {
        //     diseaseSelect.addEventListener('change', () => {
        //         if (drawer.style.left === '0px') {
        //             drawer.style.left = '-25rem';
        //         }
        //     });
        // }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const currentDate = new Date();
        const dayIndex = currentDate.getDay();
        const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];

        const weekDisplay = document.querySelector('.week-display');
        weekDisplay.innerHTML = orderedDays.map(day => `<div class="day text-center" >${day}</div>`).join('');
    });
</script>

