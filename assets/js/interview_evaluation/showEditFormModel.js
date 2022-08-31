import $ from "jquery";
import choicesInit from "../choices";
import addRemoveOptionsEditFormInit from "../addRemoveOptionsEditForm";
import {Modal} from "bootstrap";
import handleEditSheetPartForm from "./handleEditSheetPartForm";

const editSheetPartModal = document.querySelector(
        '#edit-sheet-part-modal'
    ),
    modal = new Modal(editSheetPartModal);

function showEditFormModel(e){
    var sheetPartId = $(e.target).data('sheet-part-id')

    $.ajax({
        url: '/admin/interview/evaluation/grid/sheet/get',
        method: 'POST',
        data: {sheetPartId : sheetPartId},
        success: function (data) {
            modal.show();
            $(".edit-sheet-part-modal-body").html(data)
            $(".edit-sheet-part-form").on("submit", handleEditSheetPartForm)
            choicesInit()
            addRemoveOptionsEditFormInit()
        }
    })
}

export default showEditFormModel;