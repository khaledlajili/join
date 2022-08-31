import $ from "jquery";

const addRemoveOptionsEditFormInit = () => {
    var option_list = $('.option')

    $.each(option_list, (option) => {
        addOptionFormDeleteLink($(option_list[option]))
    })

    $('.add-another-collection-widget-edit-form').on('click', addOption)

    function addOption() {
        let list = $($(this).attr('data-list-selector')),
            // Try to find the counter of the list or use the length of the list
            counter = list.data('widget-counter') || list.children().length,

            // grab the prototype template
            newWidget = list.attr('data-prototype')
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter)
        // Increase the counter
        counter++
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter)

        // create a new list element and add it to the list
        const newElem = $(list.attr('data-widget-tags')).html(newWidget)
        newElem.appendTo(list)
        addOptionFormDeleteLink(newElem)
    }

    function addOptionFormDeleteLink(item) {
        const removeFormButton = item.find(".remove-option-button")
        removeFormButton.on('click', (e) => {
            e.preventDefault();
            // remove the li for the tag form
            item.remove();
        });
    }

}

export default addRemoveOptionsEditFormInit;
