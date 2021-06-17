function initializeSortableRelationList($container) {

	var requestData = JSON.parse(JSON.stringify(eval("({" + $container.attr('data-request-data') + "})")));

	$container.find('table tbody').each(function() {

		var $el = $(this);

		$el.sortable({
			itemSelector: 'tr',
			handle: '.relation-reorder-drag-handle',
			placeholder: '<tr class="placeholder"><td class="relation-reorder-drag-placeholder" colspan="100">&nbsp;</td></tr>',
			onDrop: function($item, container, _super) {

				var reorderedIds = $el.find('.relation-reorder-model-id').map(function() {
					return $(this).val();
				}).get();

				$.request('onRelationReorder', {
					data: {
						relationName: requestData._relation_field,
						reorderedIds: reorderedIds
					}
				});

				_super($item, container);

			}
		});

	});

}

$(document).render(function() {

	$('.relation-behavior:has(.relation-reorder-drag-handle)').each(function() {
		initializeSortableRelationList($(this));
	});

});
