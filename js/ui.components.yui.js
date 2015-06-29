YUI().use(
  'node', 'event', 'event-custom', 'gallery-soon',
  function(Y) {

    'use strict';

    var pane_pagemeta = Y.one('.pagemeta');

    var pane_top = Y.one('.top');

    /** callbacks */
    var resizePageMeta;

    var on_button_click;

    /** add view port information to global setting */
    var viewport = Y.DOM.viewportRegion();

    function resizePageMeta() {

      /** definition list start */
      var viewportHeight = this.get('winHeight'),
        viewportWidth = this.get('winWidth'),
        adminBarHeight = 0,
        topHeight = Y.one('.top').get('offsetHeight'),
        navbarHeight = Y.one('#navbar').get('offsetHeight'),
        pageHeight = 0,
        nodeAdminMenu = Y.one('#admin-menu'),
        sidebarHeight

      ; /** definition list end */

      if (nodeAdminMenu) {
        adminBarHeight = Y.one('#toolbar').get('offsetHeight') + nodeAdminMenu.get('offsetHeight') + Y.one('.tabs').get('offsetHeight') + 10;
      }

      sidebarHeight = viewportHeight - (adminBarHeight + topHeight + navbarHeight + pageHeight);

      Y.one('#pagemeta').setStyles({
        'height': sidebarHeight
      });
      // responsive behavior
      if (viewportWidth > 800) {

        Y.fire('button:button-metadata:on', Y.one('#pagemeta'));
        Y.one('#button-metadata').addClass('on');
      } else {

        Y.fire('button:button-metadata:off', Y.one('#pagemeta'));
        Y.one('#button-metadata').removeClass('on');
      }


    }

    function on_button_click(e) {

      e.preventDefault();

      var self = this,
        current_target = e.currentTarget,
        event_prefix, event_id, node_target, data_target;

      /** don't waste time if the button is inactive */
      if (current_target.hasClass('inactive')) return;

      /** if current target has target, get target from data-target */
      if (current_target.hasClass('target')) {

        data_target = self.getAttribute('data-target');

        event_prefix = 'button:' + data_target;

        /** look-up for the main target */
        node_target = Y.all('#' + data_target);

      }

      /** current target is the main target */
      else {

        event_id = self.get('id');

        event_prefix = 'button:' + event_id;

        /** find possible reference targets to this target */
        node_target = Y.all('a[data-target=' + event_id + ']');
      }

      if (self.hasClass('on')) {
        self.removeClass('on');
        if (Y.Lang.isObject(node_target)) {
          node_target.each(function(node) {
            node.removeClass('on');
          });
        }
        Y.fire(event_prefix + ':off', e);
      } else {
        self.addClass('on');
        if (Y.Lang.isObject(node_target)) {
          node_target.each(function(node) {
            node.addClass('on');
          });
        }
        Y.fire(event_prefix + ':on', e);
      }

      Y.fire(event_prefix + ':toggle', e);
    }

    /** events listeners */
    Y.on('contentready', resizePageMeta, '#pagemeta');



    Y.on('windowresize', resizePageMeta, '#pagemeta');

    Y.one('.page').delegate('click', on_button_click, 'a.button');

    Y.on('button:button-metadata:on', function(e) {
      this.removeClass('hidden');
      this.ancestor('.pane-body').removeClass('pagemeta-hidden');
    }, pane_pagemeta);

    Y.on('button:button-metadata:off', function(e) {
      Y.log("button it off " + this);
      this.addClass('hidden');
      this.ancestor('.pane-body').addClass('pagemeta-hidden');
    }, pane_pagemeta);

    Y.on('button:button-fullscreen:on', function(e) {
      Y.fire('button:' + this.button.get('id') + ':off', this.pagemeta);
      this.button.removeClass('on');
      this.top.addClass('hidden');
    }, {
      top: pane_top,
      pagemeta: pane_pagemeta,
      button: Y.one('a.metadata')
    });

    Y.on('button:button-fullscreen:off', function(e) {
      this.button.blur();
      this.top.removeClass('hidden');
    }, {
      top: pane_top,
      button: Y.one('a.fullscreen')
    });

    function openLayersTilesLoading() {

      if (Y.one('body').hasClass('openlayers-loading')) Y.later(500, Y.one('.pane.load'), openLayersTilesLoading);

      else Y.one('.pane.load').hide();

    }

    openLayersTilesLoading();

  });