// any CSS you import will output into a single css file (app.css in this case)

import './styles/app.css';

// import JS


import '@fortawesome/fontawesome-free'
import 'lodash'
import './js/polyfill'
import './js/InterviewTheme'
import './js/interview_evaluation/handleIndividualInterviewSheetPartFormSubmit'
import $ from "jquery";
import showEditFormModel from "./js/interview_evaluation/showEditFormModel";
$('.kanban-item .stretched-link').on('click', showEditFormModel)
