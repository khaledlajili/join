/* -------------------------------------------------------------------------- */
/*                                   Tooltip                                  */
/* -------------------------------------------------------------------------- */
import {Tooltip} from "bootstrap";

const tooltipInit = () => {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );

  tooltipTriggerList.map(
    (tooltipTriggerEl) => new Tooltip(tooltipTriggerEl,{
      trigger:'hover'
    })
  );
};

export default tooltipInit;
