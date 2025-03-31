@include('admin.partials.__header')
@include('admin.partials.__nav')

<style>
    .card.green-card {
        background-color: #134125;
        border-radius: 15px;    
        color: white;
    }
    .card.yellow-card {
        background-color: lightyellow;
        border-radius: 15px;    
        color: #134125;
    }
    .card.green-card h5 {
        color:white;
    }
    .card.yellow-card h5 {
        color:#134125;
    }
    .card #count_today_technicians,
    .card #count_today_farmers,
    .card #affected_rate,
    .card #count_healthy_farms,
    .card #count_correct_diagnosis {
        color: white;
    }
    .card #count_today_farms,
    .card #count_treated_farms,
    .card #count_infected_farms,
    .card #count_incorrect_diagnosis {
        color: #134125;
    }
    
    .card.green-card i.bi-graph-up-arrow, .card.yellow-card i.bi-x-circle-fill {
        color:red;
    } 
    .card.yellow-card i.bi-bug-fill  {
        color:brown;
    } 

    .card.green-card i {
        color:green;
    } 
    .card.yellow-card i {
        color:#134125;
    } 
    .card.yellow-card .card-icon {
        /* background-color: #134125; */
    }

    .card.disease-chart, .variety-chart {
        background-color:lightyellow;
        border-radius: 15px; 
    }
</style>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Dashboard</h1>
    </div>
    <section class="section dashboard mt-4">
        <div class="row">
            <div class="col-lg-2">
                <div class="card info-card cardiconbg green-card">
                    <div class="card-body">
                        <h5 class="card-title">Check <span>| for</span></h5>
                        <div id="checkboxList">
                            <label><input type="checkbox" id="monthlyFilter"> Monthly</label><br>
                            <label><input type="checkbox" id="yearlyFilter"> Yearly</label><br>
                        </div>
                    </div>
                </div>
                <div class="card info-card cardiconbg green-card">
                    <div class="card-body">
                        <h5 class="card-title">Check <span>| for</span></h5>
                        <div id="checkboxList">
                            <label><input type="checkbox" id="district3Filter"> District lll</label><br>
                            <label><input type="checkbox" id="district4Filter"> District lV </label><br>
                        </div>
                    </div>
                </div>
                <div class="card info-card cardiconbg green-card">
                    <div class="card-body">
                        <h5 class="card-title">Municipal <span>| list</span></h5>
                        <div id="municipalList">
                            <ul>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card info-card cardiconbg green-card">
                            <div class="card-body">
                                <h5 class="card-title"><span>Today's | </span>Farmers</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 id="count_today_farmers">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card info-card cardiconbg yellow-card">
                            <div class="card-body">
                                <h5 class="card-title"><span>Today's | </span>Farms</h5>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-tree-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 id="count_today_farms">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card info-card cardiconbg green-card">
                            <div class="card-body">
                                <h5 class="card-title"><span>Today's | </span>Technicians</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="ps-3 d-flex flex-row">
                                        <h6 id="count_today_technicians">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <div class="col-lg-3 mb-3">
                            <select id="diseaseYearSelect" class="form-select fw-bold">
                                <option value="all" selected place-holder="">Select Year</option> 
                            </select>
                        </div>
                        <div class="card disease-chart">
                            <div class="card-body p-3">
                              {{--  <div style="width: 100%; margin: auto;">
                                    <canvas id="diseases_chart"></canvas>
                                </div>--}}
                                <div id="chart" style="width: 100%; height:auto; margin: auto; ">
                                    <canvas id="diseases_chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <div class="row">
                            <div class="col-lg-3 text-end mb-3">
                                <select id="cocoYearSelect" class="form-select fw-bold">
                                    <option value="all" place-holder="">Select Year</option> 
                                </select>
                            </div>
                        </div>
                        <div class="card variety-chart">
                            <div class="card-body p-3">
                            <div id="chart" style="width: 100%; height:auto; margin: auto; ">
                                    <canvas id="coconut_chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card info-card cardiconbg green-card">
                            <div class="card-body">
                                <h5 class="card-title"><span>Today's | </span>Correct Diagnosis</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 id="count_correct_diagnosis">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card info-card cardiconbg yellow-card">
                            <div class="card-body">
                                <h5 class="card-title"><span>Today's | </span>Incorrect Diagnosis</h5>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 id="count_incorrect_diagnosis">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card info-card cardiconbg green-card">
                            <div class="card-body">
                                <h5 class="card-title"><span>Today's | </span>Healthy Farms</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </div>
                                    <div class="ps-3 d-flex flex-row">
                                        <h6 id="count_healthy_farms">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card info-card cardiconbg yellow-card">
                            <div class="card-body">
                                <h5 class="card-title"><span>Today's | </span>Infected Farms</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-bug-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 id="count_infected_farms">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card info-card cardiconbg green-card"> 
                            <div class="card-body">
                                <h5 class="card-title"><span>Today's | </span>Affected Rate</h5>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-graph-up-arrow"></i>
                                    </div>
                                    <div class="ps-3"> 
                                        <h6 id="affected_rate">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card info-card cardiconbg yellow-card">
                            <div class="card-body">
                                <h5 class="card-title"><span>Today's | </span>Treated Farms</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-patch-check-fill"></i>
                                    </div>
                                    <div class="ps-3 d-flex flex-row">
                                        <h6 id="count_treated_farms">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@include('admin.partials.__footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const district3Municipals = [
            'San Pablo', 'Alaminos', 'Calauan', 'Liliw', 'Nagcarlan', 'Rizal', 'Victoria'
        ]; 
        const district4Municipals = [
            'Cavinti', 'Famy', 'Kalayaan', 'Luisiana', 'Paete', 'Pagsanjan', 'Pakil', 'Lumban',
            'Mabitac', 'Pangil', 'Pila', 'Santa Cruz', 'Magdalena', 'Santa Maria', 'Majayjay', 'Siniloan'
        ];
        const district3Filter = document.getElementById('district3Filter');
        const district4Filter = document.getElementById('district4Filter');
        const municipalList = document.getElementById('municipalList').querySelector('ul');
        function updateMunicipalList(municipals) {
            municipalList.innerHTML = '';
            municipals.sort().forEach(municipal => {
                const li = document.createElement('li');
                li.textContent = municipal;
                municipalList.appendChild(li);
            });
        }
        updateMunicipalList([...district3Municipals, ...district4Municipals]);
        district3Filter.addEventListener('change', function () {
            if (this.checked) {
                district4Filter.checked = false;
                updateMunicipalList(district3Municipals);
            } else {
                updateMunicipalList([...district3Municipals, ...district4Municipals]);
            }
        });
        district4Filter.addEventListener('change', function () {
            if (this.checked) {
                district3Filter.checked = false;
                updateMunicipalList(district4Municipals);
            } else {
                updateMunicipalList([...district3Municipals, ...district4Municipals]);
            }
        });
    });
</script>