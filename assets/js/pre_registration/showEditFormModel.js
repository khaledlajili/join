import $ from "jquery";
import editQuestionTypeInit from "./editQuestionType";
import choicesInit from "../choices";
import addRemoveOptionsEditFormInit from "../addRemoveOptionsEditForm";
import {Modal} from "bootstrap";
import handleEditFieldFormForm from "./handleEditFieldFormForm";

const editFieldModal = document.querySelector(
    '#edit-field-modal'
);
const modal = new Modal(editFieldModal);

function showEditFormModel(e){
    var fieldId = $(e.target).data('field-id')

    $.ajax({
        url: '/admin/preregistration/get/field',
        method: 'POST',
        data: {fieldId : fieldId},
        success: function (data) {
            modal.show();
            $(".edit-field-modal-body").html(data)
            $(".edit-field-form").on("submit", handleEditFieldFormForm)
            editQuestionTypeInit()
            choicesInit()
            addRemoveOptionsEditFormInit()
        }
    })
}

export default showEditFormModel;