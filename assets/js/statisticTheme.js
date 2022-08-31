import { docReady } from './utils';
import handleNavbarVerticalCollapsed from './navbar-vertical';
import detectorInit from './detector';
import tooltipInit from './tooltip';
import popoverInit from './popover';
import navbarTopDropShadow from './navbar-top';
import toastInit from './toast';
import navbarDarkenOnScroll from './navbar-darken-on-scroll';
import navbarComboInit from './navbar-combo';
import themeControl from './theme-control';
import dropdownOnHover from './dropdown-on-hover';
import scrollbarInit from './scrollbar';
import treeviewInit from './treeview';
import choices from "./choices";

//charts

import productShareDoughnutInit from './charts/chartjs/product-share-doughnut';
import topProductsInit from './charts/echarts/top-products';
import bounceRateChartInit from './charts/echarts/bounce-rate';
import realTimeUsersChartInit from './charts/echarts/real-time-users';
import sessionByBrowserChartInit from './charts/echarts/session-by-browser';
import sessionByCountryChartInit from './charts/echarts/session-by-country';
import usersByTimeChartInit from './charts/echarts/users-by-time';
import basicEchartsInit from './charts/echarts/basic-echarts';
import audienceChartInit from './charts/echarts/audience';
import bandwidthSavedInit from './charts/echarts/bandwidth-saved';
import FeedbackStatInit from './charts/echarts/satisfactionChart';


/* -------------------------------------------------------------------------- */
/*                            Theme Initialization                            */
/* -------------------------------------------------------------------------- */

docReady(detectorInit);
docReady(handleNavbarVerticalCollapsed);
docReady(navbarTopDropShadow);
docReady(tooltipInit);
docReady(popoverInit);
docReady(toastInit);
docReady(navbarDarkenOnScroll);
docReady(navbarComboInit);
docReady(themeControl);
docReady(dropdownOnHover);
docReady(scrollbarInit);
docReady(treeviewInit);
docReady(choices)
docReady(bandwidthSavedInit);
docReady(FeedbackStatInit);
docReady(topProductsInit);
docReady(sessionByBrowserChartInit);










