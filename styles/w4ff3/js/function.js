// JavaScript Document
"use strict";


$("ul.topmenu li:has(ul)").hover(function(){
  $(this).find("ul").slideDown("fast");
  },function(){
    $(this).find("ul").slideUp("fast");
  }
);

$("ul.topmenu > li").hover(function(){
  $(this).addClass('current');
},function(){
  $(this).removeClass('current');
});

$("ul.topmenu li a:has(ul)").addClass('with-child');

function encrypt_password(formfield, new_value) {
    if ($('#' + formfield).length && $('#' + new_value).length) {
        $('#' + new_value).val(btoa(encodeURIComponent($('#' + formfield).val())));
    }
}

function verify_password_match(password1, password2)
{

}

function now_submit(url, method, formname) {
    $.ajaxSetup({async: false});
    var returnData = null;
    if (method == 'post' && typeof(formname) != "undefined") {
        if ($('#hashpw').length && $('#password').length) {
            $('#hashpw').val(btoa(encodeURIComponent($('#password').val())));
            $('#password').val('');
        }
        var form_data = $('#' + formname).serializeArray();
        $.post(url, form_data, function (data) {
            returnData = data
        });
    }
    else if (method == 'get') {
        $.post(url, function (data) {
            returnData = data
        });
    }
    $.ajaxSetup({async: true});
    return returnData;
}

function get_data(container, urlaction, method, formname, slide)
{
    var contenth = $('#' + container).height();

    var speed = 500;

    if (!(typeof(container) != "undefined" || container != '')) {
    } else {

        if (typeof(urlaction) != "undefined" || urlaction != '') {
            var url = 'ajaxcore.php?m=' + urlaction;
        }
        else {
            var url = 'ajaxcore.php';
        }

        if (typeof(slide) == "undefined" || slide == '') {
            slide = 'false';
        }

        if (typeof(method) == "undefined") {
            var method = 'get';
        }

        var new_content = now_submit(url, method, formname);

		var searchcontrol = new_content.match(/[||+||]/);
		if (searchcontrol !== null)
		{
			var contentSplit=new_content.split("[||+||]");
			container=contentSplit[0];
			new_content=contentSplit[1];
			//alert(container);
		}

        if (container == "contentbox")
            var contentwrapper = 'wrappercontent';

        if (container == 'sitebox_left')
            var contentwrapper = 'wrappernavigation';

        var slide_up = function () {
            $('#' + container).animate({"height": "5px"}, speed, function () {
            });
        };

        var slide_down = function () {
            $('#' + container).animate({"height": contenth + "px"}, speed, function () {
            });
        };

       // var hiddenElements = $( "body" ).find( ":hidden" ).not( "script" );

        if (slide == 'true' && new_content && new_content != $('#' + container).html()) {
            $('#' + contentwrapper).animate({"height": "5px"}, speed, function () {
                $('#' + container).html(new_content);
                $('#' + contentwrapper).animate({"height": contenth + "px"}, speed, function () {
                });
            });
        }
        else if (new_content) {
            $('#' + container).html(new_content);
//            alert("Hier");
        }
    }
 //   set_screen();
}

function set_screen()
{

	var h1 = $(window).height();
	var header_height = $(header).height();
	var new_contentheight = h1 - (header_height + 50);
	$(wrapper).height(new_contentheight);
    $(wrappercontent).height(new_contentheight - 2);
 //   get_data('wrappercontent', 'sitebox_content', 'get', '#loginform', 'true');
}

function get_gidchannel(gid)
{
    var speed = 500;
    if (gid > 0)
    {
        var contentheight = $(wrappercontent).height();
        if (navigator.userAgent.match(/Android/i)) {
            alert(navigator.userAgent);
        }
        else {
            $(wrappercontent).animate({"height": "3px"}, speed, function () {
				$(wrappernavigation).width('0px');
				$(wrappercontent).animate({"width": "-250px"}, speed, function () {
					$(wrappernavigation).height('3px');
            		$(wrappernavigation).show();
                    $(wrappernavigation).animate({"width":"240px"}, speed, function() { });
                    get_data('sitebox_left', 'sitebox&gid=' + gid, 'get', false, 'false');
                    get_data('contentbox', 'maincontent&gid=' + gid, 'get', false, 'false');
//                    $(wrappernavigation).animate({"height":"3px"}, 1000, function() {
 //                       $(wrappernavigation).animate({"width":"240px"}, 1000, function() {
                            $(wrappernavigation).animate({"height": contentheight + "px"}, speed, function () {
                            $(wrappercontent).animate({"height": contentheight + "px"}, speed, function () { });
                });
//                        });
                    });
                });
//            });
        }
    }
}

function get_startsite()
{
    var speed = 500;
    var contentheight = $(wrappercontent).height();
    var contentwidth = $(wrappercontent).width();
    var new_contentwidth = contentwidth + 250;
    if (new_contentwidth <= 1230) {
        $(wrappercontent).animate({"height": "3px"}, speed, function () {
            get_data('contentbox', 'sitebox&gid=0', 'get', false, 'false');
            $(wrappernavigation).animate({"height": "3px"}, speed, function () {
                $(wrappernavigation).animate({"width": "0px"}, speed, function () {
                    $(wrappernavigation).animate({"height": "0px"}, speed, function () {
                        $(wrappernavigation).hide();
                        $(wrappercontent).animate({"width": new_contentwidth + "px"}, speed, function () {
                            $(wrappercontent).animate({"height": contentheight + "px"}, speed, function () {
                            });
                        });
                    });
                });
            });
        });
    }
}

/////////////////////////////////////////////////////////////////////////////////////////