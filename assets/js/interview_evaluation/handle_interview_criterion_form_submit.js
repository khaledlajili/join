import $ from 'jquery'

function handleInterviewCriterionFormSubmit(e) {
    e.preventDefault()
    var form = $(e.currentTarget)
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $("[data-bs-dismiss='modal']").trigger({ type: "click" })
            $("#table-criteria-"+data["sheet"]+"-body").append(data['criterionRow'])
            //$("#technicalTestsFormsContainer").append(data['technicalTestForm'])
            $(".addDCriterionModelBody").html(data['InterviewEvaluationCriterionView'])
            $(".criteria_form").on("submit", handleInterviewCriterionFormSubmit)
            // $(".sheet_form").on("submit", handleSheetPartFormSubmit)
        },
        error: function (jqXHR) {
            console.log("critere");
            $(".addDCriterionModelBody").html(jqXHR.responseText)
            $(".criteria_form").on("submit", handleInterviewCriterionFormSubmit)
            // $(".sheet_form").on("submit", handleSheetPartFormSubmit)
        }
    })
}
function handleSheetPartFormSubmit(e) {
    e.preventDefault()
    var form = $(e.currentTarget)
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            //console.log($("#sheet-tables"));
            $("[data-bs-dismiss='modal']").trigger({ type: "click" })
            $("#table-sheet-body").append(data['sheetRow'])
            //if($("#sheet-tables"))
            $("#sheet-tables").append(data['sheetTable'])
            //$("#technicalTestsFormsContainer").append(data['technicalTestForm'])
            $(".addDSheetModelBody").html(data['InterviewEvaluationSheetView'])
            $(".addDCriterionModelBody").html(data['InterviewEvaluationCriterionView'])
            $(".sheet_form").on("submit", handleSheetPartFormSubmit)
            $(".criteria_form").on("submit", handleInterviewCriterionFormSubmit)
            // $(".criteria_form").on("submit", handleInterviewCriterionFormSubmit)
        },
        error: function (jqXHR) {
            console.log("sheet");
            $(".addDSheetModelBody").html(jqXHR.responseText)
            $(".sheet_form").on("submit", handleSheetPartFormSubmit)
            // $(".criteria_form").on("submit", handleInterviewCriterionFormSubmit)
        }
    })
}

$(".criteria_form").on("submit", handleInterviewCriterionFormSubmit)
$(".sheet_form").on("submit", handleSheetPartFormSubmit)