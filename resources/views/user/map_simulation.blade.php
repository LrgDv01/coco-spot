@include('user.partials.__header')
@include('user.partials.__nav')

<main id="main" class="main px-0">
    <div class="pagetitle ms-3">
        <h1 class="text-center"><strong>Simulation</strong></h1>
    </div>
    <section class="section Simulation mt-3 m-0 p-0">
        <button id="drawer-toggle" class="btn btn-success position-absolute ms-2" 
            style="top: 22%; z-index: 12;">
            <i class="fas fa-bars" id="toggle-icon"></i>
        </button>
        <div class="card position-absolute p-2 m-2 m-2 rounded rounded-3 bg-secondary bg-opacity-75 text-white" 
                style="right:0%; width: 8rem; height: 7rem; z-index:10; font-size: 11px;">
            <label class="text-center fw-bold">LEGEND</label>
            <hr class="m-1">
            <div class="d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-1">
                    <span class="rounded rounded-5" style="display: inline-block; width: 15px; height: 13px; background-color: #00FF00; margin-right: 5px;"></span>
                    <span>Healthy Farm </span>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <span class="rounded rounded-5 " style="display: inline-block; width: 15px; height: 13px; background-color: yellow; margin-right: 5px;"></span>
                    <span>Recovering Farm </span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="rounded rounded-5" style="display: inline-block; width: 15px; height: 13px; background-color: red; margin-right: 5px;"></span>
                    <span>Infected Farm </span>
                </div>
            </div>
        </div>
        <div id="drawer" class="card shadow-sm position-absolute rounded rounded-3" 
                style="width: 100%; max-width: 25rem; height: auto; z-index: 13; top: 19%; left: -30rem; transition: left 0.3s;">
            <button id="drawer-toggle-close" class="btn btn-success position-absolute end-0">
                <i class="fas fa-times fs-2" id="toggle-icon"></i>
            </button>
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
 
                {{--                <div class="d-flex flex-column justify-content-between">
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
                </div> --}}

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
            <div class="card-footer text-end bg-white">
                <button id="simulate-button" class="btn btn-outline-danger fw-bold">Simulate</button>
            </div>
        </div>
        <div class="card position-absolute p-1 w-100 mx-2 rounded rounded-3" style="bottom: 0; left: 0; right: 0; width: 97%; max-width: 97vw; z-index:10; overflow-y: auto; border-radius: 0;">
            <div class="date-display mt-0 mb-3 d-flex justify-content-between flex-wrap responsive-date-display" style="width: 100%; max-width: 100vw; margin: 0 auto;"></div> 
            <div class="progress-bar-container mb-2" style="width: 95%; height: 10px; background: #ddd; position: relative; margin: 0 auto;">
                <div id="week-progress" style="height: 100%; background: #28a745; width: 0%;"></div>
            </div>
        </div>
        
        <!-- Map Container -->
        <div id="map_simulation" class="w-100" style="flex-grow: 1; min-height: 300px; height: 80vh; max-height: 80vh;"></div>

        {{--         <!-- Map Container -->
        <div id="map_simulation" style="width: 100%; height: 80vh;">    
            <div class="card position-absolute" style="font-size: 10px; bottom: -4%; width: 100%; height: 5rem; z-index:11">
                <div class="week-display mt-3 mb-3 d-flex justify-content-between" style="width: 90%; max-width: 700px; margin: 0 auto;"></div>
                <div class="progress-bar-container mx-3" style="width: 90%; height: 10px; background: #ddd; position: relative; margin: 0 auto;">
                    <div id="week-progress" style="height: 100%; background: #28a745; width: 0%;"></div>
                </div>
            </div>
        </div> --}}
    </section>
</main>

