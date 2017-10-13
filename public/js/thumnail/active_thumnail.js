jQuery(function($) { 
	imgs=$("img");
        for(i=0,n=imgs.length;i<n;i++){
            $(imgs[i]).parent().attr("href",$(imgs[i]).attr("src"));
            $(imgs[i]).parent().attr("rel","prettyPhoto");
        }        
});