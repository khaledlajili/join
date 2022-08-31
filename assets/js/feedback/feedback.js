import rater from 'rater-js'
import $ from 'jquery'
if (document.querySelector('#rater-preRegistration') != null) {
  var myRater = rater({
    element: document.querySelector('#rater-preRegistration'),
    rating: 0,
    rateCallback: function rateCallback(rating, done) {
      console.log(rating);
      this.setRating(rating)
      $("input[name='feedback[preRegistrationRating]']").val(rating)
      done()
    },
    starSize: 32,
    step: 0.5,
  })
}

if (document.querySelector('#rater-collectiveInterview') != null) {
  var myRater = rater({
    element: document.querySelector('#rater-collectiveInterview'),
    rating: 0,
    rateCallback: function rateCallback(rating, done) {
      this.setRating(rating)
      $("input[name='feedback[collectiveInterviewsRating]']").val(rating)
      done()
    },
    starSize: 32,
    step: 0.5,
  })
}
if (document.querySelector('#rater-technicalTest') != null) {
  var myRater = rater({
    element: document.querySelector('#rater-technicalTest'),
    rating: 0,
    rateCallback: function rateCallback(rating, done) {
      this.setRating(rating)
      $("input[name='feedback[technicalTestRating]']").val(rating)
      done()
    },
    starSize: 32,
    step: 0.5,
  })
}
if (document.querySelector('#rater-inteview') != null) {
  var myRater = rater({
    element: document.querySelector('#rater-inteview'),
    rating: 0,
    rateCallback: function rateCallback(rating, done) {
      this.setRating(rating)
      $("input[name='feedback[individualInterviewsRating]']").val(rating)
      done()
    },
    starSize: 32,
    step: 0.5,
  })
}
if (document.querySelector('#rater-trial') != null) {
  var myRater = rater({
    element: document.querySelector('#rater-trial'),
    rating: 0,
    rateCallback: function rateCallback(rating, done) {
      this.setRating(rating)
      $("input[name='feedback[trialPeriodRating]']").val(rating)
      done()
    },
    starSize: 32,
    step: 0.5,
  })
}
