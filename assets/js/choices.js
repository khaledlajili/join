import utils from './utils';
import Choices from "choices.js";

/* -------------------------------------------------------------------------- */
/*                                   choices                                   */
/* -------------------------------------------------------------------------- */
const choicesInit = () => {
  if (Choices) {
    const elements = document.querySelectorAll('.js-choice');
    elements.forEach((item) => {
      const userOptions = utils.getData(item, 'options');
      const choices = new Choices(item, {
        itemSelectText: '',
        ...userOptions,
      });

      return choices;
    });
  }
};

export default choicesInit;
