//dropdown checkboxes


 ko.components.register('dashboard-grid', {
    viewModel: {
        createViewModel: function (controller, componentInfo) {
            var ViewModel = function (controller, componentInfo) {
                var grid = null;

                this.widgets = controller.widgets;

                this.afterAddWidget = function (items) {
                    if (grid == null) {
                        grid = jQuery(componentInfo.element).find('.grid-stack').gridstack({
                            auto: false
                        }).data('gridstack');
                    }

                    var item = _.find(items, function (i) { return i.nodeType == 1 });
                    var t = grid.addWidget(item);
                    jQuery(item).find('.item-title').text(t.data('gs-id'));

                    ko.utils.domNodeDisposal.addDisposeCallback(item, function () {
                        grid.removeWidget(item);
                    });
                };
            };

            return new ViewModel(controller, componentInfo);
        }
    },
    template:
        [
            '<div class="grid-stack" data-bind="foreach: {data: widgets, afterRender: afterAddWidget}">',
            '   <div class="grid-stack-item" data-bind="attr: {\'data-gs-id\':$data.id , \'data-gs-x\': $data.x, \'data-gs-y\': $data.y,\'data-gs-x\': $data.x,\'data-gs-max-width\': $data.maxWidth, \'data-gs-max-height\': $data.maxHeight, \'data-gs-width\': $data.width, \'data-gs-height\': $data.height, \'data-gs-auto-position\': $data.auto_position}">',
            '       <div class="grid-stack-item-content"><span class="item-title"></span><button data-bind="click: $root.deleteWidget">X</button></div>',
            '   </div>',
            '</div> '
        ].join('')
}); 


var Controller = function (widgets) {
    var self = this;

    this.widgets = ko.observableArray(widgets);

     this.addNewWidget = function (name,el) {
                    this.widgets.push({
                        x: 0,
                        y: 0,
                        width: 3,
                        height: 1,
                        maxWidth: 12,
                        maxHeight: 1,
                        auto_position: true,
                        id: name,
                    });
                    return false;
                };

    this.deleteWidget = function (item) {
        self.widgets.remove(item);
        jQuery('#wyz-business-filter-keys li').each(function(){
            if(jQuery(this).find('a').data('value')==item.id)
                jQuery(this).show();
        });
        return false;
    };
};

var widgets = [
                {x: 0, y: 0, width: 2, height: 2},
                {x: 2, y: 0, width: 4, height: 2},
                {x: 6, y: 0, width: 2, height: 4},
                {x: 1, y: 2, width: 4, height: 2}
            ];
businessFilters.controller = new Controller(widgets);
ko.applyBindings(businessFilters.controller);