<style>
    #drawer.open {
        left: 0 !important; /* Override inline style */
    }

    @media (max-width: 768px) {
    .card.position-absolute.pb-3.w-100 {
        max-width: 100vw !important;
        left: 0 !important;
        right: 0 !important;
        border-radius: 0 !important;
        padding-bottom: 1rem !important;
    }
    .date-display.mt-3.mb-3.d-flex.justify-content-between.flex-wrap {
        font-size: 12px;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 4px;
    }
    .progress-bar-container {
        width: 98% !important;
    }
    /* Responsive font size for date-display on tablets */
    .responsive-date-display {
        font-size: clamp(9px, 1.5vw, 17px);
    }
    /* Responsive styling for Leaf Spot days */
    .leaf-spot-day {
        flex: 1 1 0;
        min-width: 0;
        font-size: clamp(7px, 2vw, 13px);
        white-space: nowrap;
        /* Show full text, allow horizontal scroll if needed */
        overflow: visible;
        text-overflow: unset;
        margin-right: 2px;
    }
    }
    @media (max-width: 480px) {
    .date-display.mt-3.mb-3.d-flex.justify-content-between.flex-wrap {
        font-size: 10px;
    }
    .progress-bar-container {
        width: 99% !important;
    }
    /* Responsive font size for date-display on mobile */
    .responsive-date-display {
        font-size: clamp(8px, 4vw, 13px);
    }
    .leaf-spot-day {
        font-size: clamp(6px, 4vw, 10px);
        overflow: visible;
        text-overflow: unset;
    }
    }
    .leaf-spot-row {
        width: 100%;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        margin-bottom: 0;
        gap: 0;
    }
    .leaf-spot-day, .leaf-spot-date {
        flex: 1 1 0;
        min-width: 0;
        font-size: clamp(8px, 1vw, 14px);
        white-space: nowrap;
        overflow: visible;
        text-overflow: unset;
        margin-right: 2px;
    }
    .leaf-spot-date {
        color: #888;
        font-size: clamp(7px, 1.5vw, 12px);
        margin-top: 0;
    }
    @media (max-width: 768px) {
        .leaf-spot-day, .leaf-spot-date {
            font-size: clamp(8px, 2vw, 12px);
        }
        .leaf-spot-date {
            font-size: clamp(8px, 1.5vw, 12px);
        }
    }
    @media (max-width: 480px) {
        .leaf-spot-day, .leaf-spot-date {
            font-size: clamp(6px, 3vw, 10px);
        }
        .leaf-spot-date {
            font-size: clamp(6px, 2vw, 10px);
        }
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

    // document.addEventListener('DOMContentLoaded', () => {
    //     const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    //     const currentDate = new Date();
    //     const dayIndex = currentDate.getDay();
    //     const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];

    //     const weekDisplay = document.querySelector('.week-display');
    //     weekDisplay.innerHTML = orderedDays.map(day => `<div class="day text-center" >${day}</div>`).join('');
    // });

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
                // Only show 7 days for horizontal fit
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const dayIndex = currentDate.getDay();
                const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];
                // Split into two rows: day names and dates
                const dayNames = orderedDays.map(day => `<div class="leaf-spot-day text-center">${day}</div>`).join('');
                const dayDates = orderedDays.map((_, index) => {
                    const date = new Date(currentDate);
                    date.setDate(currentDate.getDate() + index);
                    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    return `<div class="leaf-spot-date text-center">${formattedDate}</div>`;
                }).join('');
                dateDisplay.innerHTML = `
                    <div class="d-flex flex-row w-100 leaf-spot-row">${dayNames}</div>
                    <div class="d-flex flex-row w-100 leaf-spot-row">${dayDates}</div>
                `;
            } else {
                dateDisplay.innerHTML = `<div class="day text-center fw-bold">All Diseases</div>`;
            }
        });
    });
</script>
<script>
    // document.addEventListener('DOMContentLoaded', () => {
    //     const currentDate = new Date();
    //     const dateDisplay = document.querySelector('.date-display');
    //     dateDisplay.innerHTML = `<div class="day text-center fw-bold">All Diseases</div>`;
    //     document.getElementById('disease').addEventListener('change', (event) => {
    //         const selectedDisease = event.target.value;
    //         if (selectedDisease === 'Stem Bleeding') {
    //             const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    //             const dayIndex = currentDate.getDay();
    //             const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)].slice(0, 3);
    //             const formattedDays = orderedDays.map((day, index) => {
    //                 const date = new Date(currentDate);
    //                 date.setDate(currentDate.getDate() + index);
    //                 const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    //                 return `<div class="day text-center">${day}, ${formattedDate}</div>`;
    //             });
    //             dateDisplay.innerHTML = formattedDays.join('');
    //         } else if (selectedDisease === 'Lethal Yellowing') {
    //             const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    //             const monthIndex = currentDate.getMonth();
    //             const orderedMonths = [...months.slice(monthIndex), ...months.slice(0, monthIndex)].slice(0, 3);
    //             dateDisplay.innerHTML = orderedMonths.map(month => `<div class="month text-center">${month}</div>`).join('');
    //         } else if (selectedDisease === 'Leaf Spot') {
    //             const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'Monday', 'Tuesday'];
    //             const dayIndex = currentDate.getDay();
    //             const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];
    //             const formattedDays = orderedDays.map((day, index) => {
    //                 const date = new Date(currentDate);
    //                 date.setDate(currentDate.getDate() + index);
    //                 const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    //                 return `<div class="day text-center me-2">${day}, ${formattedDate}</div>`;
    //             });
    //             dateDisplay.innerHTML = formattedDays.join('');
    //             // dateDisplay.innerHTML = orderedDays.map(day => `<div class="day text-center">${day}</div>`).join(''); 
    //         } else {
    //             dateDisplay.innerHTML = `<div class="day text-center fw-bold">All Diseases</div>`;
    //         }
    //     });
    // });
</script>
