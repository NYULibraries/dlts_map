// http://yuilibrary.com/yui/docs/event/synth-example.html
// http://www.cambiaresearch.com/articles/15/javascript-char-codes-key-codes
Y.use('node', 'event-synthetic', 'event-custom', function (Y) {
  
  Y.Event.define("arrow", {
    // Webkit and IE repeat keydown when you hold down arrow keys.
    // Opera links keypress to page scroll; others keydown.
    // Firefox prevents page scroll via preventDefault() on either
    // keydown or keypress.
    _event: (Y.UA.webkit || Y.UA.ie) ? 'keydown' : 'keypress',

    _keys: {
        37: true,
        38: true,
        39: true,
        40: true
    },

    _directions: {
        '37': 'w',
        '38': 'n',
        '39': 'e',
        '40': 's'
    },

    _keydown: function (e) {
        if (this._keys[e.keyCode]) {
            var node = e.currentTarget,
                input = node.getData('-yui3-arrow-dir');

            if (!input) {
                input = [];
                node.setData('-yui3-arrow-dir', input);
            }

            // Avoid repeats for browsers that fire multiple
            // keydowns when holding down an arrow key
            if (input[input.length - 1] !== e.keyCode) {
                input.push(e.keyCode);
            }
        }
    },

    _notify: function (e, notifier) {
        if (this._keys[e.keyCode]) {
            var node = e.currentTarget,
                input  = node.getData('-yui3-arrow-dir'),
                directions = this._directions,
                direction = [e.keyCode],
                i;

            for (i = input.length - 1; i >= 0; --i) {
                if ((e.keyCode - input[i]) % 2) {
                    direction.push(input[i]);
                    break;
                }
            }
            e.direction = directions[direction];
            notifier.fire(e);
        }
    },

    _keyup: function (e) {
        if (this._keys[e.keyCode]) {
            var node = e.currentTarget,
                input = node.getData('-yui3-arrow-dir'),
                i;

            if (input) {
                for (i = input.length - 1; i >= 0; --i) {
                    if (input[i] === e.keyCode) {
                        input.splice(i, 1);
                        break;
                    }
                }
            }
        }
    },

    on: function (node, sub, notifier, filter) {
        var method = (filter) ? 'delegate' : 'on';
        sub._handle = new Y.EventHandle([
            node[method]('keydown', Y.rbind(this._keydown, this), filter),
            node[method](this._event, Y.rbind(this._notify, this, notifier), filter),            
            node[method]('keyup', Y.rbind(this._keyup, this), filter)
        ]);
    },

    detach: function (node, sub, notifier) {
        sub._handle.detach();
    },

    delegate: function () {
        this.on.apply(this, arguments);
    },

    detachDelegate: function () {
        this.detach.apply(this, arguments);
    }

  });
    
  function move(e) {
    e.preventDefault();
    var map = OpenLayers.DLTS.pages[0], size, handled = true, slideFactor = 75;
    if (!e.shiftKey) {
      switch (e.direction) {
        case 'n':
          map.pan(0, -slideFactor);
          break;
          
        case 's':
          map.pan(0, slideFactor);
          break;
          
        case 'e':
          map.pan(slideFactor, 0);
          break;
          
        case 'w':
          map.pan(-slideFactor, 0);
          break;
          
      }
    }
    // Shift opptions
    else {
      
      switch (e.direction) {
        case 'e':
          Y.fire('openlayers:next', e);
          
          break;
          
        case 'w':
          Y.fire('openlayers:previous', e);
          break;          
        }
    }
  }
    
  Y.on('arrow', move);
    
});