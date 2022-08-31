import $ from 'jquery'
import choices from "../choices";


function handleRecruiterRegistrationFormSubmit(e) {
    e.preventDefault()
    var form = $(e.currentTarget);
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $("[data-bs-dismiss='modal']").trigger({ type: "click" })
            $("#table-recruiters-body").append(data['recruiterRow'])
            $(".addRecruiterModelBody").html(data['recruiterRegistrationFormView'])
            $(".recruiter_registration_form").on("submit", handleRecruiterRegistrationFormSubmit)
            choices()
        },
        error: function (jqXHR) {
            $(".addRecruiterModelBody").html(jqXHR.responseText)
            $(".recruiter_registration_form").on("submit", handleRecruiterRegistrationFormSubmit)
            choices()
        }
    })
}

$(".recruiter_registration_form").on("submit", handleRecruiterRegistrationFormSubmit)