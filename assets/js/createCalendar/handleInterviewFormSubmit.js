import appCreateInterviewCalendarInit from "./index";
import $ from 'jquery'
import {Modal} from "bootstrap";

const detailEventModalSelector = document.querySelector('#eventDetailsModal .modal-content'),
    detailEventModal = new Modal(detailEventModalSelector);

function handleInterviewFormSubmit(e) {
    console.log('t3adet')
    e.preventDefault()
    var form = $(e.currentTarget)
    $.ajax({
        url: '/admin/interview/submitData',
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $("[data-bs-dismiss='modal']").trigger({ type: "click" })
            $(".create_interview_form").on("submit", handleInterviewFormSubmit)
            $('.calendarScript').html(data)
            appCreateInterviewCalendarInit()
        },
        error: function (jqXHR) {
            $(".addEventModelBody").html(jqXHR.responseText)
            $(".create_interview_form").on("submit", handleInterviewFormSubmit)
        }
    })
}
export default handleInterviewFormSubmit;