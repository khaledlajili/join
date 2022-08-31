import $ from "jquery";
import editQuestionTypeInit from "./editQuestionType";
import choicesInit from "../choices";
import addRemoveOptionsEditFormInit from "./../addRemoveOptionsEditForm";
import SelectQuestionTypeInit from "./selectQuestionType";
import addRemoveOptionsInit from "./../addRemoveOptions";
import showEditFormModel from "./showEditFormModel";


function handleEditFieldFormForm(e) {
    e.preventDefault()
    var form = $(e.currentTarget)
    var fieldId = $(e.target).data('field-id')
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $("[data-bs-dismiss='modal']").trigger({ type: 'click' })
            $('#'+ fieldId).replaceWith(data)
            $('#'+ fieldId+ ' .stretched-link').on('click', showEditFormModel)
            SelectQuestionTypeInit()
            choicesInit()
            addRemoveOptionsInit()
        },
        error: function (jqXHR) {
            $(".edit-field-modal-body").html(jqXHR.responseText)
            $(".edit-field-form").on("submit", handleEditFieldFormForm)
            editQuestionTypeInit()
            choicesInit()
            addRemoveOptionsEditFormInit()
        }
    })
}

export default handleEditFieldFormForm;
