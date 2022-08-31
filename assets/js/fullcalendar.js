/* -------------------------------------------------------------------------- */
/*                                FullCalendar                                */
/* -------------------------------------------------------------------------- */
import {Calendar} from "@fullcalendar/core";
import utils from './utils';
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
const { merge } = window._;

export const renderCalendar = (el, option) => {
  const options = merge(
    {
      plugins : [ dayGridPlugin , interactionPlugin , timeGridPlugin , timeGridPlugin , listPlugin],
      initialView: 'timeGridWeek',
      editable: true,
      eventStartEditable: false,
      displayEventTime: false,
      direction: document.querySelector('html').getAttribute('dir'),
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
      },
      buttonText: {
        month: 'Month',
        week: 'Week',
        day: 'Day',
      },
    },
    option
  );
  const calendar = new Calendar(el, options);
  calendar.render();
  document
    .querySelector('.navbar-vertical-toggle')
    ?.addEventListener('navbar.vertical.toggle', () => calendar.updateSize());
  return calendar;
};

export const fullCalendarInit = () => {
  const calendars = document.querySelectorAll('[data-collective]');
  calendars.forEach(item => {
    const options = utils.getData(item, 'options');
    renderCalendar(item, options);
  });
};

