define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/event",
    "dojo/aspect",
    "dojo/_base/unload",
    "dojo/dom-style",
    "dojox/grid/EnhancedGrid",
    "dijit/_WidgetsInTemplateMixin",
    "dojo/text!dojoGrid/widget/templates/grid.html",

    //Template & Functionality requires
    "./grid/NewRowDialog",

    //Grid Mixins
    "dojox/grid/cells/dijit"
],

    function(declare, lang, event, aspect, unload, style, EnhancedGrid, _WidgetsInTemplateMixin, gridTemplate, NewRowDialog) {
        return declare("dojoGrid.widget.Grid", [EnhancedGrid, _WidgetsInTemplateMixin], {
            templateString: gridTemplate,
            widgetsInTemplate: true,
            editable: false,

            //TODO: on google's cdn, if we dont overwrite these messages, there is an error loaind the nls
            loadingMessage: "<span class='dojoxGridLoading'>Loading...</span>",
            errorMessage: "<span class='dojoxGridError'>Sorry, an error occurred</span>",

            postCreate: function() {
                this.inherited(arguments);

                //we should be clean to begin with, and even if we are not, we will check below
                this._onStoreClean();

                //wire up the events
                this.own(aspect.after(this.store, 'onNew', lang.hitch(this, '_onStoreDirty')));
                this.own(aspect.after(this.store, 'onDelete', lang.hitch(this, '_onStoreDirty')));
                this.own(aspect.after(this.store, 'onSet', lang.hitch(this, '_onStoreDirty')));

                unload.addOnWindowUnload(this, '_windowOnUnload');

                if(this.store.isDirty()){
                    this._onStoreDirty();
                }
            },

            /**
             *
             * @param value Flag to indicate if the grid is editable.
             */
            _setEditableAttr: function(value) {
                if(value === false) {
                    style.set(this.editControlContainer, 'display', 'none');
                } else {
                    style.set(this.editControlContainer, 'display', 'block');
                }

                this.editable = value;
            },

            /**
             * Displays the new row dialog
             */
            _addRowBtnClick: function(e){
                var dialog = new NewRowDialog({
                    grid: this
                });

                dialog.show();
            },

            _deleteRowBtnClick: function(e){
                this.removeSelectedRows();
            },

            _applyBtnClick: function(e){
                this.store.save();
                this._onStoreClean();
            },

            _cancelBtnClick: function(e){
                this.store.revert();
                this._onStoreClean();
            },

            /**
             * Called when the store is marked as dirty
             *
             * @param e
             */
            _onStoreDirty: function(){
                this.applyButton.set('disabled', false);
                this.cancelButton.set('disabled', false);
            },

            /**
             * Called when the store is clean again.
             * Eg: after calling _applyBtnClick, _cancelBtnClick
             */
            _onStoreClean: function(){
                this.applyButton.set('disabled', true);
                this.cancelButton.set('disabled', true);
            },

            /**
             * Called when the window is unloading.
             * Checks for dirty data in the grid, and alerts the user to save / revert their changes before navigating away.
             *
             */
            _windowOnUnload: function(e){
                if(this.store.isDirty()){
                    var message = 'The grid has unsaved changes that are about to be lost.';
                    e.returnValue = message;
                    event.stop(e);
                    return message;
                }
            }
    });
});
