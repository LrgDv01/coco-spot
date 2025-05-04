@include('admin.partials.__header')
@include('admin.partials.__nav')
<main id="main" class="main">
    <div class="pagetitle">
        <h1><strong>Simulation</strong></h1>
    </div>
    <section class="section Simulation mt-4 m-0 p-0">
        <div class="m-0 p-0">
            <!-- Control Card -->
            <div class="card position-absolute p-3 m-3 rounded rounded-3 bg-success bg-opacity-25 text-white" style="right:2%; width: 15rem; height: 9rem; z-index:10">
                <h5 class="text-center">LEGEND</h5>
                <hr class="mb-2 mt-0">
                <div class="d-flex flex-column justify-content-between">
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
            </div>
            <div class="card shadow-sm position-absolute rounded rounded-3" style="width: 25rem; height: 20rem; z-index: 1">
                <div class="card-body bg-success">
                    <div class="form-group mt-3">
                        <label for="disease" class="form-label fw-bold text-white">Type of Disease</label>
                        <select id="disease" class="form-select">
                            <option value="All_Diseases" disabled selected hidden>Select Disease</option>
                            <option value="All_Diseases">All Disease</option>
                            <option value="Stem Bleeding">Stem Bleeding</option>
                            <option value="Lethal Yellowing">Lethal Yellowing</option>
                            <option value="Leaf Spot">Leaf Spot</option>
                        </select>
                    </div>
                    <div id="description" class="card mt-4 mb-0 alert alert-info" style="height: 200px;">
                        <label class="alert-text text-center fw-bold">Descriptions</label>
                        <hr class="m-0 my-2">
                        <div id="description-text" class="card-body pe-2 p-0 m-" style="font-size: 13px; max-height: 200px; overflow-y: auto;">
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="condition" class="form-label fw-bold text-white">Conditions</label>
                        <select id="condition" class="form-select">
                            <option value="All_Conditions" disabled selected hidden>Select Condition</option>
                            <option value="All_Conditions">All Condition</option>
                            <option value="not_treated">Not Treated</option>
                            <option value="treated">Treated</option>
                        </select>
                    </div>
                    <div id="treatment" class="card mt-4 mb-0 alert alert-info" style="height: 200px;">
                        <label class="alert-text text-center fw-bold">Treatment Recommendations</label>
                        <hr class="m-0 my-2">
                        <div id="treatment-text" class="card-body pe-2 p-0 m-0" style="font-size: 13px; max-height: 200px; overflow-y: auto;">
                        </div>
                    </div>
                </div> 
                <div class="card-footer text-end">
                    <button id="simulate-button" class="btn btn-outline-danger fw-bold">Simulate</button>
                </div>
            </div>
            <div class="card position-absolute pb-3" style="bottom: 0%; left: 50rem; width: 50%; max-height: 7rem; z-index:10; overflow-y: auto;">
                <div class="date-display mt-3 mb-3 d-flex justify-content-between" style="width: 100%; max-width: 90%; margin: 0 auto;"></div> 
                <div class="progress-bar-container" style="width: 100%; max-width: 90%; height: 10px; background: #ddd; position: relative; margin: 0 auto;">
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
        const currentDate = new Date();
        const dateDisplay = document.querySelector('.date-display');
        dateDisplay.innerHTML = `<div class="day text-center fw-bold">All Diseases</div>`;
        document.getElementById('disease').addEventListener('change', (event) => {
            const selectedDisease = event.target.value;
            if (selectedDisease === 'Stem Bleeding') {
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const dayIndex = currentDate.getDay();
                const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)].slice(0, 3);
                const formattedDays = orderedDays.map((day, index) => {
                    const date = new Date(currentDate);
                    date.setDate(currentDate.getDate() + index);
                    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    return `<div class="day text-center">${day}, ${formattedDate}</div>`;
                });
                dateDisplay.innerHTML = formattedDays.join('');
            } else if (selectedDisease === 'Lethal Yellowing') {
                const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const monthIndex = currentDate.getMonth();
                const orderedMonths = [...months.slice(monthIndex), ...months.slice(0, monthIndex)].slice(0, 3);
                dateDisplay.innerHTML = orderedMonths.map(month => `<div class="month text-center">${month}</div>`).join('');
            } else if (selectedDisease === 'Leaf Spot') {
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'Monday', 'Tuesday'];
                const dayIndex = currentDate.getDay();
                const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];
                const formattedDays = orderedDays.map((day, index) => {
                    const date = new Date(currentDate);
                    date.setDate(currentDate.getDate() + index);
                    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    return `<div class="day text-center me-2">${day}, ${formattedDate}</div>`;
                });
                dateDisplay.innerHTML = formattedDays.join('');
                // dateDisplay.innerHTML = orderedDays.map(day => `<div class="day text-center">${day}</div>`).join(''); 
            } else {
                dateDisplay.innerHTML = `<div class="day text-center fw-bold">All Diseases</div>`;
            }
        });
    });
</script>
