define([
    "ko",
    "nestable"
	],function(ko){
        var initialData = [
        { item_id: "1" ,name: "Women", classes: "vc-women" },
        { item_id: "2", name: "Men", classes: "vc-menu" },
        { item_id: "3", name: "Child", classes: "vc-child" }
        ];

        var nestablOptions = {
            group: 1,
            expandBtnHTML: '<button data-action="expand" type="button"><i class="fa fa-caret-down"></i></button>',
            collapseBtnHTML : '<button data-action="collapse" type="button"><i class="fa fa-caret-up"></i></button>',
            maxDepth: 8
        };

        var updateOutput = function(e){
            var list   = e.length ? e : jQuery(e.target),
            output = list.data('output');
            if (window.JSON) {
                output.val(window.JSON.stringify(list.nestable('serialize')));
            } else {
                output.val('JSON browser support required for this demo.');
            }
        };

        var Item = function(data){
            var self = this;
            var d = new Date();
            var id = '_' + d.getTime() + '_' + d.getMilliseconds();
            self.name = ko.observable(data.name || "new item");
            self.classes = ko.observable(data.classes || "");
            self.item_id = ko.observable(data.item_id || id);
        }

        Item.prototype.toJSON = function() {
        var copy = ko.toJS(this); //easy way to get a clean copy
        delete copy.full; //remove an extra property
        return copy; //return the copy to be serialized
    }

    //can pass fresh data to this function at anytime to apply updates or revert to a prior version
    Item.prototype.update = function(data) { 
        this.name(data.name || "new item");
    };

    var MenuModel = function(items){
        var self = this;
        
        //hold the currently selected item
        self.selectedItem = ko.observable();
        self.items = ko.observableArray(ko.utils.arrayMap(items, function(data){
            return new Item(data);
        }));

        //make edits to a copy
        self.itemForEditing = ko.observable();

        self.addIem = function(){
            var d = new Date();
            var id = '_' + d.getTime() + '_' + d.getMilliseconds();
            var item = new Item([]);
            self.items.push(item);
            self.selectedItem(item);
            self.itemForEditing(item);
            updateOutput(jQuery('#nestable').data('output', jQuery('#nestable-output')));
        };

        self.removeItem = function(item){
            if (confirm('Are you sure you want to do this ?')) {
                self.items.remove(item);
                self.firstActive();
                updateOutput(jQuery('#nestable').data('output', jQuery('#nestable-output')));
            }
        };

        self.acceptItem = function(item){
            var selected = self.selectedItem();
            //clean copy of edited
            var edited = ko.toJS(self.itemForEditing());

            //apply updates from the edited item to the selected items
            selected.update(edited);

            // reset form
            self.selectedItem(null);
            self.itemForEditing(null);
        }

        self.save = function(){
            self.lastSaveJson(JSON.stringify(ko.toJS(self.items), null, 2));
        }

        //hold the currently selected item
        self.selectItem = function(item){
            self.itemForEditing(new Item(ko.toJS(item)));
            self.selectedItem(item);
        }

        self.revertItem = function(){
            self.selectedItem(null);
            self.itemForEditing(null);
        }

        self.firstActive = function(){
            var item = self.items()[0];
            if(item){
                self.selectedItem(item);
                self.itemForEditing(item);
            }else{
                self.selectedItem(false);
                self.itemForEditing(false);
            }
        }
    }
    return MenuModel;
    //ko.applyBindings(new MenuModel(initialData));

});