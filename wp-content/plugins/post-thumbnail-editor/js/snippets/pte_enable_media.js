(function($) {
   // for debug : trace every event
   //var originalTrigger = wp.media.view.MediaFrame.Post.prototype.trigger;
   //wp.media.view.MediaFrame.Post.prototype.trigger = function(){
	//   console.log('Event Triggered:', arguments);
	//   originalTrigger.apply(this, Array.prototype.slice.call(arguments));
   //}

   var pteIframeLink;
   pteIframeLink = _.template(pteL10n.url + "&title=false");

   $( function() {
      var injectTemplate, template;
      // Change the attachment-details html
      injectTemplate = _.template("<a class=\"pte\" href=\"#\">\n   " + pteL10n.PTE + "\n</a>", {});
      template = $("#tmpl-attachment-details").text();
      template = template.replace(/(<div class="compat-meta">)/, "" + injectTemplate + "\n$1");
      $("#tmpl-attachment-details").text(template);
   });

   // Hook into the media.views.attachment to create our link
   var oldDetails = wp.media.view.Attachment.Details;
   wp.media.view.Attachment.Details = oldDetails.extend({
      initialize: function() {
         oldDetails.prototype.initialize.apply( this, arguments );
      },

      // PTE link listener...
      events: _.extend(oldDetails.prototype.events, {
         'click .pte': 'loadPteEditor'
      }),

      // this.controller == wp.media.view.MediaFrame.Post instance
      // This sets/gets a state 'pte', that we can then modify to a state
      // which will load an iframe:
      //
      // See media-views.js:1077:iframeContent()
      //
      loadPteEditor: function() {
         this.controller.state( 'pte' ).set({
            src: pteIframeLink({id:this.model.id})
			, title: pteL10n.PTE + ": " + this.model.attributes.filename
			, content: 'iframe'
         });
         this.controller.setState('pte');
      }
   });

   // Overwrite the MediaFrame.Post class
   //var oldPost = wp.media.view.MediaFrame.Post;
   //wp.media.view.MediaFrame.Post = oldPost.extend({
   //    initialize: function() {
   //        oldPost.prototype.initialize.apply(this, arguments);
   //        this.on( 'content:create:pte', this.pteLoadIframe, this );
   //    },
   //    pteLoadIframe: function( content ) {
   //        console.log( content );
   //        //console.log( this );
   //        this.iframeContent( content );
   //    },
   //    logger: function() {
   //        console.log( arguments );
   //    }
   //});

   //PteController = wp.media.controller.State.extend({
   //    initialize: function() {
   //        this.props = new Backbone.Model({url: "test"});
   //        this.props.on( 'change:data', this.refresh, this );
   //    },
   //    refresh: function() {
   //        console.log( 'REFRESHING' );
   //        this.frame.content.get().refresh();
   //    },
   //    customAction: function() {
   //        console.log( 'CUSTOM ACTION!' );
   //    }
   //});

   //PteIframe = wp.media.view.Iframe.extend({
   //    initialize: function() {
   //        wp.media.view.Iframe.prototype.initialize.apply( this, arguments );
   //    }
   //})

   //var oldMediaFramePost = wp.media.view.MediaFrame.Post;
   //wp.media.view.MediaFrame.Post = oldMediaFramePost.extend({
   //    initialize: function () {
   //        oldMediaFramePost.prototype.initialize.apply( this, arguments );
   //        this.states.add([ new PteController({
   //            id: 'pte',
   //            menu: 'default',
   //            content: 'PAJAMAS',
   //            title: 'PAJAMAS - title',
   //            priority: 200,
   //            type: 'link'
   //        })]);

   //        this.on( 'content:render:PAJAMAS', this.customContent, this );
   //    },
   //    customContent: function(content) {
   //        this.$el.addClass('hide-toolbar');
   //        this.$el.addClass('hide-sidebar');
   //        var view = new PteIframe({
   //            controller: this,
   //            model: this.state().props
   //        });
   //        this.content.set( view );
   //    }
   //});

})(jQuery);
