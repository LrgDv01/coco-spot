import { filter } from "./utils/filterchart.js";

let diseases_chart;
function updateChart(displayYearly, displayMonthly, selectedYear, diseaseData, selectedDistrict) {
    let labels = [];
    let budRotData = []; 
    let leafSpotData = [];
    let stemBleedingData = [];

    // const filteredData = selectedYear === "all"
    //     ? diseaseData
    //     : diseaseData.filter(item => item.year === parseInt(selectedYear));

    const filteredData = diseaseData.filter(item => {
        const yearMatches = selectedYear === "all" || item.year === parseInt(selectedYear);
        const districtMatches = selectedDistrict === "all" || item.district === selectedDistrict;
        return yearMatches && districtMatches;
    });

    if (displayYearly) {
        const yearlyData = {};
        filteredData.forEach((item) => {
            if (!yearlyData[item.year]) {
                yearlyData[item.year] = {bud_rot: 0, leaf_spot_disease: 0, stem_bleeding: 0 };
            }
            yearlyData[item.year].bud_rot += item.bud_rot;
            yearlyData[item.year].leaf_spot_disease += item.leaf_spot_disease;
            yearlyData[item.year].stem_bleeding += item.stem_bleeding;
        });
        for (const year in yearlyData) {
            labels.push(year.toString());
            budRotData.push(yearlyData[year].bud_rot);
            leafSpotData.push(yearlyData[year].leaf_spot_disease);
            stemBleedingData.push(yearlyData[year].stem_bleeding);
        }
    }

    if (displayMonthly) {
        filteredData.slice(0, 12).forEach((item) => {
            labels.push(`${item.year}-${String(item.month).padStart(2, "0")}`);
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
                    label: "Bud Rot",
                    data: budRotData,
                    backgroundColor: "#964B00",
                },
                {
                    label: "Leaf Spot",
                    data: leafSpotData,
                    backgroundColor: "#556B2F",
                },
                {
                    label: "Stem Bleeding",
                    data: stemBleedingData,
                    backgroundColor: "#8B0A1A",
                },
            ],
        },
        options: {
            indexAxis: "y",
            responsive: true,
            plugins: {
                legend: { position: "top" },
                title: {
                    display: true,
                    text: `Coconut Diseases (${selectedYear})`,
                    font: { size: 24 },
                    color: 'black'
                },
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
    // let sendYear = year === null ? currentYear : year ;
    function reqYear(yearly, monthly, yearSelected, selectedDistrict) {
        $.ajax({ 
            url: "/admin/get_dashboard_info",
            method: "GET",
            data: { 
                year: yearSelected,
                district: selectedDistrict 
             },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }, 
            success: function (data) {
                console.log('response', data);
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
                            district: data.diseaseDistrict[year][month] || "all",
                            bud_rot: data.count_disease[year][month].bud_rot || 0,
                            leaf_spot_disease: data.count_disease[year][month].leaf_spot || 0,
                            stem_bleeding: data.count_disease[year][month].stem_bleeding || 0
                        });
                    }
                }
                updateChart(yearly, monthly, yearSelected, diseaseData, selectedDistrict);
            },
            error: function (err) {
                console.error("Error fetching disease data:", err);
            }
        }); 
    }
    reqYear(true, true, currentYear, 'all');
    filter(sendYear, reqYear);
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

