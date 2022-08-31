import $ from 'jquery'

function handleStudyLevelFormSubmit(e) {
    e.preventDefault()
    const form = $(e.currentTarget);
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (data) {
            $("[data-bs-dismiss='modal']").trigger({ type: "click" })
            $("#table-study-levels-body").append(data['studyLevelRow'])
            $(".addStudyLevelModelBody").html(data['studyLevelFormView'])
            $(".study_level_form").on("submit", handleStudyLevelFormSubmit)
        },
        error: function (jqXHR) {
            $(".addStudyLevelModelBody").html(jqXHR.responseText)
            $(".study_level_form").on("submit", handleStudyLevelFormSubmit)
        }
    })
}

$(".study_level_form").on("submit", handleStudyLevelFormSubmit)