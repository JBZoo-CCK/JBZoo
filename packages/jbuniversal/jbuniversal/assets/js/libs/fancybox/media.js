/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    /* Media helper for fancyBox version: 1.0.6 (Fri, 14 Jun 2013) */
    (function(d){var j=function(a,c,b){b=b||"";"object"===d.type(b)&&(b=d.param(b,!0));d.each(c,function(b,c){a=a.replace("$"+b,c||"")});b.length&&(a+=(0<a.indexOf("?")?"&":"?")+b);return a};d.fancybox.helpers.media={defaults:{youtube:{matcher:/(youtube\.com|youtu\.be|youtube-nocookie\.com)\/(watch\?v=|v\/|u\/|embed\/?)?(videoseries\?list=(.*)|[\w-]{11}|\?listType=(.*)&list=(.*)).*/i,params:{autoplay:1,autohide:1,fs:1,rel:0,hd:1,wmode:"opaque",enablejsapi:1},type:"iframe",url:"//www.youtube.com/embed/$3"},
    vimeo:{matcher:/(?:vimeo(?:pro)?.com)\/(?:[^\d]+)?(\d+)(?:.*)/,params:{autoplay:1,hd:1,show_title:1,show_byline:1,show_portrait:0,fullscreen:1},type:"iframe",url:"//player.vimeo.com/video/$1"},metacafe:{matcher:/metacafe.com\/(?:watch|fplayer)\/([\w\-]{1,10})/,params:{autoPlay:"yes"},type:"swf",url:function(a,c,b){b.swf.flashVars="playerVars="+d.param(c,!0);return"//www.metacafe.com/fplayer/"+a[1]+"/.swf"}},dailymotion:{matcher:/dailymotion.com\/video\/(.*)\/?(.*)/,params:{additionalInfos:0,autoStart:1},
    type:"swf",url:"//www.dailymotion.com/swf/video/$1"},twitvid:{matcher:/twitvid\.com\/([a-zA-Z0-9_\-\?\=]+)/i,params:{autoplay:0},type:"iframe",url:"//www.twitvid.com/embed.php?guid=$1"},twitpic:{matcher:/twitpic\.com\/(?!(?:place|photos|events)\/)([a-zA-Z0-9\?\=\-]+)/i,type:"image",url:"//twitpic.com/show/full/$1/"},instagram:{matcher:/(instagr\.am|instagram\.com)\/p\/([a-zA-Z0-9_\-]+)\/?/i,type:"image",url:"//$1/p/$2/media/?size=l"},google_maps:{matcher:/maps\.google\.([a-z]{2,3}(\.[a-z]{2})?)\/(\?ll=|maps\?)(.*)/i,
    type:"iframe",url:function(a){return"//maps.google."+a[1]+"/"+a[3]+""+a[4]+"&output="+(0<a[4].indexOf("layer=c")?"svembed":"embed")}}},beforeLoad:function(a,c){var b=c.href||"",g=!1,f,e,h;for(f in a)if(a.hasOwnProperty(f)&&(e=a[f],h=b.match(e.matcher))){g=e.type;b=d.extend(!0,{},e.params,c[f]||(d.isPlainObject(a[f])?a[f].params:null));b="function"===d.type(e.url)?e.url.call(this,h,b,c):j(e.url,h,b);break}g&&(c.href=b,c.type=g,c.autoHeight=!1)}}})(jQuery);

})(jQuery, window, document);