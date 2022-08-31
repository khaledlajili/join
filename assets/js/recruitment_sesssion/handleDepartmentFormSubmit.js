import $ from 'jquery'

function handleDepartmentFormSubmit(e) {
    e.preventDefault()
    const form = $(e.currentTarget);
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $("[data-bs-dismiss='modal']").trigger({ type: "click" })
            $("#table-departments-body").append(data['departmentRow'])
            $(".addDepartmentModelBody").html(data['departmentFormView'])
            $(".department_form").on("submit", handleDepartmentFormSubmit)
        },
        error: function (jqXHR) {
            $(".addDepartmentModelBody").html(jqXHR.responseText)
            $(".department_form").on("submit", handleDepartmentFormSubmit)
        }
    })
}

$(".department_form").on("submit", handleDepartmentFormSubmit)