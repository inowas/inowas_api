function panelOpened(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-right');

    $(e.target)
        .prev('.panel-heading')
        .addClass('inowas-panel-heading-active');
}

function panelClosed(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-right glyphicon-chevron-down')

    $(e.target)
        .prev('.panel-heading')
        .removeClass('inowas-panel-heading-active');
}

$('#accordeon')
    .on('hide.bs.collapse', panelClosed)
    .on('show.bs.collapse', panelOpened);