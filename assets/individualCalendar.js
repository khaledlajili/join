import './styles/app.css';
import $ from 'jquery'

import '@fortawesome/fontawesome-free/js/all.min.js'
import 'lodash'
import './js/polyfill'
import './js/calendarTheme.js'
import appInterviewCalendarInit from './js/individualCalendar/index';
import handleInterviewFormSubmit from "./js/individualCalendar/handleInterviewFormSubmit";

$(".interview_form").on("submit", handleInterviewFormSubmit)
appInterviewCalendarInit()