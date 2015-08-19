function panelOpened(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-right');
}

function panelClosed(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-right glyphicon-chevron-down')
}

$('#mapoverlaypanel')
    .on('hide.bs.collapse', panelClosed)
    .on('show.bs.collapse', panelOpened);