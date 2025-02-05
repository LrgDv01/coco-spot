@include('admin.partials.__header')
@include('admin.partials.__nav')


<style>
    .card.green-card {
        background-color: #134125;
        border-radius: 15px;    
        color: white;
    }
    .card h5 {
        color:white;
    }
    .card #count_today_total_cost,
          #count_today_farmers,
          #count_today_farms,
          #count_doa {
            color:white;
    }
    .card i {
        color:green;
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
            <div class="col-lg-3 mb-1">
                <div class="col-lg-8">
                    <div class="card info-card cardiconbg green-card">
                        <div class="card-body">
                            <h5 class="card-title">Check <span>| for</span></h5>
                            <div id="checkboxList">
                                <label><input type="checkbox" id="monthlyFilter"> Monthly</label><br>
                                <label><input type="checkbox" id="yearlyFilter"> Yearly</label><br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mb-3">
                    <div class="card info-card cardiconbg green-card">
                        <div class="card-body">
                            <h5 class="card-title">Check <span>| for</span></h5>
                            <div id="checkboxList">
                                <label><input type="checkbox" id="district3Filter"> District lll</label><br>
                                <label><input type="checkbox" id="district4Filter"> District lV </label><br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mb-3">
                    <div class="card info-card cardiconbg green-card">
                        <div class="card-body">
                            <h5 class="card-title">Municipality <span>| list</span></h5>
                            <div id="checkboxList">
                                <label> San Pablo</label><br>
                                <label> Caluan</label><br>
                                <label> Liliw</label><br>
                                <label> Nagcarlan</label><br>
                                <label> Rizal</label><br>
                                <label> Victoria</label><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col">
                <div class="row">
                    <div class="col-lg-3 mb-3">
                        <!-- <div class="row">  -->
                            <!-- <h4>Login and Appointment</h4> -->
                            <div class="card info-card cardiconbg green-card">
                                <div class="card-body">
                                    <h5 class="card-title">Cost <span>| Today</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="fa-solid fa-peso-sign text-success"></i>
                                        </div>
                                        <div class="ps-3 d-flex flex-row">
                                            <h6 id="count_today_total_cost">0</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- </div>      -->
                    </div>
                    <div class="col-lg-3 mb-3">
                        <!-- <div class="row">  -->
                            <div class="card info-card cardiconbg green-card">
                                <div class="card-body">
                                    <h5 class="card-title">Farmers <span>| Today</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 id="count_today_farmers">0</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>

                    <div class="col-lg-3 mb-3">
                        <!-- <div class="row">  -->
                            <div class="card info-card cardiconbg green-card">
                                <div class="card-body">
                                    <h5 class="card-title">Farms <span>| Today</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi-tree"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 id="count_today_farms">0</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>
                    <div class="col-lg-3 mb-3">
                        <!-- <div class="row">  -->
                            <div class="card info-card cardiconbg green-card">
                                <div class="card-body">
                                    <h5 class="card-title">DA <span>| Today</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 id="count_doa">0</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>

                </div>

                <div class="row">
                    <div class="col-lg-8 mb-3">
                        <div class="row">
                            <div class="col-lg-8 d-flex align-items-center"><h4>Diseases</h4></div>
                            <div class="col-lg-3 text-start mb-3">
                                <!-- Dropdown for selecting a specific year -->
                                <select id="diseaseYearSelect" class="form-select">
                                    <option value="all" place-holder="">Select Year</option> <!-- Option for all years -->
                                    <!-- Years will be added dynamically in JS -->
                                </select>

                            </div>
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
                    <div class="col-lg-8 mb-3">
                        <div class="row">
                            <div class="col-lg-8 d-flex align-items-center"><h4>Coconut Variety</h4></div>
                            <div class="col-lg-3 text-end mb-3">
                                <!-- <select id="yearSelect" class="form-select"></select> -->
                                <select id="cocoYearSelect" class="form-select">
                                    <option value="all" place-holder="">Select Year</option> <!-- Option for all years -->
                                    <!-- Years will be added dynamically in JS -->
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
            </div>
        </div>
          
    </section>

</main>

@include('admin.partials.__footer')
