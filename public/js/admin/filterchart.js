export function filter(sendYear, reqYear) {

    sendYear.addEventListener("change", (e) => {
        const selectedYear = e.target.value;
        if ($("#monthlyFilter").is(":checked")) {
            reqYear(false, true, selectedYear);
        }else if ($("#yearlyFilter").is(":checked")) {
            reqYear(true, false, selectedYear);
        }else{
            reqYear(true, true, selectedYear);
        }
    });
    document.getElementById("monthlyFilter").addEventListener("change", (event) => {
        if (event.target.checked) {
            $("#yearlyFilter").prop("checked", false);   
            reqYear(false, true, $("#diseaseYearSelect").val());
            reqYear(false, true, $("#cocoYearSelect").val());
        } 
        else {
            $("#district3Filter").prop("checked", false);
            $("#district4Filter").prop("checked", false);
            reqYear(true, true,  $("#diseaseYearSelect").val());
            reqYear(true, true,  $("#cocoYearSelect").val());
        }
    });
    document.getElementById("yearlyFilter").addEventListener("change", (event) => {
        if (event.target.checked) {
            $("#monthlyFilter").prop("checked", false); 
            reqYear(true, false, $("#diseaseYearSelect").val()); 
            reqYear(true, false, $("#cocoYearSelect").val()); 

        } 
        else {
            $("#district3Filter").prop("checked", false);
            $("#district4Filter").prop("checked", false);
            reqYear(true, true, $("#diseaseYearSelect").val()); 
            reqYear(true, true, $("#cocoYearSelect").val()); 

        }
    });
}
