var smartbgimage="<h6 class='margin-top-10 semi-bold'>Background</h6><img src='img/pattern/graphy-xs.png' data-htmlbg-url='img/pattern/graphy.png' width='22' height='22' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/tileable_wood_texture-xs.png' width='22' height='22' data-htmlbg-url='img/pattern/tileable_wood_texture.png' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/sneaker_mesh_fabric-xs.png' width='22' height='22' data-htmlbg-url='img/pattern/sneaker_mesh_fabric.png' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/nistri-xs.png' data-htmlbg-url='img/pattern/nistri.png' width='22' height='22' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/paper-xs.png' data-htmlbg-url='img/pattern/paper.png' width='22' height='22' class='bordered cursor-pointer'>";$("#smart-bgimages").fadeOut();$("#demo-setting").click(function(){$("#ribbon .demo").toggleClass("activate")});$('input[type="checkbox"]#smart-fixed-nav').click(function(){if($(this).is(":checked")){$.root_.addClass("fixed-header");nav_page_height()}else{$('input[type="checkbox"]#smart-fixed-ribbon').prop("checked",false);$('input[type="checkbox"]#smart-fixed-navigation').prop("checked",false);$.root_.removeClass("fixed-header");$.root_.removeClass("fixed-navigation");$.root_.removeClass("fixed-ribbon")}});$('input[type="checkbox"]#smart-fixed-ribbon').click(function(){if($(this).is(":checked")){$('input[type="checkbox"]#smart-fixed-nav').prop("checked",true);$.root_.addClass("fixed-header");$.root_.addClass("fixed-ribbon");$('input[type="checkbox"]#smart-fixed-container').prop("checked",false);$.root_.removeClass("container")}else{$('input[type="checkbox"]#smart-fixed-navigation').prop("checked",false);$.root_.removeClass("fixed-ribbon");$.root_.removeClass("fixed-navigation")}});$('input[type="checkbox"]#smart-fixed-navigation').click(function(){if($(this).is(":checked")){$('input[type="checkbox"]#smart-fixed-nav').prop("checked",true);$('input[type="checkbox"]#smart-fixed-ribbon').prop("checked",true);$.root_.addClass("fixed-header");$.root_.addClass("fixed-ribbon");$.root_.addClass("fixed-navigation");$('input[type="checkbox"]#smart-fixed-container').prop("checked",false);$.root_.removeClass("container")}else{$.root_.removeClass("fixed-navigation")}});$('input[type="checkbox"]#smart-rtl').click(function(){if($(this).is(":checked")){$.root_.addClass("smart-rtl")}else{$.root_.removeClass("smart-rtl")}});$('input[type="checkbox"]#smart-fixed-container').click(function(){if($(this).is(":checked")){$.root_.addClass("container");$('input[type="checkbox"]#smart-fixed-ribbon').prop("checked",false);$.root_.removeClass("fixed-ribbon");$('input[type="checkbox"]#smart-fixed-navigation').prop("checked",false);$.root_.removeClass("fixed-navigation");if(smartbgimage){$("#smart-bgimages").append(smartbgimage).fadeIn(1e3);$("#smart-bgimages img").bind("click",function(){var e=$(this);var t=$("html");bgurl=e.data("htmlbg-url");t.css("background-image","url("+bgurl+")")});smartbgimage=null}else{$("#smart-bgimages").fadeIn(1e3)}}else{$.root_.removeClass("container");$("#smart-bgimages").fadeOut()}});$("#reset-smart-widget").bind("click",function(){$("#refresh").click();return false});$("#smart-styles > a").bind("click",function(){var e=$(this);var t=$("#logo img");$.root_.removeClassPrefix("smart-style").addClass(e.attr("id"));t.attr("src",e.data("skinlogo"));$("#smart-styles > a #skin-checked").remove();e.prepend("<i class='fa fa-check fa-fw' id='skin-checked'></i>")})