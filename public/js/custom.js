function typeOf(value) {
    var s = typeof value;
    if (s === 'object') {
        if (value) {
            if (value instanceof Array) {
                s = 'array';
            }
        } else {
            s = 'null';
        }
    }
    return s;
}

var ajax = function(options, form){
    var defaults = {
        blockUi: true,
        url: '',
        type: 'get',
        dataType: 'json',
        successCallback: function(response){},
        errorCallback: function(response){},
        success: function(response){
            var that = this;
            if(typeof response !== "undefined"){
                if(response.msg){
                    if(response.msg.show){
                        Swal.fire(response.msg.title, response.msg.text, response.msg.type);
                    }
                }
                if(response.status){
                    that.successCallback(response, form);
                }
            }
        },
        error: function(request, b, c){
            var that = this;
            var response = request.responseJSON;
            var msg = 'Something wrong, please try again';
            if(typeOf(response) === 'object' && response.errors != null){
                if(typeOf(response.errors) === "object"){
                    for(var x in response.errors){
                        if(response.errors.hasOwnProperty(x)){
                           if(typeOf(response.errors[x]) === "array" && response.errors[x].length){
                               msg = response.errors[x][0];
                               break;
                           }
                           else if(typeOf(response.errors[x]) === "string" && response.errors[x].length){
                            msg = response.errors[x];
                            break;
                           }
                        }
                    }
                }
                else if(typeOf(response.errors) === "array" && response.errors.length){
                    msg = response.errors[0];
                }
            }
            Swal.fire('Error', msg, 'error');
            that.errorCallback(response, form);
        },
        complete: function(){
            unblockUi();
        }
    };
    options = $.extend(defaults, options);
    if(options.blockUi){
        blockUi();
    }
    $.ajax(options);
};

var select2 = function(el){
    el.select2({
        formatNoMatches: function () {
            return "No Record Found";
        },
    });
};
var select2Tag = function(el){
    el.select2({
        tags:true,
        formatNoMatches: function () {
            return "No Record Found";
        },
    });
};

var blockUi = function(){
    $('.preloader').fadeIn();
};
var unblockUi = function(){
    $('.preloader').fadeOut();
};

var setJsonViewer = function (){
    var pre = $(document).find('.json-viewer[data-init="false"]');
    if(pre.length){
        pre.each(function(){
            var el = $(this);
            var json = JSON.parse(el.html());
            el.html();
            el.jsonViewer(json, {
                collapsed: true,
                rootCollapsable: false,
                withQuotes: false,
                withLinks: true
            });
            el.attr('data-init', 'true');
        });
    }
}

$(function(){
    $(document).on('submit', '.ajax-form', function(e){
        e.preventDefault();
        var el = $(this);
        var success = el.attr('data-success');
        var error = el.attr('data-error');
        var options = {
            url: el.attr('action'),
            type: el.attr('method'),
            data: el.serialize(),
        };
        if(typeof success !== "undefined" && typeof formSubmission[success] === "function"){
            options.successCallback = formSubmission[success];
        }
        if(typeof error !== "undefined" && typeof formSubmission[error] === "function"){
            options.errorCallback = formSubmission[error];
        }
        ajax(options, el);
    });

    $('.default-select2').select2({
        formatNoMatches: function () {
            return "No Record Found";
        },
    });
    select2($('.default-select2'));
    select2Tag($('.default-select2-tag'));

});