import { filter } from "./filterchart.js";

let diseases_chart;
function updateChart(displayYearly, displayMonthly, selectedYear, diseaseData) {
    let labels = [];
    let coconutWiltData = [];
    let budRotData = []; 
    let leafSpotData = [];
    let stemBleedingData = [];

    const filteredData = selectedYear === "all"
        ? diseaseData
        : diseaseData.filter(item => item.year === parseInt(selectedYear));

    if (displayYearly) {
        const yearlyData = {};
        filteredData.forEach((item) => {
            if (!yearlyData[item.year]) {
                yearlyData[item.year] = { coconut_wilt: 0, bud_rot: 0, leaf_spot_disease: 0, stem_bleeding: 0 };
            }
            yearlyData[item.year].coconut_wilt += item.coconut_wilt;
            yearlyData[item.year].bud_rot += item.bud_rot;
            yearlyData[item.year].leaf_spot_disease += item.leaf_spot_disease;
            yearlyData[item.year].stem_bleeding += item.stem_bleeding;
        });
        for (const year in yearlyData) {
            labels.push(year.toString());
            coconutWiltData.push(yearlyData[year].coconut_wilt);
            budRotData.push(yearlyData[year].bud_rot);
            leafSpotData.push(yearlyData[year].leaf_spot_disease);
            stemBleedingData.push(yearlyData[year].stem_bleeding);
        }
    }

    if (displayMonthly) {
        filteredData.slice(0, 12).forEach((item) => {
            labels.push(`${item.year}-${String(item.month).padStart(2, "0")}`);
            coconutWiltData.push(item.coconut_wilt);
            budRotData.push(item.bud_rot);
            leafSpotData.push(item.leaf_spot_disease);
            stemBleedingData.push(item.stem_bleeding);
        });
    }

    if (diseases_chart) {
        diseases_chart.destroy();
    }

    const config = {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Coconut Wilt",
                    data: coconutWiltData,
                    backgroundColor: "rgba(245, 130, 48, 0.6)",
                },
                {
                    label: "Bud Rot",
                    data: budRotData,
                    backgroundColor: "rgba(145, 30, 180, 0.6)",
                },
                {
                    label: "Leaf Spot",
                    data: leafSpotData,
                    backgroundColor: "rgba(255, 225, 25, 0.6)",
                },
                {
                    label: "Stem Bleeding",
                    data: stemBleedingData,
                    backgroundColor: "rgba(230, 25, 75, 0.6)",
                },
            ],
        },
        options: {
            indexAxis: "y",
            responsive: true,
            plugins: {
                legend: { position: "top" },
                tooltip: { enabled: true },
                zoom: {
                    pan: { enabled: true, mode: 'xy' },
                    zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'y' },
                },
            },
            scales: {
                x: { title: { display: true, text: 'Counts', font: { style: 'italic', size: 16 } } },
                y: { title: { display: true, text: 'Dates', font: { style: 'italic', size: 16 } }, beginAtZero: true }
            },
        },
    };
    const ctx = $("#diseases_chart")[0].getContext("2d");
    diseases_chart = new Chart(ctx, config);
}

function displayDiseaseChart(year = null) {
    const currentYear = new Date().getFullYear();
    let sendYear = year || currentYear;

    function reqYear(yearly, monthly,  yearSelected) {
        $.ajax({ 
            url: "/admin/get_dashboard_info",
            method: "GET",
            data: { year: yearSelected },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }, 
            success: function (data) {
                if (!data.count_disease) {
                    console.error("Invalid data format received");
                    return;
                }
                const diseaseData = [];
                for (const year in data.count_disease) {
                    for (const month in data.count_disease[year]) {
                        diseaseData.push({
                            year: parseInt(year),
                            month: parseInt(month),
                            coconut_wilt: data.count_disease[year][month].coconut_wilt || 0,
                            bud_rot: data.count_disease[year][month].bud_rot || 0,
                            leaf_spot_disease: data.count_disease[year][month].leaf_spot || 0,
                            stem_bleeding: data.count_disease[year][month].stem_bleeding || 0
                        });
                    }
                }
                updateChart(yearly, monthly, yearSelected, diseaseData);
            },
            error: function (err) {
                console.error("Error fetching disease data:", err);
            }
        }); 
    }
    reqYear(true, true, currentYear);
    filter(sendYear, reqYear);

    // const district3Checkbox = document.getElementById("district3Filter");
    // district3Checkbox.addEventListener("change", (event) => {
    //     $("#district4Filter").prop("checked", false); 
    //     if (event.target.checked && $("#yearlyFilter").is(":checked")) {  
    //         updateChart(true, false, 'all'); 
    //     }
    //     if (event.target.checked &&  $("#monthlyFilter").is(":checked")) {
    //         updateChart(false, true, $("#diseaseYearSelect").val()); 
    //     }else {
    //         if ($("#yearlyFilter").is(":checked")) {
    //             updateChart(true, false, 'all'); 
    //         }
    //         else if ($("#monthlyFilter").is(":checked")) {
    //             updateChart(false, true, $("#diseaseYearSelect").val()); 
    //         } 
    //     }
    // });

    // const district4Checkbox = document.getElementById("district4Filter");   
    // district4Checkbox.addEventListener("change", (event) => {
    //     $("#district3Filter").prop("checked", false); 
    //     if (event.target.checked && $("#yearlyFilter").is(":checked")) {  
    //         updateChart(true, false, 'all'); 
    //     }
    //     if (event.target.checked &&  $("#monthlyFilter").is(":checked")) {
    //         updateChart(false, true, $("#diseaseYearSelect").val()); 
    //     }else {
    //         if ($("#yearlyFilter").is(":checked")) {
    //             updateChart(true, false, 'all'); 
    //         }
    //         else if ($("#monthlyFilter").is(":checked")) {
    //             updateChart(false, true, $("#diseaseYearSelect").val()); 
    //         }
    //     }
    // });
}

$(document).ready(function () {
    const currentYear = new Date().getFullYear();
    const diseaseYearSelect = document.getElementById('diseaseYearSelect');
    if (diseaseYearSelect) {
        for (let year = 2020; year <= currentYear; year++) {
            const option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            diseaseYearSelect.appendChild(option);
        }
        const options = Array.from(diseaseYearSelect.options).slice(1);
        diseaseYearSelect.innerHTML = "";
        options.reverse().forEach((option) => diseaseYearSelect.appendChild(option));
        displayDiseaseChart(diseaseYearSelect);
    }
});

