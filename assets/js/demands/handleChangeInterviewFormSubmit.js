// import appCreateInterviewCalendarInit from "./index";
import $ from 'jquery'
// import {Modal} from "bootstrap";

// const detailEventModalSelector = document.querySelector('#eventDetailsModal .modal-content'),
//     detailEventModal = new Modal(detailEventModalSelector);

function handleChangeInterviewFormSubmit(e) {
    console.log('t3adet')
    e.preventDefault()
    var form = $(e.currentTarget)
    $.ajax({
        url: '/admin/interview/submitChange',
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $("[data-bs-dismiss='modal']").trigger({ type: "click" })
            $(".change_interview_form").on("submit", handleChangeInterviewFormSubmit)
            location.reload();
            // appCreateInterviewCalendarInit()
        },
        error: function (jqXHR) {
            $(".changeInterview").html(jqXHR.responseText)
            console.log("error");
            $(".change_interview_form").on("submit", handleChangeInterviewFormSubmit)
        }
    })
}
export default handleChangeInterviewFormSubmit;