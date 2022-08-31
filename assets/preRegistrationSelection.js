// any CSS you import will output into a single css file (app.css in this case)

import './styles/app.css'

// import JS

import '@fortawesome/fontawesome-free/js/all.min.js'
import 'lodash'
import './js/polyfill'
import './js/appTheme'
import candidateSelection from './js/selection/Selection'
candidateSelection('/admin/preregistration/result/submit', '/admin/preregistration/result/data/')

