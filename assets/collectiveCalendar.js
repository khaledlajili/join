import './styles/app.css';
import $ from 'jquery'



import '@fortawesome/fontawesome-free/js/all.min.js'
import 'lodash'
import './js/polyfill'
import './js/calendarTheme'
import appCollectiveCalendarInit from './js/collective/index';
import handleCollectiveInterviewFormSubmit from "./js/collective/handleCollectiveInterviewFormSubmit";

$(".collective_interview_form").on("submit", handleCollectiveInterviewFormSubmit)
appCollectiveCalendarInit()