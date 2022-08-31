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
import draggableInit from './draggable';
import kanbanInit from "./kanban";
import choicesInit from './choices';
import SelectQuestionTypeInit from "./pre_registration/selectQuestionType";
import addRemoveOptionsInit from "./addRemoveOptions";

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
docReady(draggableInit);
docReady(kanbanInit);
docReady(choicesInit);
docReady(SelectQuestionTypeInit);
docReady(addRemoveOptionsInit)

