import appInterviewCalendarInit from "./index";
import $ from 'jquery'
import {Modal} from "bootstrap";

const addEventModalSelector = document.querySelector('#addEventModal'),
    addEventModal = new Modal(addEventModalSelector);

function handleInterviewFormSubmit(e) {
    console.log('t3adet')
    e.preventDefault()
    var form = $(e.currentTarget)
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            addEventModal.hide();
            $('.addEventModelBody').html(data)
            $(".interview_form").on("submit", handleInterviewFormSubmit)
            appInterviewCalendarInit()
        },
        error: function (jqXHR) {
            $(".addEventModelBody").html(jqXHR.responseText)
            $(".interview_form").on("submit", handleInterviewFormSubmit)
        }
    })
}
export default handleInterviewFormSubmit;