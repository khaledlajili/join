import $ from "jquery";

const SelectQuestionTypeInit = () => {
    var select_question_type = $(".select-question-type").find('select'),
        option_field_container = $(".option-field-container")

    function showHideOptions(e) {
        var select_question_type_value = $(e.currentTarget).find(":selected").val(),
            option_types = ['select', 'checkbox', 'radio']
        if ($.inArray(select_question_type_value, option_types) !== -1) {
            option_field_container.show()
        } else {
            option_field_container.hide()
        }
    }

    function showHideOptionsOnLoad(e) {
        var select_question_type_value = $(e).find(":selected").val(),
            option_types = ['select', 'checkbox', 'radio']
        if ($.inArray(select_question_type_value, option_types) !== -1) {

            option_field_container.show()
        } else {
            option_field_container.hide()
        }
    }

    showHideOptionsOnLoad(select_question_type)

    $(select_question_type).on("change", showHideOptions)

}

export default SelectQuestionTypeInit;