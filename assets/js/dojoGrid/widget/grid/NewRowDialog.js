define([
    "require",
    "dojo/_base/lang",
    "dojo/_base/declare",
    "dojo/_base/event",
    "dojo/_base/array",
    "dojo/query",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dijit/Dialog",

    //Dext components
    "dext/_base/lang"

],

    function(require, lang, declare, event, array, query, attr, construct, Dialog, dextLang){
        return declare("dojoGrid.widget.grid.NewRowDialog", [Dialog], {
            /**
             * Default dialog title
             */
            title: 'New Row',

            /**
             * The grid that the dialog will be adding a row to
             */
            grid: null,

            buildRendering: function() {
                this.inherited(arguments);

                //get all the editors from the cells and ensure the appropriate dijits are loaded
                var cellEditors = [];
                array.forEach(this.grid.layout.cells, lang.hitch(this, function(item, index, array) {
                    if (!item.editable && !item.editableOnNew)
                        return;

                    cellEditors[cellEditors.length] = this._getEditor(item);
                }));

                //require all the dijits, and then build the rendering
                require(array.map(cellEditors, function(editor){return editor.editorClass}), lang.hitch(this, function() {

                    var containerNode = query(this.containerNode);

                    //Create all the editors
                    array.forEach(cellEditors, function(item, index, array){
                        var label = construct.create('div');
                        attr.set(label, 'innerHTML', item.name);
                        containerNode.adopt(label);

                        var editor = new (eval(item.editorClass.replace(/\//g, '.')))(item.params);
                        containerNode.adopt(editor.domNode);
                    });

                    containerNode.adopt(construct.create('br'));

                    //Create the submit button for the dialog
                    var submitButton = new dijit.form.Button({
                        type:"submit",
                        label: "Add",

                        onClick: lang.hitch(this, function(e){
                            return this.validate();
                        })
                    });

                    var submitButtonDiv = construct.create('div');
                    attr.set(submitButtonDiv, 'align', 'right');
                    query(submitButtonDiv).adopt(submitButton.domNode);
                    containerNode.adopt(submitButtonDiv);
                }));
            },

            execute: function(/*Object*/ formContents){
                console.log('execute');
                console.debug(formContents);

                this.grid.store.newItem(this._processForm(formContents));
            },

            /**
             * Processes the form elements that have been populated in the dialog, doing any modifications
             * that are needed to transform the result into a store-appropriate
             *
             * @param object formElements
             * @return Formatted form object
             */
            _processForm: function(formElements){
                dextLang.forEachProperty(formElements, function(value, property, obj){
                    if(value instanceof Array){
                        obj[property] = (value.length > 0);
                    }
                });

                return formElements;
            },

            /**
             * Gets the editor for the specified cell
             *
             * @param cell The cell to get the editor for
             */
            _getEditor: function(cell){
                var editorClass = 'dijit/form/ValidationTextBox';

                if(dextLang.isset(cell.type)){
                    if(cell.type == dojox.grid.cells.CheckBox){
                        editorClass = 'dijit/form/CheckBox';
                    }
                }

                var params = lang.mixin({
                    name: cell.field,
                    required: true
                }, cell.editorParams);

                //if the defaultValue is set, use it
                if(dextLang.isset(params.defaultValue)) {
                    params['value'] = params.defaultValue;
                }

                //Not compatable with XSS dojo
                return {
                    editorClass: editorClass,
                    name: cell.name,
                    params : params
                };
            }
        });
    }
);
