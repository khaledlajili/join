import $ from 'jquery'

function handleCollectiveInterviewEvaluationCriterionFormSubmit(e) {
  e.preventDefault()
  var form = $(e.currentTarget)
  $.ajax({
    url: form.attr('action'),
    method: 'POST',
    data: form.serialize(),
    success: function (data) {
      $("[data-bs-dismiss='modal']").trigger({ type: 'click' })
      $('#table-criteria-body').append(data['collectiveInterviewEvaluationCriterionRow'])
      $('.addDCriterionModelBody').html(data['collectiveInterviewEvaluationCriterionFormView'],)
      $('.collective-interview-evaluation-criterion-form').on('submit', handleCollectiveInterviewEvaluationCriterionFormSubmit,)
    },
    error: function (jqXHR) {
      $('.addDCriterionModelBody').html(jqXHR.responseText)
      $('.collective-interview-evaluation-criterion-form').on('submit', handleCollectiveInterviewEvaluationCriterionFormSubmit,)
    },
  })
}

$('.collective-interview-evaluation-criterion-form').on('submit', handleCollectiveInterviewEvaluationCriterionFormSubmit)
