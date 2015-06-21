jQuery.preloadImages = function()
{
    for(var i = 0; i<arguments.length; i++)
        jQuery("<img>").attr("src", arguments[i]);
}
//jQuery.preloadImages("key.gif", "keyo.gif", "rss.gif", "rsso.gif", "sel.gif", "selo.gif");

jQuery(document).ready(function(){
	
    $("#iconbar li a").hover(
        function(){
            var iconName = $(this).children("img").attr("src");
            var extensaoImg = iconName.split(".")[1];
            var origen = iconName.split("."+extensaoImg)[0];
            
            $(this).children("img").attr({
                src: "" + origen + "."+extensaoImg
            });
            $(this).css("cursor", "pointer");
            $(this).animate({
                width: "140px",
                height: "40px"
            }, {
                queue:false,
                duration:"normal"
            } );
            $(this).children("span").animate({
                opacity: "show"
            }, "fast");
        },
        function(){
            var iconName = $(this).children("img").attr("src");
            var extensaoImg = iconName.split(".")[1];
            var origen = iconName.split(".")[0];
            $(this).children("img").attr({
                src: "" + origen + "."+extensaoImg
            });
            $(this).animate({
                width: "32px",
                height: "40px"
            }, {
                queue:false,
                duration:"normal"
            } );
            $(this).children("span").animate({
                opacity: "hide"
            }, "fast");
        });
});