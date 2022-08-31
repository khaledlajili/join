// any CSS you import will output into a single css file (app.css in this case)

import './styles/app.css';

// import JS

import '@fortawesome/fontawesome-free/js/all.min.js'
import 'lodash'
import './js/polyfill'
import './js/preRegistrationTheme'
import './js/pre_registration/handlePreRegistrationFormSubmit'
import './js/pre_registration/saveFieldsOrder'
import $ from "jquery";
import showEditFormModel from "./js/pre_registration/showEditFormModel";
$('.kanban-item .stretched-link').on('click', showEditFormModel)
