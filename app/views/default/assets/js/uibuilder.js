/*
	UIBuilderData
*/
function UIBuilderData (name,type,attributes) {
    this._name = name;
    this._type = type;
	//extend data
    this._attributes = attributes;
	
	window.jQuery.extend(this,attributes);
	var $this = this;
	
	this.fetch = function(){
		if(this._type){
			var namespaces = this._type.split('.');
			var func = UIBuilder.view;
			for (var i = 0; i < namespaces.length; i++) { 
				if(typeof func[namespaces[i]] != 'undefined'){
					func = func[namespaces[i]];
				} else {
					return '';	//not exists template
				}
			}
			return func.apply(this, [this]);
		}
	},
	this.getSize = function(){
		if(typeof $this.size !== 'undefined'){
			return 'col-md-' + $this.size;
		} else {
			return '';
		}
	};

	this.getValidationLabel = function(){
		if(typeof $this.size !== 'undefined'){
			return $this.size;
		} else {
			return '';
		}
	};
	
	this.getHelpText = function(){
		if(typeof $this.size !== 'undefined'){
			return $this.size;
		} else {
			return '';
		}
	};
	
}

if (typeof(UIBuilder) == 'undefined') {
	// create our halo namespace
	UIBuilder = {
		extend: function (obj) {
			window.jQuery.extend(this, obj);
		}
	}
}

UIBuilder.extend({
	getInstance: function(name,type,attributes){
		var instance = new UIBuilderData(name,type,attributes);
		return instance;
	},
	view: {
		form: {
			date: function($builder){
				var html = 
				'<div class="form-group '+ $builder.getSize() + ($builder.error?'error':'') + '">' +
				'<label for="' + $builder.name + '" class="' + $builder.getValidationLabel() +'">'+ $builder.title + $builder.getHelpText() + '</label>' +
				'<div class="input-group halo_field_date date form_date" data-date="" data-date-format="dd-mm-yyyy">' +
				'<input class="form-control" name="' + $builder.name + '" type="text" value="'+ $builder.value + '" readonly' +
				'>' +
				'<span class="input-group-addon"><span class="fa fa-times"></span></span>' + 
				'<span class="input-group-addon"><span class="fa fa-calendar"></span></span>' +
				'</div>' +
				'</div>';
				
				return html;
			}
		},
		filter: {
			date: function($builder) {
			
			}
		}
	}
});