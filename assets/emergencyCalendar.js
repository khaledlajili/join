import './styles/app.css';
import $ from 'jquery'



import '@fortawesome/fontawesome-free/js/all.min.js'
import 'lodash'
import './js/polyfill'
import './js/calendarTheme'
import handleEmergencyInterviewFormSubmit from "./js/emergencyCalendar/handleEmergencyInterviewFormSubmit";
import appEmergencyCalendarInit from "./js/emergencyCalendar/index";

$(".emergency_interview_form").on("submit", handleEmergencyInterviewFormSubmit)
appEmergencyCalendarInit()