(function ( $ ) {

    var Base = $.oc.foundation.base,
        BaseProto = Base.prototype

    var VideoUpload = function (element, options) {
        this.$el = $(element)
        this.options = options || {}

        $.oc.foundation.controlUtils.markDisposable(element)
        Base.call(this)
        this.init()
    }

    VideoUpload.prototype = Object.create(BaseProto)
    VideoUpload.prototype.constructor = VideoUpload

    VideoUpload.prototype.init = function() {

        this.uniqueId = this.$el.data('unique-id')
        this.$generateButton = $('.video-upload-btn', this.$el)
        this.$videoUrlInput = $('.video-upload-url', this.$el)
        this.$videosContainer = $('.video-upload-container', this.$el)
        this.$relationType = this.$el.data("relation-type")

        this.$el.on('click', '.video-upload-btn', this.proxy(this.onGenerateVideo))
        this.$el.on('click', '.video-upload-remove', this.proxy(this.onRemoveVideo))
        this.$generateButton.on('ajaxDone', this.proxy(this.onAddVideo))

        this.$el.one('dispose-control', this.proxy(this.dispose))

        if (this.options.isSortable === null) {
            this.options.isSortable = this.$el.hasClass('is-sortable')
        }

        if (this.options.isSortable) {
            this.bindSortable()
        }

        this.evalIsPopulated()

    };

    VideoUpload.prototype.dispose = function() {
        this.$el.off('dispose-control', this.proxy(this.dispose))
        this.$el.removeData('bkp.videoUpload')

        this.$el.off('click', '.video-upload-btn', this.proxy(this.onGenerateVideo))
        this.$el.off('click', '.video-upload-remove', this.proxy(this.onRemoveVideo))

        this.$el = null
        this.$generateButton = null
        this.$videoUrlInput = null
        this.$videosContainer = null
        this.$relationType = null

        this.options = null

        BaseProto.dispose.call(this)
    };

    //
    // Events
    //

    VideoUpload.prototype.onAddVideo = function(e, c, result) {
        let $result = $(result.result);
        this.$videosContainer.append($result);
        this.evalIsPopulated()
    };

    VideoUpload.prototype.onGenerateVideo = function (ev) {
        var self = this;
        var url = this.$videoUrlInput.val();
        var $btn = $(ev.target);

        $btn.request($btn.data('request-handler'), {
            data: {
                video_upload_url: url
            }
        });
    };

    VideoUpload.prototype.onRemoveVideo = function(ev) {
        var self = this,
            $object = $(ev.target).closest('.upload-object')

        $(ev.target)
            .closest('.video-upload-remove')
            .one('ajaxDone', function(){
                $object.remove();
                self.evalIsPopulated()
            })
            .request()

        ev.stopPropagation()
    }

    //
    // Sorting
    //

    VideoUpload.prototype.bindSortable = function() {
        var
            self = this,
            placeholderEl = $('<div class="upload-object upload-placeholder"/>').css({
                width: this.options.imageWidth,
                height: this.options.imageHeight
            })

        this.$videosContainer.sortable({
            itemSelector: 'div.upload-object.is-success',
            nested: false,
            tolerance: -100,
            placeholder: placeholderEl,
            handle: '.drag-handle',
            onDrop: function ($item, container, _super) {
                _super($item, container)
                self.onSortAttachments()
            },
            distance: 10
        })
    }

    VideoUpload.prototype.onSortAttachments = function() {
        if (this.options.sortHandler) {
            /*
             * Build an object of ID:ORDER
             */
            var orderData = {}

            this.$el.find('.upload-object.is-success')
                .each(function(index){
                    var id = $(this).data('id')
                    orderData[id] = index + 1
                })

            this.$el.request(this.options.sortHandler, {
                data: { sortOrder: orderData }
            })
        }
    }

    //
    // Helpers
    //

    VideoUpload.prototype.evalIsPopulated = function () {
        var isPopulated = !!$('.upload-object', this.$videosContainer).length

        if(isPopulated && this.$relationType == "attachOne") {
            this.$generateButton.addClass('disabled')
        } else {
            this.$generateButton.removeClass('disabled')
        }

        if(isPopulated){
            this.$videosContainer.css('padding-top', '20px');
        } else {
            this.$videosContainer.css('padding-top', '0');
        }
    }

    VideoUpload.DEFAULTS = {
        sortHandler: null,
        uniqueId: null,
        isSortable: null,
    }

    VideoUpload.prototype.triggerChange = function() {
        this.$el.closest('[data-field-name]').trigger('change.oc.formwidget')
    };

    //
    // Plugin definition
    //

    $.fn.videoUploader = function (option) {
        return this.each(function () {
            // $this = le div contenant tout ton html et data-control="videoupload"
            var $this = $(this)
            var data = $this.data('bkp.videoUpload')
            var options = $.extend({}, VideoUpload.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('bkp.videoUpload', (data = new VideoUpload(this, options)))
            if (typeof option == 'string') data[option].call($this)
        })
    }

    //
    // Plugin init
    //

    $(document).render(function() {
        $('[data-control="videoupload"]').videoUploader()
    });

}( window.jQuery ));