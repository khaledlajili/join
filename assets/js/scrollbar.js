/* -------------------------------------------------------------------------- */
/*                                 Scrollbars                                 */
/* -------------------------------------------------------------------------- */
import OverlayScrollbars from "overlayscrollbars"

const scrollbarInit = () => {
  Array.prototype.forEach.call(
    document.querySelectorAll(".scrollbar-overlay"),
    (el) => new OverlayScrollbars(el, {
      scrollbars: {
        autoHide: "leave",
        autoHideDelay: 200
      },
    })
  );
};

export default scrollbarInit;
