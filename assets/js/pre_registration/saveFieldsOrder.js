import $ from "jquery";
import {Modal} from "bootstrap";

const orderSavedSuccesfullyModalSelector = document.querySelector('#orderSavedSuccesfully'),
    SavingOrderFailedModalSelector = document.querySelector('#savingOrderFailed'),
    orderSavedSuccesfullyModal = new Modal(orderSavedSuccesfullyModalSelector),
    SavingOrderFailedModal = new Modal(SavingOrderFailedModalSelector);

function saveFieldsOrder(e) {
    e.preventDefault();
    var FieldsList = [];
    $(".field-item-container").children().each((index, elem) => {
        FieldsList.push(elem.id);
    });
    if (FieldsList.length < 2) {
        SavingOrderFailedModal.show()
    }
    else{
        $.ajax({
            type: "POST",
            url: "/admin/preregistration/save/fields/order",
            data: {
                order: FieldsList
            }
        }).then(function (data) {
            orderSavedSuccesfullyModal.show()
        });
    }
}

$(".saveOrder").on("click", saveFieldsOrder)