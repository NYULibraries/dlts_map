YUI.add('dlts-carousel', function(Y) {
    /**
     * Create a DLTS Carousel control.
     *
     * The Carousel module provides a widget for browsing among a set of like
     * objects represented pictorially.
     *
     * @class DLTSCarousel
     * @extends Widget
     * @param config {Object} Configuration options for the widget
     * @constructor
     */      
    function DLTSCarousel() {
        DLTSCarousel.superclass.constructor.apply(this, arguments);
    }
          
    /**
     * The identity of the widget.
     *
     * @property DLTSCarousel.NAME
     * @type String
     * @default "carousel"
     * @readOnly
     * @protected
     * @static
     */
    DLTSCarousel.NAME = "carousel";
      
    /**
     * @event afterPageScroll
     * @description          fires after the Carousel page scrolls its view port.
     *                       The page visible in the view port are passed back.
     * @param {Event}  ev    The <code>afterPageScroll</code> event
     * @param {Number} pos   The index of the page visible in the view port
     *                       
     * @type Event.Custom
     */
    var AFTERPAGESCROLL_EVENT = "afterPageScroll";

    /**
     * Extend Gallery Carousel
     * Add afterPageScroll events. Override few scrolls prototype methods.
     */
    Y.DLTSCarousel = Y.extend(DLTSCarousel, Y.Carousel, {

        /**
         * Scroll the Carousel to make the item at index visible.
         *
         * @method scrollTo
         * @param {Number} index The index to be scrolled to
         * @public
         */
        scrollTo: function (index) {
          
            var self, isCircular, numItems, numVisible, attr, cb, first, offset, number_page;
                
            self = this;
            
            isCircular = self.get("isCircular");
            
            numItems   = self.get("numItems");
            
            numVisible = self.get("numVisible");                

            index = self._getCorrectedIndex(index); /** sanitize the value */
            
            if (isNaN(index)) {
                return;
            }
            
            number_page = self.getPageForItem(index);
            
            offset = self._getOffsetForIndex(index);
            
            cb     = self.get("contentBox");
            
            attr   = self.get("isVertical") ? "top" : "left";
            
            first  = self.getFirstVisible();

            cb.setStyle(attr, offset);

            self.set("selectedItem", index); // assume this is what the user want
            
            self.fire(AFTERPAGESCROLL_EVENT, { pos: number_page });
        },
        
    });
}, "@VERSION@" ,{requires:["gallery-carousel", "substitute"]});