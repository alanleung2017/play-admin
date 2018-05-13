/**
 * Created by Alan on 2017/1/14.
 */
$(function(){
    $('.list-group-item').click(function(){
        $(this).addClass('active');
        $('.active').not($(this)).removeClass('active');
    });

    /*左侧菜单阴影*/
    $(".sub-menu li a").each(function(){
        var $thisa = $(this);
        if($thisa[0].href==String(window.location)){
            $("ul.page-sidebar-menu li").removeClass("active");
            $("ul.page-sidebar-menu li").removeClass("open");
            $thisa.parent().addClass("active");
            $thisa.parent().parent().parent().addClass("active");
            $thisa.parent().parent().parent().addClass("open");
        }
    });
});