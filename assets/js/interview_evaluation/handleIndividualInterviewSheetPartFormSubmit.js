import $ from 'jquery'
import choicesInit from "../choices";
import addRemoveOptionsInit from "../addRemoveOptions";
import kanbanInit from "../kanban";
import showEditFormModel from "./showEditFormModel";

function handleSheetPartFormFormSubmit(e) {
    e.preventDefault()
    var form = $(e.currentTarget)
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $(".sheet-part-item-container").append(data['sheetPartItem'])
            document.querySelector('.sheet-part-item-container').scrollTo({ top : 10000 });
            $(".add-sheet-part-form-container").html(data['SheetPartFormView'])
            $(".add-sheet-part-form").on("submit", handleSheetPartFormFormSubmit)
            choicesInit()
            addRemoveOptionsInit()
            kanbanInit()
            $('.kanban-item .stretched-link').on('click', showEditFormModel)
        },
        error: function (jqXHR) {
            $(".add-sheet-part-form-container").html(jqXHR.responseText)
            $(".add-sheet-part-form").on("submit", handleSheetPartFormFormSubmit)
            choicesInit()
            addRemoveOptionsInit()
            kanbanInit()
        }
    })
}

$(".add-sheet-part-form").on("submit", handleSheetPartFormFormSubmit)