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

    /* Buttons helper for fancyBox version: 1.0.5 (Mon, 15 Oct 2012) */
    (function(e){var d=e.fancybox;d.helpers.buttons={defaults:{skipSingle:!1,position:"top",tpl:'<div id="fancybox-buttons"><ul><li><a class="btnPrev" title="Previous" href="javascript:;"></a></li><li><a class="btnPlay" title="Start slideshow" href="javascript:;"></a></li><li><a class="btnNext" title="Next" href="javascript:;"></a></li><li><a class="btnToggle" title="Toggle size" href="javascript:;"></a></li><li><a class="btnClose" title="Close" href="javascript:jQuery.fancybox.close();"></a></li></ul></div>'},
    list:null,buttons:null,beforeLoad:function(c,a){c.skipSingle&&2>a.group.length?(a.helpers.buttons=!1,a.closeBtn=!0):a.margin["bottom"===c.position?2:0]+=30},onPlayStart:function(){this.buttons&&this.buttons.play.attr("title","Pause slideshow").addClass("btnPlayOn")},onPlayEnd:function(){this.buttons&&this.buttons.play.attr("title","Start slideshow").removeClass("btnPlayOn")},afterShow:function(c,a){var b=this.buttons;b||(this.list=e(c.tpl).addClass(c.position).appendTo("body"),b={prev:this.list.find(".btnPrev").click(d.prev),
    next:this.list.find(".btnNext").click(d.next),play:this.list.find(".btnPlay").click(d.play),toggle:this.list.find(".btnToggle").click(d.toggle)});0<a.index||a.loop?b.prev.removeClass("btnDisabled"):b.prev.addClass("btnDisabled");a.loop||a.index<a.group.length-1?(b.next.removeClass("btnDisabled"),b.play.removeClass("btnDisabled")):(b.next.addClass("btnDisabled"),b.play.addClass("btnDisabled"));this.buttons=b;this.onUpdate(c,a)},onUpdate:function(c,a){var b;this.buttons&&(b=this.buttons.toggle.removeClass("btnDisabled btnToggleOn"),
    a.canShrink?b.addClass("btnToggleOn"):a.canExpand||b.addClass("btnDisabled"))},beforeClose:function(){this.list&&this.list.remove();this.buttons=this.list=null}}})(jQuery);

})(jQuery, window, document);