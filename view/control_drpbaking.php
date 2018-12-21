<script type="text/javascript">

	function whitespace(val) {
		return '<div style="white-space: pre;">'+val+'</div>';
	}

	// function upsize(val) {
	// 	return '<font size="2" style="font-family:sans-serif; white-space:normal; line-height:1.5;">' + val + '</font>';
	// }

	Ext.define('bakingdry',{
		extend: 'Ext.data.Model',
		fields: ['unid','opid','partno','scanin','scanout','estmin','estmax','nik']
	});

	var store_bakingdry = Ext.create('Ext.data.Store',{
		model: 'bakingdry',
		autoLoad: true,
		pageSize: 25,
		proxy: {
			type: 'ajax',
			url: 'json/displayDryBaking.php',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'totalcount'
			}
		},
		listeners: {
			load: function(store) {
				store.proxy.setExtraParam('drpbk_fldsrc','');
			}
		}
	});

	var toolbar_bakingdry = Ext.create('Ext.toolbar.Toolbar',{
		dock:'bottom',
		ui: 'footer',
		defaults: {
			defaultType: 'button',
			scale: 'medium'
		},
		items: ['->',{
			name: 'reset',
			icon: 'resources/reset.png',
			handler: function() { this.up('form').getForm().reset(); }
		}]
	});

	var form_bakingdry = Ext.create('Ext.form.Panel',{
		name: 'form_bakingdry',
		layout: 'anchor',
		width: 500,
		bodyStyle: {
        	background: 'rgba(255, 255, 255, 0)'
        },
	    defaults: {
		    anchor: '100%',
		    labelWidth: 150,
		    padding: '5 0 0 0',
		    fieldStyle: 'font-size:20px;text-align:center;'
		},
	    defaultType: 'textfield',
	    items: [{
			emptyText: 'SCAN NIK',
			name: 'drpbk_nik',
			// value: '37297',
			selectOnFocus: true,
			listeners: {
				afterrender: function(field) { field.focus(true,500); },
		        specialkey: function(field, e) {
					if (e.getKey() == e.ENTER) {
						var txtval = field.getValue();
						var len = txtval.length;
						if (len != 0) {
							Ext.ComponentQuery.query('textfield[name=drpbk_partno]')[0].setDisabled(false);
							Ext.ComponentQuery.query('textfield[name=drpbk_partno]')[0].focus(true,1);
						} else { }
					}
		        },
		        change: function(field) {
					var txtval = field.getValue();
					var len = txtval.length;
					if (len == 0) {
						Ext.ComponentQuery.query('textfield[name=drpbk_partno]')[0].setDisabled(true);
					}
				}
			}
		}, {
			emptyText: 'SCAN PART NUMBER',
			name: 'drpbk_partno',
			disabled: true,
			listeners: {
				specialkey: function(field, e) {
					if (e.getKey() == e.ENTER) {
						var form = this.up('form').getForm();
						if(form.isValid()) {
							form.submit({
								url: 'response/inputBakingDry.php',
								waitMsg : 'Now transfering data, please wait..',
								success : function(form, action) {
			                        Ext.Msg.show({
			                        	title   : 'SUCCESS - INOFRMATION',
			                        	msg     : action.result.msg,
			                        	buttons : Ext.Msg.OK
			                        });
			                        store_bakingdry.loadPage(1);
			                        field.reset();
			                    },
			                    failure : function(form, action) {
			                        Ext.Msg.show({
				                        title   : 'OOPS, AN ERROR JUST HAPPEN !',
				                        icons   : Ext.Msg.ERROR,
				                        msg     : action.result.msg,
				                        buttons : Ext.Msg.OK
			                        });
			                    }
							});
						}
						
					}
		        },
		        change:function(field) {
	                field.setValue(field.getValue().toUpperCase());
	            }
			}
		}],
		dockedItems: toolbar_bakingdry
	});

	var grid_bakingdry = Ext.create('Ext.grid.Panel', {
		store: store_bakingdry,
		selModel: Ext.create('Ext.selection.CheckboxModel'),
	    viewConfig: {
	    	enableTextSelection  : true
	    },
	    width: 400,
	    columns: [
	    	{ header: 'NO', xtype: 'rownumberer', width: 55, sortable: false },
	    	{ text: 'UNIQUE ID', dataIndex: 'unid', flex: true, hidden: true },
	    	{ text: 'ID', dataIndex: 'opid', flex: true, hidden: true },
	    	{ text: 'PART NUMBER', dataIndex: 'partno', flex: 1, renderer: whitespace },
	    	{ text: 'SCAN IN', dataIndex: 'scanin', flex: 1 },
	    	{ text: 'SCAN OUT', dataIndex: 'scanout', flex: 1 },
	    	{ text: 'ESTIMATE TIME MIN', dataIndex: 'estmin', flex: 1 },
	    	{ text: 'ESTIMATE TIME MAX', dataIndex: 'estmax', flex: 1 },
	    	{ text: 'NIK', dataIndex: 'nik', flex: 1 }
	    ],
	    bbar: {
	    	xtype: 'pagingtoolbar',
	    	displayInfo	: true,
	    	store: store_bakingdry,
	    	items: ['->',{
	    		xtype: 'textfield',
	    		name: 'drpbk_fldsrc',
	    		width: 600,
	    		emptyText: 'Search part number in here...',
	    		fieldStyle: 'text-align:center;',
	    		listeners: {
	    			specialkey: function(field, e) {
						if (e.getKey() == e.ENTER) {
							store_bakingdry.proxy.setExtraParam('drpbk_fldsrc',field.getValue());
							store_bakingdry.loadPage(1);
							// console.log(field.value);
						}
	                },
	                change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
	    		}
	    	}]
	    }
	});

	var panel_bakingdry = Ext.create('Ext.panel.Panel',{
		border: true,
		layout: 'border',
		plain: true,
		items: [{
			region: 'north',
			layout: {
				type: 'hbox',
				pack: 'center'
			},
			bodyPadding: 10,
			bodyStyle: {
				background: 'url("resources/bg-image-2.jpg") no-repeat center',
	        	backgroundSize: 'cover'
			},
			items: form_bakingdry
		}, {
	        region: 'center', // GRID SIDE
	        layout: 'fit',
	        items: grid_bakingdry
	    }]
	});
	
</script>