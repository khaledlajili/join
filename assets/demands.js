// any CSS you import will output into a single css file (app.css in this case)

import './styles/app.css'
import $ from 'jquery'

// import JS

import '@fortawesome/fontawesome-free/js/all.min.js'
import 'lodash'
import './js/polyfill'
import './js/appTheme'
import './js/demands/getDemandeData'
import handleChangeInterviewFormSubmit from "./js/demands/handleChangeInterviewFormSubmit";

$(".change_interview_form").on("submit", handleChangeInterviewFormSubmit)

