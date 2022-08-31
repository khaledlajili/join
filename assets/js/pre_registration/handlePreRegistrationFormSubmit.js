import $ from 'jquery'
import choicesInit from "../choices";
import SelectQuestionTypeInit from "./selectQuestionType";
import addRemoveOptionsInit from "../addRemoveOptions";
import kanbanInit from "../kanban";
import showEditFormModel from "./showEditFormModel";

function handleFieldFormFormSubmit(e) {
    e.preventDefault()
    var form = $(e.currentTarget)
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $(".field-item-container").append(data['fieldItem'])
            document.querySelector('.field-item-container').scrollTo({ top : 10000 });
            $(".add-field-form-container").html(data['fieldFormView'])
            $(".add-field-form").on("submit", handleFieldFormFormSubmit)
            SelectQuestionTypeInit()
            choicesInit()
            addRemoveOptionsInit()
            kanbanInit()
            $('.kanban-item .stretched-link').on('click', showEditFormModel)
        },
        error: function (jqXHR) {
            $(".add-field-form-container").html(jqXHR.responseText)
            $(".add-field-form").on("submit", handleFieldFormFormSubmit)
            SelectQuestionTypeInit()
            choicesInit()
            addRemoveOptionsInit()
            kanbanInit()
        }
    })
}

$(".add-field-form").on("submit", handleFieldFormFormSubmit)