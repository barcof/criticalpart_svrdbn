<script type="text/javascript">

	Ext.define('detail_part',{
		extend: 'Ext.data.Model',
		fields: ['unid','id','partno','proddate','htempmin','htempmax','humidmin','humidmax','lifetime','btempmin','btempmax','periodmin','periodmax','expdate','nik']
	});

	var store_detail = Ext.create('Ext.data.Store',{

	});
	
	var toolbar_detail = Ext.create('Ext.toolbar.Toolbar',{
		dock: 'bottom',
		ui: 'footer',
		defaults: {
			defaultType: 'button',
			scale: 'large'
		},
		items: [{
			name: 'update',
			icon: 'resources/save.png',
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
			icon: 'resources/delete.png',
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
		},{
			name: 'print',
			icon: 'resources/print.png',
			handler	: function(widget, event) {
				var rec = grid_exp.getSelectionModel().getSelection();
				var len = rec.length;
				
				if(len == "") {
					Ext.Msg.show({
						title		:'Message',
						icon		: Ext.Msg.ERROR,
						msg			: "No data selected.",
						buttons		: Ext.Msg.OK
					});
				} else {
					//	get selected data
					var i = 0; // initial variable for looping
					var a = ''; // empty string 
					var b = ''; // empty string
					var total = 0;
					
					for (var i=0; i < len; i++) {
						cb 	= a + '' + rec[i].data.id;
						a 	= a + '' + rec[i].data.id + '/';
						
						lbl = b + '' + rec[i].data.id;
						b 	= b + '' + rec[i].data.id + ', ';
						
						total++;
					}
					window.open ("response/printExp_sato.php?total="+total+"&cb="+cb+"");
				}
			}
		},'->',{
			name: 'create',
			icon: 'resources/create.png',
			formBind: true,
			handler: function() {
				var getForm = this.up('form').getForm();
				if (getForm.isValid()) {
					getForm.submit({
						url: 'response/inputExp.php',
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
		}, {
			name: 'reset',
			icon: 'resources/reset.png',
			handler: function() {
				this.up('form').getForm().reset();
				// Ext.ComponentQuery.query('textfield[name=nik]')[0].setEditable(true);
				
			}
		}]
	});

	var form_detail = Ext.create('Ext.form.Panel',{
		// title: 'FORM SETTING PARTS',
		// header: { titleAlign: 'center' },
		name: 'form_detail',
		layout: 'anchor',
		// width: 600,
		bodyStyle: {
			background: 'rgba(255, 255, 255, 0)'
		},
		// defaults: {
		//     anchor: '100%',
		//     labelWidth: 150,
		//     padding: '0 0 5 0',
		//     fieldStyle: 'text-align:center;'
		// },
		// defaultType: 'textfield',
		items: [{
			xtype: 'container',
			title: 'Header',
			name: 'cont-top',
			layout: { type: 'hbox', pack: 'center', align: 'stretch' },
			defaults: {
			    anchor: '100%',
			    labelWidth: 150,
			    padding: '0 5 5 5',
			    fieldStyle: 'text-align:center;'
			},
			defaultType: 'textfield',
			items: [{
				emptyText: 'SCAN NIK',
				name: 'detnik',
				value: '37297',
				selectOnFocus: true,
				allowBlank: false,
				listeners: {
					afterrender: function(field) { field.focus(true,500); },
			        specialkey: function(field, e) {
						if (e.getKey() == e.ENTER) {
							var txtval = field.value;
							var len = txtval.length;
							if (len != 0) {
								Ext.ComponentQuery.query('textfield[name=drypartno]')[0].setDisabled(false);
							} else {

							}
							// Ext.ComponentQuery.query('textfield[name=drypartno]')[0].focus(true,1);
						}
			        },
			        change: function(field) {
						var txtval = field.value;
						var len = txtval.length;
						if (len == 0) {
							Ext.ComponentQuery.query('textfield[name=drypartno]')[0].setDisabled(true);
						}
					}
				}
			}, {
				emptyText: 'PART NUMBER',
				name: 'detpart',
				width: 250,
				allowBlank: false

			}, {
				xtype: 'datefield',
				name: 'detproddate',
				editable: false,
				emptyText: 'PRODUCTION DATE',
				format: 'Y-m-d',
			    allowBlank: false
			}]
		}, {
			xtype: 'fieldset',
			cls: 'customFieldSet',
			title: '<span style="font-size:14px;color:#263238;letter-spacing:5px;font-weight:bold">CONDITION AFTER OPEN</span>',
			layout: { type: 'hbox', pack: 'center', align: 'stretch' },
			items: [{
				xtype: 'fieldcontainer',
				cls: 'customLabel',
				fieldLabel: 'TEMPERATURE [ °C ]',
				labelAlign: 'top',
				labelStyle: 'color:#263238;letter-spacing:1px',
				// width: 150,
				layout: { type: 'hbox', pack: 'center', align: 'stretch' },
				defaults: {
				    padding: '0 0 5 0',
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0,
				    width: 85
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'MIN'
				}, {
					xtype: 'label',
					text: '_',
					width: 10
				}, {
					emptyText: 'MAX'
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
				    padding: '0 0 5 0',
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0,
				    width: 85
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'MIN'
				}, {
					xtype: 'label',
					text: '_',
					width: 10
				}, {
					emptyText: 'MAX'
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
				    padding: '0 0 5 0',
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'FLOOR LIFE'
				}]
			}]
		}, {
			xtype: 'fieldset',
			cls: 'customFieldSet',
			title: '<span style="font-size:14px;color:#263238;letter-spacing:5px;font-weight:bold">BAKING CONDITION</span>',
			layout: { type: 'hbox', pack: 'center', align: 'stretch' },
			items: [{
				xtype: 'fieldcontainer',
				cls: 'customLabel',
				fieldLabel: 'TEMPERATURE [ °C ]',
				labelAlign: 'top',
				labelStyle: 'color:#263238;letter-spacing:1px',
				// width: 150,
				layout: { type: 'hbox', pack: 'center', align: 'stretch' },
				defaults: {
				    anchor: '100%',
				    padding: '0 0 5 0',
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0,
				    width: 100
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'MIN'
				}, {
					xtype: 'label',
					text: '_',
					width: 10
				}, {
					emptyText: 'MAX'
				}]
			}, {xtype:'tbspacer',width:20}, 
			{
				xtype: 'fieldcontainer',
				cls: 'customLabel',
				fieldLabel: 'PERIOD [ HOUR ]',
				labelAlign: 'top',
				labelStyle: 'color:#263238;letter-spacing:1px',
				// width: 150,
				layout: { type: 'hbox', pack: 'center', align: 'stretch' },
				defaults: {
				    anchor: '100%',
				    padding: '0 0 5 0',
				    fieldStyle: 'text-align:center;',
				    hideTrigger: true,
			        keyNavEnabled: false,
			        mouseWheelEnabled: false,
				    minValue: 0,
				    // width: 100
				},
				defaultType: 'numberfield',
				items: [{
					emptyText: 'MIN'
				}, {
					xtype: 'label',
					text: '_',
					width: 10
				}, {
					emptyText: 'MAX'
				}]
			}]
		}],
		dockedItems: [toolbar_detail]
	});

	var grid_detail = Ext.create('Ext.grid.Panel', {
		selModel: Ext.create('Ext.selection.CheckboxModel'),
	    viewConfig: {
	    	enableTextSelection  : true
	    },
	    width: 400,
	    columns: [
	    	{ header: 'NO', xtype: 'rownumberer', width: 55, sortable: false },
	    	{ text: 'UNIQUE ID', dataIndex: '', hidden: true },
	    	{ text: 'ID', dataIndex: '', hidden: true },
	    	{ text: 'PART NUMBER', dataIndex: '', flex: 1 },
	    	{ header: 'CONDITION AFTER OPEN', columns: [
	    		{ text: 'LIFETIME', dataIndex: '', width: 400 }
	    	]},
	    	{ header: 'BAKING CONDITION', columns:[
		    	{ text: 'SCAN IN', dataIndex: '', flex: 1 },
		    	{ text: 'SCAN OUT', dataIndex: '', flex: 1 }
	    	]}
	    ]
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