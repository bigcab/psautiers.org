

function preload()
{
    // Find all preload images
    $("img.preload").each(function()
    {
        var element = $(this);
 
        // Store the original src
        var originalSrc = element.attr("src");
 
        // Replace the image with a spinner
        element.attr("src", "jquery/spinner.gif");
//        element.attr("width", "32");
//        element.attr("height", "32");
        
        // Show spinner
        element.show();
 
        // Load the original image
        $('<img />').attr('src', originalSrc).load(function(){
 
            // Image is loaded, replace the spinner with the original
            element.attr("src", originalSrc);
            element.attr("width", "75");
            element.attr("height", "75");
        });
    });
}

function mycarousel_itemLoadCallback(carousel, state)
{
    
    for (var i = carousel.first; i <= carousel.last; i++) {
        if (carousel.has(i)) {
            continue;
        }

        if (i > mycarousel_itemList.length) {
            break;
        }
        var item=mycarousel_itemList[i-1];
        var id=item.id;
        var url=item.url;
        
        carousel.add(i, mycarousel_getItemHTML(mycarousel_itemList[i-1]));
    }
    preload();
};

/**
 * Item html creation helper.
 */
function mycarousel_getItemHTML(item)
{
    return '<a href=\"show.php?id_base='+id_base+'&id_recueil='+id_recueil+'&id_piece='+item.id+'\"><img class=\"preload\"  src=\"' + item.url + '\" width=\"75\" height=\"75\" title=\"'+item.title+'\"  /></a>';
};

jQuery(document).ready(function() {
    jQuery('#mycarousel').jcarousel({
        size: mycarousel_itemList.length,
        wrap: 'circular',
        itemLoadCallback: {onBeforeAnimation: mycarousel_itemLoadCallback}
    });
    preload();
});
