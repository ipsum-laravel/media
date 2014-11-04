;(function ( $, window, document, undefined ) {

    var pluginName = "formFileupload",
    defaults = {
        message: true,
        messageSelecteur: '#fileupload-message',
        afterDone: function() {},
        afterFail: function() {}
    };

    // The actual plugin constructor
    function Plugin ( element, options ) {
        this.element = element;
        this.$element = $(this.element);
        this.settings = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    // Avoid Plugin.prototype conflicts
    $.extend(Plugin.prototype, {
        init: function () {
            var self = this;

            this.initHtml();

            this.$element.fileupload({
                dataType: 'json',

                add: function (e, data) {
                    data.submit();
                },

                done: function (e, data) {
                    if (self.settings.message) {
                        $(self.settings.messageSelecteur).prepend(data.result.notifications);
                    }

                    self.$element.removeClass('fileupload-error');

                    self.settings.afterDone(e, data);
                },

                progressall: function (data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    self.$element.find('.fileupload-progress').val(progress).change();
                },

                fail:function(e, data){
                    self.$element.addClass('fileupload-error');

                    self.settings.afterFail(e, data);
                }
            })
            .on('dragover', function() {
                self.$element.addClass('fileupload-hover');
            })
            .on('drop', function() {
                self.$element.removeClass('fileupload-hover');
            })
            .on('dragleave', function() {
                self.$element.removeClass('fileupload-hover');
            });

            $('.fileupload-bouton', self.$element).click(function(){
                // Simulate a click on the file input button
                // to show the file browser dialog
                self.$element.find('input[type=file]').click();
                return false;
            });
        },

        initHtml: function () {
            this.$element
            .addClass('fileupload')
            .append('<input class="fileupload-progress" name="progress" type="text" value="0" data-width="100" data-height="100" data-fgColor="#30b1f2" data-readOnly="1" data-bgColor="#a5a8ac" />')
            .append('<p><a class="fileupload-bouton" href="#">Sélectionner des fichiers</a><br>ou déposer ici</p>');

            // Initialize the knob plugin
            this.$element.find('.fileupload-progress').knob();
        }
    });

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[ pluginName ] = function ( options ) {
        this.each(function() {
            if ( !$.data( this, "plugin_" + pluginName ) ) {
                $.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
            }
        });

        // chain jQuery functions
        return this;
    };

})( jQuery, window, document );
