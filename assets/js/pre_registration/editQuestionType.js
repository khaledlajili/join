import $ from "jquery";

const editQuestionTypeInit = () => {
    var edit_question_type = $(".edit-question-type").find('select'),
        edit_option_fields_container = $(".edit-option-field-container")

    function showHideOptions(e) {
        var edit_question_type_value = $(e.currentTarget).find(":selected").val(),
            option_types = ['select', 'checkbox', 'radio']
        if ($.inArray(edit_question_type_value, option_types) !== -1) {
            edit_option_fields_container.show()
        } else {
            edit_option_fields_container.hide()
        }
    }

    function showHideOptionsOnLoad(e) {
        var edit_question_type_value = $(e).find(":selected").val(),
            option_types = ['select', 'checkbox', 'radio']
        if ($.inArray(edit_question_type_value, option_types) !== -1) {
            edit_option_fields_container.show()
        } else {
            edit_option_fields_container.hide()
        }
    }

    showHideOptionsOnLoad(edit_question_type)

    $(edit_question_type).on("change", showHideOptions)

}

export default editQuestionTypeInit;