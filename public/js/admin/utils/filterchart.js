export function filter(sendYear, reqYear) {
   
    sendYear.addEventListener("change", (e) => {
        const selectedYear = e.target.value;
        if ($("#monthlyFilter").is(":checked")) {
            reqYear(false, true, selectedYear, 'all');
        } else if ($("#yearlyFilter").is(":checked")) {
            reqYear(true, false, selectedYear, 'all');
        } else {
            reqYear(true, true, selectedYear, 'all');
        }
    });

    document.getElementById("monthlyFilter").addEventListener("change", (event) => {
        if (event.target.checked) {
            $("#yearlyFilter").prop("checked", false);
            $("#district3Filter").prop("checked", false);
            $("#district4Filter").prop("checked", false);
            if ($("#diseaseYearSelect")){ 
                reqYear(false, true, $("#diseaseYearSelect").val(), 'all');
            }else {
                reqYear(false, true, $("#cocoYearSelect").val(), 'all');
            }
        } else {
            // $("#district3Filter").prop("checked", false);
            // $("#district4Filter").prop("checked", false);
            if ($("#diseaseYearSelect")){ 
                reqYear(true, true, $("#diseaseYearSelect").val(), 'all');
            }else {
                reqYear(true, true, $("#cocoYearSelect").val(), 'all');
            }
        }
    });

    document.getElementById("yearlyFilter").addEventListener("change", (event) => {
        if (event.target.checked) {
            $("#monthlyFilter").prop("checked", false);
            $("#district3Filter").prop("checked", false);
            $("#district4Filter").prop("checked", false);
            if ($("#diseaseYearSelect")){
                reqYear(true, false, $("#diseaseYearSelect").val(), 'all');
            }else {
                reqYear(true, false, $("#cocoYearSelect").val(), 'all');
            }
        } else {
            // $("#district3Filter").prop("checked", false);
            // $("#district4Filter").prop("checked", false);
            if ($("#diseaseYearSelect")){
                reqYear(true, true, $("#diseaseYearSelect").val(), 'all');
            }else {
                reqYear(true, true, $("#cocoYearSelect").val(), 'all');
            }
        }
    });


    const district3Checkbox = document.getElementById("district3Filter");
    district3Checkbox.addEventListener("change", (event) => {
        $("#district4Filter").prop("checked", false); 
        $("#monthlyFilter").prop("checked", false);
        $("#yearlyFilter").prop("checked", false);
        if (event.target.checked || $("#yearlyFilter").is(":checked")) {
            reqYear(true, false, 'all', 'III'); 
        }
        if (event.target.checked || $("#monthlyFilter").is(":checked")) {
            reqYear(false, true, $("#diseaseYearSelect").val(), 'III'); 
        } else {
            if ($("#yearlyFilter").is(":checked")) {
                reqYear(true, false, 'all', 'all'); 
            }
            else if ($("#monthlyFilter").is(":checked")) {
                reqYear(false, true, $("#diseaseYearSelect").val(), 'all'); 
            }
        }
    });

    const district4Checkbox = document.getElementById("district4Filter");
    district4Checkbox.addEventListener("change", (event) => {
        $("#district3Filter").prop("checked", false); 
        $("#monthlyFilter").prop("checked", false);
        $("#yearlyFilter").prop("checked", false);
        if (event.target.checked || $("#yearlyFilter").is(":checked")) {  
            reqYear(true, false, 'all', 'IV'); 
        }
        if (event.target.checked || $("#monthlyFilter").is(":checked")) {
            reqYear(false, true, $("#diseaseYearSelect").val(), 'IV'); 
        }else {
            if ($("#yearlyFilter").is(":checked")) {
                reqYear(true, false, 'all', 'all'); 
            }
            else if ($("#monthlyFilter").is(":checked")) {
                reqYear(false, true, $("#diseaseYearSelect").val(), 'all'); 
            }
        }
    });

    // function getSelectedDistrict() {
    //     if ($("#district3Filter").is(":checked")) {
    //         return "III"; 
    //     } else if ($("#district4Filter").is(":checked")) {
    //         return "IV"; 
    //     }
    //     return "all"; 
    // }

}
