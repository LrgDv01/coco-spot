import { filter } from "./utils/filterchart.js";

let coconut_chart;
function updateChart(displayYearly, displayMonthly, selectedYear, cocoData, selectedDistrict) {
    let labels = [];
    let lagunaTallData = [];
    let dwarfCoconutData = []; 
    let hybridData = [];

    // const filteredData = selectedYear === "all"
    //     ? cocoData
    //     : cocoData.filter(item => item.year === parseInt(selectedYear));
 
    const filteredData = cocoData.filter(item => {
        const yearMatches = selectedYear === "all" || item.year === parseInt(selectedYear);
        const districtMatches = selectedDistrict === "all" || item.district === selectedDistrict;
        return yearMatches && districtMatches;
    });

    if (displayYearly) {
        const yearlyData = {};
        filteredData.forEach((item) => {
            if (!yearlyData[item.year]) {
                yearlyData[item.year] = { laguna_tall: 0, dwarf_coconut: 0, hybrid: 0 };
            }
            yearlyData[item.year].laguna_tall += item.laguna_tall;
            yearlyData[item.year].dwarf_coconut += item.dwarf_coconut;
            yearlyData[item.year].hybrid += item.hybrid;
        });
        for (const year in yearlyData) {
            labels.push(year.toString());
            lagunaTallData.push(yearlyData[year].laguna_tall);
            dwarfCoconutData.push(yearlyData[year].dwarf_coconut);
            hybridData.push(yearlyData[year].hybrid);
        }
    }
    if (displayMonthly) {
        filteredData.slice(0, 12).forEach((item) => {
            labels.push(`${item.year}-${String(item.month).padStart(2, "0")}`);
            lagunaTallData.push(item.laguna_tall);
            dwarfCoconutData.push(item.dwarf_coconut);
            hybridData.push(item.hybrid);
        });
    }
    if (coconut_chart) {
        coconut_chart.destroy();
    }
    const config = {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Laguna Tall",
                    data: lagunaTallData,
                    backgroundColor: "#65735C", 
                    borderWidth: 3, 
                    // fill: "-1", 
                    pointRadius: 5, 
                    pointHoverRadius: 9,
                },
                {
                    label: "Dwarf",
                    data: dwarfCoconutData,
                    backgroundColor: "#2F6F3D", 
                    borderWidth: 3, 
                    // fill: "-1",
                    pointRadius: 5, 
                    pointHoverRadius: 9,
                },
                {
                    label: "Hybrid",
                    data: hybridData,
                    backgroundColor: "#228B22", 
                    borderWidth: 3, 
                    // fill: "-1",
                    pointRadius: 5, 
                    pointHoverRadius: 9,
                }
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: "top" },
                title: {
                    display: true,
                    text: `Coconut Varities (${selectedYear})`,
                    font: { size: 24 },
                    color: 'black'
                },
                tooltip: { enabled: true, intersect: false, mode: "nearest"},
                zoom: {
                    pan: { enabled: true, mode: 'xy' },
                    zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'x' },
                },
            },
            interaction: {
                mode: "nearest", 
                axis: "xy",
                intersect: false,
            },  
            scales: {
                x: { title: { display: true, text: 'Years', font: { style: 'italic', size: 16 } } },
                y: { title: { display: true, text: 'Counts', font: { style: 'italic', size: 16 } }, beginAtZero: true }
            },
        },
    };
    const ctx = $("#coconut_chart")[0].getContext("2d");
    coconut_chart = new Chart(ctx, config);
}

function displayCocoChart(year = null) { 
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
                if (!data.count_variety) {
                    console.error("Invalid data format received");
                    return;
                }
                const cocoData = [];
                for (const year in data.count_variety) {
                    for (const month in data.count_variety[year]) {
                        cocoData.push({
                            year: parseInt(year),
                            month: parseInt(month),
                            district: data.varietyDistrict[year][month] || "all", 
                            laguna_tall: data.count_variety[year][month].laguna_tall || 0,
                            dwarf_coconut: data.count_variety[year][month].dwarf_coconut || 0,
                            hybrid: data.count_variety[year][month].hybrid || 0
                        });
                    }
                }
                updateChart(yearly, monthly, yearSelected, cocoData, selectedDistrict);
            },
            error: function (err) {
                console.error("Error fetching disease data:", err);
            }
        });
    }
    reqYear(true, true, currentYear, 'all');
    filter(sendYear, reqYear);
  
}

$(document).ready(function() {
    const currentYear = new Date().getFullYear();
    const cocoYearSelect = document.getElementById('cocoYearSelect');
    if (cocoYearSelect) {
        for (let year = 2020; year <= currentYear; year++) {
            const option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            cocoYearSelect.appendChild(option);
        }
        const options = Array.from(cocoYearSelect.options).slice(1);
        cocoYearSelect.innerHTML = ""; 
        options.reverse().forEach((option) => cocoYearSelect.appendChild(option));
        displayCocoChart(cocoYearSelect);
    }
});
 
 