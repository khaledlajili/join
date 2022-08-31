import $ from 'jquery'

function handleCheckboxSwitch(e, id) {
    const form = $("#" + id),
        dateInputs = form.find(':input');
    if (e.currentTarget.checked) {
        form.removeClass('d-none')
        $.each(dateInputs, (index, item) => {
            $(item).prop('required', true)
        })
    } else {
        form.addClass('d-none')
        $.each(dateInputs, (index, item) => {
            $(item).val(null)
            $(item).prop('required', false)
        })
    }
}

function handleCheckboxSwitchOnLoad(e, id) {
    if (e.length !== 0) {
        const form = $("#" + id),
            dateInputs = form.find(':input');
        if (e.is(":checked")) {
            form.removeClass('d-none')
            $.each(dateInputs, (index, item) => {
                $(item).prop('required', true)
            })
        } else {
            form.addClass('d-none')
            $.each(dateInputs, (index, item) => {
                $(item).val(null)
                $(item).prop('required', false)
            })
        }
    }

}

handleCheckboxSwitchOnLoad($("#recruitment_session_collectiveInterview"), 'collectiveInterviewForm')
handleCheckboxSwitchOnLoad($("#recruitment_session_technicalTest"), 'technicalTestForm')
handleCheckboxSwitchOnLoad($("#recruitment_session_bookingForInterview"), 'bookingForInterviewForm')
handleCheckboxSwitchOnLoad($("#recruitment_session_trialPeriod"), 'trialPeriodForm')

$("#recruitment_session_collectiveInterview").on("change", (event) => handleCheckboxSwitch(event, 'collectiveInterviewForm'))
$("#recruitment_session_technicalTest").on("change", (event) => handleCheckboxSwitch(event, 'technicalTestForm'))
$("#recruitment_session_bookingForInterview").on("change", (event) => handleCheckboxSwitch(event, 'bookingForInterviewForm'))
$("#recruitment_session_trialPeriod").on("change", (event) => handleCheckboxSwitch(event, 'trialPeriodForm'))