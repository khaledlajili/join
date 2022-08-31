import $ from 'jquery'
import handleChangeInterviewFormSubmit from './/handleChangeInterviewFormSubmit'

var changeInterview = document.getElementById('changeInterview')
if(changeInterview){
    $('.loading').css('visibility', 'visible')
    changeInterview.addEventListener('shown.bs.modal', function (event) {
        var triggerElement = $(event.relatedTarget)
        $.ajax({
            url:'/admin/interview/change/'+triggerElement[0].id,
            method : "POST",
            success: function(data){
                $(".changeInterviewFormContainer").html(data);
                $(".change_interview_form").on("submit", handleChangeInterviewFormSubmit)

            },
            error: function(data){
                console.log("error");
            }
        })
    })
}