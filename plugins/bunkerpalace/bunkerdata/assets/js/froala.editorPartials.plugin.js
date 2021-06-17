;(function($) {

    function encodeParams(params) {
        return btoa(JSON.stringify(params));
    }

    function getEditorWidget(froalaInstance) {
        return froalaInstance.$el.parents('[data-control="richeditor"]');
    }

    function getPartialsForEditorInstance(froalaInstance) {
        var widgetId = getEditorWidget(froalaInstance).attr('id');
        return $.oc.embeddedPartials[widgetId];
    }

    function buildPartialsOptionsList(partials) {
        
        if (!partials) {
            return '';
        }

        return Object.keys(partials).map(function(partialKey) {
            return '<li><a class="fr-command" data-cmd="embedPartials" data-param1="' + partialKey + '">' + partials[partialKey]['name'] + '</a></li>';
        });
    }

    $.FroalaEditor.RegisterCommand('embedPartials', {
        title: 'Partials',
        type: 'dropdown',
        icon: '<i class="icon-th-large"></i>',
        undo: true,
        focus: true,
        refreshAfterCallback: true,
        refreshOnShow: function($btn, $dropdown) {
            var partials = getPartialsForEditorInstance(this);
            var $list = $dropdown.find('ul.fr-dropdown-list');
            $list.html(buildPartialsOptionsList(partials));
        },
        callback: function(cmd, val, params) {

            var $editor = getEditorWidget(this);
            var partials = getPartialsForEditorInstance(this);
            var $partialNode = $('<figure>' + partials[val]['name'] + '</figure>');

            $partialNode.attr({
                'contenteditable': false,
                'data-editor-embedded-partial': partials[val]['partial'],
                'tabindex': '0',
                'draggable': 'true',
                'data-ui-block': 'true',
                'class': 'fr-draggable'
            });

            if (partials[val]['params']) {
                $partialNode.attr({
                    'data-editor-embedded-partial-params': encodeParams(partials[val]['params']),
                });
            }

            $editor.richEditor('insertUiBlock', $partialNode);
        }
    });

}(jQuery));
