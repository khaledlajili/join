import $ from 'jquery'
import {Modal} from "bootstrap";
import choices from "../choices";
import appEmergencyCalendarInit from "./index";

const addEventModalSelector = document.querySelector('#addEventModal'),
    addEventModal = new Modal(addEventModalSelector);

function handleEmergencyInterviewFormSubmit(e) {
    e.preventDefault()
    var form = $(e.currentTarget)
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            addEventModal.hide();
            $('.addEventModelBody').html(data)
            $(".collective_interview_form").on("submit", handleEmergencyInterviewFormSubmit)
            appEmergencyCalendarInit()
            choices()
        },
        error: function (jqXHR) {
            $(".addEventModelBody").html(jqXHR.responseText)
            $(".collective_interview_form").on("submit", handleEmergencyInterviewFormSubmit)
            choices()
        }
    })
}
export default handleEmergencyInterviewFormSubmit;