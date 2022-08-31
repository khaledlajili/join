import './styles/app.css';
import $ from 'jquery'



import '@fortawesome/fontawesome-free/js/all.min.js'
import 'lodash'
import './js/polyfill'
import './js/calendarTheme'
import appCreateInterviewCalendarInit from './js/createCalendar/index';
import handleInterviewFormSubmit from "./js/createCalendar/handleInterviewFormSubmit";

$(".create_interview_form").on("submit", handleInterviewFormSubmit)
appCreateInterviewCalendarInit()