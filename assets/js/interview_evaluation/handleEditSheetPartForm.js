import $ from "jquery";
import choicesInit from "../choices";
import addRemoveOptionsEditFormInit from "../addRemoveOptionsEditForm";
import addRemoveOptionsInit from "../addRemoveOptions";
import showEditFormModel from "./showEditFormModel";

function handleEditSheetPartForm(e) {
    e.preventDefault()
    var form = $(e.currentTarget)
    var sheetPartId = $(e.target).data('sheet-part-id')
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $("[data-bs-dismiss='modal']").trigger({ type: 'click' })
            $('#'+ sheetPartId).replaceWith(data)
            $('#'+ sheetPartId+ ' .stretched-link').on('click', showEditFormModel)
            choicesInit()
            addRemoveOptionsInit()
        },
        error: function (jqXHR) {
            $(".edit-sheet-part-modal-body").html(jqXHR.responseText)
            $(".edit-sheet-part-form").on("submit", handleEditSheetPartForm)
            choicesInit()
            addRemoveOptionsEditFormInit()
        }
    })
}

export default handleEditSheetPartForm;