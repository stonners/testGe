$(window).on('ajaxUpdateComplete', function() {
    $('.file-upload-focus-handle').unbind();
    $('.file-upload-focus-handle').mousedown(function(e) {
        if(e.which===1) {
            var button = $(this);
            var parent = button.parent();
            var parent_height = parent.innerHeight();
            var parent_width = parent.innerWidth();

            button.css('top', button.css('top'));
            button.css('left', button.css('left'));
            var top = parseInt(button.css('top'));
            var left = parseInt(button.css('left'));

            var min_x = 0,
                min_y = 0,
                max_x = parent_width,
                max_y = parent_height,
                drag_start_ypos = e.clientY,
                drag_start_xpos = e.clientX;

            $(window).on('mousemove',function(e) {
                button.addClass('drag');
                var new_top = top+(e.clientY-drag_start_ypos);
                button.css({top:new_top+'px'});
                if(new_top<min_y) { button.css({top:min_y+'px'}); }
                if(new_top>max_y) { button.css({top:max_y+'px'}); }

                var new_left = left+(e.clientX-drag_start_xpos);
                button.css({left:new_left+'px'});
                if(new_left<min_x) { button.css({left:min_x+'px'}); }
                if(new_left>max_x) { button.css({left:max_x+'px'}); }
            });

            $(window).on('mouseup',function(e) {
                 if(e.which===1) {
                    $('.button').removeClass('drag');
                    $(window).unbind('mouseup');
                    $(window).unbind('mousemove');

                    var y = (parseInt(button.css('top')) / parent_height) * 100;
                        x = (parseInt(button.css('left')) / parent_width) * 100;
                    $.request('onUpdateFocusPoint', {'data' : {
                        'file' : parent.attr('data-file-id'),
                        'x' : x,
                        'y' : y
                    }});
                 }
            });
        }
    });

});
