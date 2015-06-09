YUI().use(
    'node'
  , 'event'
  , 'event-custom'
  , 'crossframe'
  , function(Y) {

    'use strict';

    Y.on('button:button-metadata:on', function(e) {
        Y.CrossFrame.postMessage("parent", JSON.stringify({
            fire: 'button:button-metadata:on',
            data: {}
          })
        );
    });

    Y.on('button:button-metadata:off', function(e) {
        Y.CrossFrame.postMessage("parent", JSON.stringify({
            fire: 'button:button-metadata:off',
            data: {}
          })
        );
    });

    Y.on('button:button-fullscreen:on', function(e) {
        Y.CrossFrame.postMessage("parent", JSON.stringify({
            fire: 'button:button-fullscreen:on',
            data: {}
          })
        );
    });

    Y.on('button:button-fullscreen:off', function(e) {
        Y.CrossFrame.postMessage("parent", JSON.stringify({
            fire: 'button:button-fullscreen:off',
            data: {}
          })
        );
    });

    Y.on('openlayers:change', function(e) {
        Y.CrossFrame.postMessage("parent", JSON.stringify({
            fire: 'openlayers:change',
            data: { 
              sequence: e.sequence,
              title: e.title
            }
          })
        );
    });

    Y.Global.on('crossframe:css', function (e, data, callback) { 
        Y.Get.css(data.message, function (err) {
            if (err) {
                Y.log('Error loading CSS: ' + err[0].error, 'error');
                return;
            }
            callback( {"info": "some information from receiver. (" + parseInt(new Date().getTime()) + ")"} );
        });      
    });

});