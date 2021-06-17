;(function($) {

    $.oc.embeddedPartials = $.oc.embeddedPartials || {};

    var $currentScript = $(document.currentScript);
    var widgetId = $currentScript.attr('data-widget-id');
    var partialsData = $currentScript.attr('data-partials');

    if (!partialsData || !widgetId) {
        return;
    }

    var partials = JSON.parse(atob(partialsData));

    $.oc.embeddedPartials[widgetId] = partials;

}(jQuery));
