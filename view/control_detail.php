<script type="text/javascript">

	Ext.define('detail_part',{
		extend: 'Ext.data.Model',
		fields: ['unid','id','partno','htempmin','htempmax','humidmin','humidmax','lifetime','btempmin','btempmax','periodmin','periodmax','nik']
	});

	var store_detail = Ext.create('Ext.data.Store',{
		model: 'detail_part',
		autoLoad: true,
		pageSize: 5,
		proxy: {
			type: 'ajax',
			url: 'json/displayDetailPart.php',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'totalcount'
			}
		}
	});
	
	var toolbar_detail = Ext.create('Ext.toolbar.Toolbar',{
		dock: 'right',
		ui: 'footer',
		defaults: {
			defaultType: 'button',
			scale: 'small'
		},
		items: [{
			name: 'update',
			text: 'UPDATE',
			icon: 'resources/save_s.png',
			handler: function() {
				var getForm = this.up('form').getForm();
				if (getForm.isValid()) {
					getForm.submit({
						url: 'response/updateExp.php',
						waitMsg : 'Now transfering data, please wait..',
						success : function(form, action) {
	                        Ext.Msg.show({
	                        	title   : 'SUCCESS',
	                        	msg     : action.result.msg,
	                        	buttons : Ext.Msg.OK
	                        });
	                        exp_control.loadPage(1);
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
		},{
			name: 'delete',
			text: 'DELETE',
			icon: 'resources/delete_s.png',
			handler: function() {
				var rec = grid_exp.getSelectionModel().getSelection();
				var len = rec.length;
				if (rec == 0) {
					Ext.Msg.show({
						title: 'Failure - Select Data',
						icon: Ext.Msg.ERROR,
						msg: 'Select any field you desire to delete',
						buttons: Ext.Msg.OK
					});
				} else {
					Ext.Msg.confirm('Confirm', 'Are you sure want to delete data ?', function(btn) {
						if(btn == 'yes') {
							for (var i=0;i<len;i++) {
								Ext.Ajax.request({
									url: 'response/deleteExp.php',
									method: 'POST',
									params: 'unid='+rec[i].data.unid,
									success: function(obj) {
										var resp = obj.responseText;
										if(resp !=0) {
											exp_control.loadPage(1);
										} else {
											Ext.Msg.show({
												title: 'Delete Data',
												icon: Ext.Msg.ERROR,
												msg: resp,
												buttons: Ext.Msg.OK
											});
										}
									}
								});
							}
						}
					});
				}
			}
		},
		'->',
		{
			name: 'create',
			text: 'INPUT',
			icon: 'resources/create.png',
			formBind: true,
			handler: function() {
				var getForm = this.up('form').getForm();
				if (getForm.isValid()) {
					getForm.submit({
						url: 'response/inputDetail.php',
						waitMsg : 'Now transfering data, please wait..',
						success : function(form, action) {
	                        Ext.Msg.show({
	                        	title   : 'SUCCESS',
	                        	msg     : action.result.msg,
	                        	buttons : Ext.Msg.OK
	                        });
	                        store_detail.loadPage(1);
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
		}, {
			name: 'reset',
			text: 'RESET',
			icon: 'resources/reset_s.png',
			handler: function() {
				this.up('form').getForm().reset();
				// Ext.ComponentQuery.query('textfield[name=nik]')[0].setEditable(true);
				
			}
		}]
	});

	var form_detail = Ext.create('Ext.form.Panel',{
		name: 'form_detail',
		layout: 'anchor',
		bodyStyle: {
			background: 'rgba(255, 255, 255, 0)'
		},
		// width: '50%',
		items: [{
			xtype: 'container',
			name: 'cont-top',
			layout: { type: 'hbox', pack: 'center', align: 'stretch' },
			defaults: {
			    width: 275,
			    padding: '0 5 5 5',
			    fieldStyle: 'text-align:center;'
			},
			defaultType: 'textfield',
			items: [{
				xtype: 'hiddenfield',
				name: 'detunid'
			}, {
				emptyText: 'SCAN NIK',
				name: 'detnik',  // NIK
				// value: '37297',
				selectOnFocus: true,
				allowBlank: false,
				listeners: {
					afterrender: function(field) { field.focus(true,500); },
			        specialkey: function(field, e) {
						if (e.getKey() == e.ENTER) {
							var txtval = field.value;
							var len = txtval.length;
							if (len != 0) {
								Ext.ComponentQuery.query('textfield[name=detpart]')[0].setDisabled(false);
							} else {

							}
							Ext.ComponentQuery.query('textfield[name=detpart]')[0].focus(true,1);
						}
			        },
			        change: function(field) {
						var txtval = field.value;
						var len = txtval.length;
						if (len == 0) {
							Ext.ComponentQuery.query('textfield[name=detpart]')[0].setDisabled(true);
						}
					}
				}
			}, {
				emptyText: 'PART NUMBER',
				name: 'detpart', // PART NUMBER
				allowBlank: false,
				disabled: true,
				listeners: {
		        	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
		        }

			}
			]
		}, {
			xtype: 'fieldset',
			cls: 'customFieldSet',
			title: '<span style="color:#263238;letter-spacing:5px;font-weight:bold">CONDITION AFTER OPEN</span>',
			layout: { type: 'hbox', pack: 'center', align: 'stretch' },
			height: 75,
			items: [{
				xtype: 'fieldcontainer',
				cls: 'customLabel',
				fieldLabel: 'TEMPERATURE [ °C ]',
				labelAlign: 'top',
				labelStyle: 'color:#263238;letter-spacing:1px',
				layout: { type: 'hbox', pack: 'center', align: 'stretch' },
				defaults: {
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0,
				    width: 85
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'MIN',
					name: 'htempmin', // HUMIDITY TEMPERATURE MIN VALUE
					allowBlank: false
				}, {
					xtype: 'label',
					text: '_',
					width: 10
				}, {
					emptyText: 'MAX',
					name: 'htempmax', // HUMIDITY TEMPERATURE MAX VALUE
					allowBlank: false
				}]
			}, {xtype:'tbspacer',width:20},
			{
				xtype: 'fieldcontainer',
				cls: 'customLabel',
				fieldLabel: 'HUMIDITY [ % ]',
				labelAlign: 'top',
				labelStyle: 'color:#263238;letter-spacing:1px',
				layout: { type: 'hbox', pack: 'center', align: 'stretch' },
				defaults: {
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0,
				    width: 85
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'MIN',
					name: 'humidmin', // HUMIDITY MIN VALUE
					allowBlank: false
				}, {
					xtype: 'label',
					text: '_',
					width: 10
				}, {
					emptyText: 'MAX',
					name: 'humidmax', // HUMIDITY MAX VALUE
					allowBlank: false
				}]
			}, {xtype:'tbspacer',width:20},
			{
				xtype: 'fieldcontainer',
				cls: 'customLabel',
				fieldLabel: 'TIME LIMIT [ HOUR ]',
				labelAlign: 'top',
				labelStyle: 'color:#263238;letter-spacing:1px',
				layout: { type: 'hbox', pack: 'center', align: 'stretch' },
				defaults: {
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'FLOOR LIFE',
					name: 'lifetime', // FLOOR LIFE
					allowBlank: false
				}]
			}]
		}, {
			xtype: 'fieldset',
			cls: 'customFieldSet',
			title: '<span style="color:#263238;letter-spacing:5px;font-weight:bold">BAKING CONDITION</span>',
			layout: { type: 'hbox', pack: 'center', align: 'stretch' },
			height: 75,
			items: [{
				xtype: 'fieldcontainer',
				cls: 'customLabel',
				fieldLabel: 'TEMPERATURE [ °C ]',
				labelAlign: 'top',
				labelStyle: 'color:#263238;letter-spacing:1px',
				layout: { type: 'hbox', pack: 'center', align: 'stretch' },
				defaults: {
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0,
				    width: 100
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'MIN',
					name: 'btempmin', // BAKING TEMPERATURE MIN
					allowBlank: false
				}, {
					xtype: 'label',
					text: '_',
					width: 10
				}, {
					emptyText: 'MAX',
					name: 'btempmax', // BAKING TEMPERATURE MAX
					allowBlank: false
				}]
			}, {xtype:'tbspacer',width:20}, 
			{
				xtype: 'fieldcontainer',
				cls: 'customLabel',
				fieldLabel: 'PERIOD [ HOUR ]',
				labelAlign: 'top',
				labelStyle: 'color:#263238;letter-spacing:1px',
				layout: { type: 'hbox', pack: 'center', align: 'stretch' },
				defaults: {
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'MIN',
					name: 'periodmin', // BAKING PERIOD MIN
					allowBlank: false
				}, {
					xtype: 'label',
					text: '_',
					width: 10
				}, {
					emptyText: 'MAX',
					name: 'periodmax', // BAKING PERIOD MAX
					allowBlank: false
				}]
			}]
		}],
		dockedItems: [toolbar_detail]
	});

	var grid_detail = Ext.create('Ext.grid.Panel', {
		store: store_detail,
		selModel: Ext.create('Ext.selection.CheckboxModel'),
	    viewConfig: {
	    	enableTextSelection  : true
	    },
	    width: 400,
	    columns: [
	    	{ header: 'NO', xtype: 'rownumberer', width: 55, sortable: false },
	    	{ text: 'UNIQUE ID', dataIndex: 'unid', hidden: true },
	    	{ text: 'ID', dataIndex: 'id', flex: 1 },
	    	{ text: 'PART NUMBER', dataIndex: 'partno', flex: 1 },
	    	// { text: 'PROD. DATE', dataIndex: 'proddate', flex: 1 },
	    	{ header: 'CONDITION AFTER OPEN', columns: [
	    		{ text: 'TEMPERATURE [°C]', columns:[
		    		{ text: 'MIN', dataIndex: 'htempmin', width: 70 },
		    		{ text: 'MAX', dataIndex: 'htempmax', width: 70 }
	    		] },
	    		{ text: 'HUMIDITY [%]', columns: [
	    			{ text: 'MIN', dataIndex: 'humidmin', width: 70 },
		    		{ text: 'MAX', dataIndex: 'humidmax', width: 70 }
	    		] },
	    		{ text: 'TIME LIMIT', dataIndex: 'lifetime', flex: 1 }
	    	] },
	    	{ header: 'BAKING CONDITION', columns:[
		    	{ text: 'TEMPERATURE [°C]', columns:[
		    		{ text: 'MIN', dataIndex: 'btempmin', width: 70 },
		    		{ text: 'MAX', dataIndex: 'btempmax', width: 70 }
	    		] },
	    		{ text: 'BAKING PERIOD', columns: [
	    			{ text: 'MIN', dataIndex: 'periodmin', width: 70 },
		    		{ text: 'MAX', dataIndex: 'periodmax', width: 70 }
	    		] }
	    	] },
	    	// { text: 'EXPIRED DATE', dataIndex: 'expdate', flex: 1 },
	    	{ text: 'NIK', dataIndex: 'nik', flex: 1 }
	    ],
	    bbar: {
	    	xtype: 'pagingtoolbar',
	    	displayInfo	: true,
	    	store: store_detail,
	    	items: ['->',{
	    		xtype: 'textfield',
	    		name: 'fldsrc',
	    		width: 600,
	    		emptyText: 'Search part number in here...',
	    		fieldStyle: 'text-align:center;',
	    		listeners: {
	    			specialkey: function(field, e) {
						if (e.getKey() == e.ENTER) {
							exp_control.proxy.setExtraParam('fldsrc',field.getValue());
							exp_control.loadPage(1);
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

	var panel_detail = Ext.create('Ext.panel.Panel',{
		border: true,
		layout: 'border',
	    defaults: {
	    	split: false,
	    	plain: true
	    },
	    items: [{
	        region: 'north',
	        layout: {
	        	type: 'hbox',
	        	pack: 'center'
	        },
	        bodyPadding: 10,
	        bodyStyle: {
	        	// background: '#ADD2ED',
	        	background: 'url("resources/bg-image-2.jpg") no-repeat center',
	        	backgroundSize: 'cover'
	        },
	        items: form_detail
	    }, {
	        region: 'center',
	        layout: 'fit',
	        items: grid_detail
	    }]
	});

</script>