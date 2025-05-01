@include('admin.partials.__header')
@include('admin.partials.__nav')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Simulation</h1>
    </div>
    <section class="section Simulation mt-4 m-0 p-0">
        <div class="container m-0 p-0">
            <!-- Control Card -->
            <div class="card position-absolute p-3 m-3 rounded rounded-3 bg-success bg-opacity-25 text-white" style="right:2%; width: 15rem; height: 9rem; z-index:10">
                <h5 class="text-center">LEGEND</h5>
                <div class="d-flex align-items-center mb-1">
                    <span class="rounded rounded-5" style="display: inline-block; width: 20px; height: 20px; background-color: #00FF00; margin-right: 5px;"></span>
                    <span>Healthy Farm </span>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <span class="rounded rounded-5 " style="display: inline-block; width: 20px; height: 20px; background-color: yellow; margin-right: 5px;"></span>
                    <span>Recovering Farm </span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="rounded rounded-5" style="display: inline-block; width: 20px; height: 20px; background-color: red; margin-right: 5px;"></span>
                    <span>Infected Farm </span>
                </div>
            </div>
            <div class="card shadow-sm position-absolute rounded rounded-3" style="width: 25rem; height: 20rem; z-index: 1">
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
                    <div id="description" class="card mt-4 mb-0 alert alert-info" style="height: 200px;">
                        <label class="alert-text text-center fw-bold">Descriptions</label>
                        <hr>
                        <div id="description-text" class="card-body pe-2 p-0 m-0" style="max-height: 200px; overflow-y: auto;">
                        </div>
                    </div>
                    <div id="treatment" class="card mt-4 mb-0 alert alert-info" style="height: 200px;">
                        <label class="alert-text text-center fw-bold">Treatment Recommendations</label>
                        <hr>
                        <div id="treatment-text" class="card-body pe-2 p-0 m-0" style="max-height: 200px; overflow-y: auto;">
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button id="simulate-button" class="btn btn-outline-danger fw-bold">Simulate</button>
                </div>
            </div>
            <div class="card position-absolute" style="bottom: 0%; left:35%;  width: 50rem; height: 5rem; z-index:10">
                <div class="week-display mt-3 mb-3 d-flex justify-content-between" style="width: 100%; max-width: 700px; margin: 0 auto;">
                </div>
                <div class="progress-bar-container" style="width: 100%; max-width: 700px; height: 10px; background: #ddd; position: relative; margin: 0 auto;">
                    <div id="week-progress" style="height: 100%; background: #28a745; width: 0%;"></div>
                </div>
            </div>
            <!-- Map Container -->
            <div id="map_simulation" style="flex-grow: 1; width: calc(100vw - 350px); height: calc(100vh - 200px);"></div>
        </div>
    </section>
</main>
@include('admin.partials.__footer')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const currentDate = new Date();
        const dayIndex = currentDate.getDay();
        const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];

        const weekDisplay = document.querySelector('.week-display');
        weekDisplay.innerHTML = orderedDays.map(day => `<div class="day text-center" style="flex: 1;">${day}</div>`).join('');
    });
</script>

