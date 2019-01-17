<script type="text/javascript">

	function whitespace(val) {
		return '<div style="white-space: pre;">'+val+'</div>';
	}

	function expstatus(val) {
		// var len = val.substr(0,10);
	  	// Harus di maintain karna status EXPIRED di hardcode
	  	var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		var H = today.getHours();
		var i = today.getMinutes();
		var s = today.getSeconds();

		// console.log(today);

		if(dd<10) { dd = '0'+dd };

		if(mm<10) { mm = '0'+mm };

		if(H<10) { H = '0'+H };

		if(i<10) { i = '0'+i };

		if(s<10) { s = '0'+s };

		today = yyyy + '-' + mm + '-' + dd +' '+ H + ':' + i + ':' + s;

		var lastmonth = (val.substr(5,2)-1)

		var valyear = (val.substr(0,4));

		if ((lastmonth < mm) && (yyyy == valyear )) {
			if (today >= val) {
				return ("<span style=color:red;>"+val+"<br>( EXPIRED PART )</span>");
			} else { 
				// return (val); 
				return ("<span style=color:#ff6f00;><b>"+val+"</b><br>( WILL BE EXPIRED SOON )</span>");
			}
		} else { return (val); }

		// if (val == null || val == '') {
		// 	return val;
		// } else {
		// 	if (today > val) {
		// 		return ("<span style=color:red;>"+val+"<br>( EXPIRED PART )</span>");
		// 	} else { return val; }
		// }
	}

	function partstatus(val) {
		if(val == 'E10') {
			return ("<span style=color:red>EMPTY REEL</span>");
		} else {
			return (val);
		}
	}


	Ext.define('open_part',{
		extend: 'Ext.data.Model',
		fields: ['unid','openid','partno','qty','proddate','selflife','opendate','floorlife','nik','place','accdate','datacode']
	});

	var open_part = Ext.create('Ext.data.Store',{
		model: 'open_part',
		autoLoad: true,
		pageSize: 25,
		proxy: {
			type: 'ajax',
			url: 'json/displayOpenPart.php',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'totalcount'
			}
		},
		listeners: {
			load: function(store) {
				store.proxy.setExtraParam('stpartfldsrc','');
			}
		}
	});
	
	var toolbar_openpart = Ext.create('Ext.toolbar.Toolbar',{
		dock:'bottom',
		ui: 'footer',
		defaults: {
			defaultType: 'button',
			scale: 'medium'
		},
		items: [
		{
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
		},
		'->',
		{
			text: 'SINGLE LABEL',
			name: 'borrow',
			// icon: 'resources/splice.png',
			handler: function() {
				Ext.create('Ext.window.Window', {
				    title: 'FORM BORROW PART',
				    // height: 200,
				    border: false,
				    padding: '5 5 5 5',
				    width: 400,
				    layout: 'fit',
				    animateTarget: this,
				    items: form_borrow,
				    closeAction: 'hide',
				    listeners: {
				    	activate: function() {
				    		Ext.ComponentQuery.query('button[name=borrow]')[0].setDisabled(true);
				    	},
				    	close: function() {
				    		Ext.ComponentQuery.query('button[name=borrow]')[0].setDisabled(false);
				    	}
				    }
				}).show();
			}
		}, '->' ,{
			name: 'create',
			icon: 'resources/create.png',
			formBind: true,
			handler: function() {
				var getForm = this.up('form').getForm();
				if (getForm.isValid()) {
					getForm.submit({
						url: 'response/inputOpenPart.php',
						waitMsg : 'Now transfering data, please wait..',
						success : function(form, action) {
	                        Ext.Msg.show({
	                        	title   : 'SUCCESS',
	                        	msg     : action.result.msg,
	                        	buttons : Ext.Msg.OK
	                        });
	                        open_part.loadPage(1);
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
	
	var form_borrow = Ext.create('Ext.form.Panel',{
		name: 'form_borrow',
		layout: 'anchor',
		defaults: {
		    anchor: '100%',
		    padding: '5 5 5 5',
		    fieldStyle: 'font-size:20px;text-align:center;'
		},
		defaultType: 'textfield',
		items: [{
			emptyText: 'SCAN NIK',
			name: 'br_nik',
			// value: '37297',
			allowBlank: false,
			selectOnFocus: true,
			listeners: {
				afterrender: function(field) { field.focus(true,500); },
		        specialkey: function(field, e) {
		        	if (e.getKey() == e.ENTER) {
		        		var txtval = field.getValue();
						var len = txtval.length;
						if (len != 0) {
							Ext.ComponentQuery.query('textfield[name=br_oldpartno]')[0].focus(true);
						} else {

						}
					}
		        }
			}
		}, {
			emptyText: 'SCAN OLD PART NUMBER',
			name: 'br_oldpartno',
			listeners: {
				specialkey: function(field, e) {
					if (e.getKey() == e.ENTER) {
						var txtval = field.getValue();
						var len = txtval.length;
						if (len != 0) {
							Ext.ComponentQuery.query('textfield[name=br_newpartno]')[0].setDisabled(false);
							Ext.ComponentQuery.query('textfield[name=br_newpartno]')[0].focus(true);
						} else {
							Ext.ComponentQuery.query('textfield[name=br_newpartno]')[0].setDisabled(true);
						}
					}
		        },
		        change: function(field) {
					var txtval = field.getValue();
					var len = txtval.length;
					if (len == 0) {
						Ext.ComponentQuery.query('textfield[name=br_newpartno]')[0].setDisabled(true);
					} else {
						field.setValue(field.getValue().toUpperCase());
					}

				}
			}
		}, {
			emptyText: 'SCAN NEW PART NUMBER',
			name: 'br_newpartno',
			disabled: true,
			listeners: {
				specialkey: function(field, e) {
					if (e.getKey() == e.ENTER) {
						var form = this.up('form').getForm();
						if(form.isValid()) {
							form.submit({
								url: 'response/inputBorrow.php',
								waitMsg : 'Now transfering data, please wait..',
								success : function(form, action) {
			                        Ext.toast({
									     html: 'Data Saved',
									     title: 'SUCCESS - INOFRMATION',
									     width: 200,
									     align: 't'
									 });
			                        drypart.loadPage(1);
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
		        }
			}
		}, {
			xtype: 'button',
			text: 'RESET',
			name: 'br_reset',
			handler: function() {
				this.up('form').getForm().reset();
			}
		}]
	});
	
	var form_openpart = Ext.create('Ext.form.Panel',{
		// title: 'FORM CONTROL DRY PART',
		// header: { titleAlign: 'center' },
		name: 'form_openpart',
		layout: 'anchor',
		bodyStyle: {
        	background: 'rgba(255, 255, 255, 0)'
        },
		items: [{
			xtype: 'container',
			name: 'open-input-top',
			layout: {
			    type: 'hbox',
			    pack: 'center',
			    align: 'stretch'
			},
			defaults: {
			    // anchor: '100%',
			    // flex: 1,
			    width: 200,
			    padding: '0 5 5 5',
			    fieldStyle: 'font-size:14px;text-align:center;'
			},
			defaultType: 'textfield',
			items: [{
				xtype: 'hiddenfield',
				name: 'openunid'
			}, {
				emptyText: 'SCAN NIK',
				name: 'opennik', // NIK
				// value: '37297',
				allowBlank: false,
				selectOnFocus: true,
				listeners: {
					afterrender: function(field) { field.focus(true,500); },
			        specialkey: function(field, e) {
						if (e.getKey() == e.ENTER) {
							var txtval = field.getValue();
							var len = txtval.length;
							if (len != 0) {
								Ext.ComponentQuery.query('textfield[name=openpartno]')[0].setDisabled(false);
							} else {

							}
							Ext.ComponentQuery.query('textfield[name=openpartno]')[0].focus(true,1);
						}
			        },
			        change: function(field) {
						var txtval = field.getValue();
						var len = txtval.length;
						if (len == 0) {
							Ext.ComponentQuery.query('textfield[name=openpartno]')[0].setDisabled(true);
						}
					}
				}
			}, {
				xtype: 'datefield',
				name: 'openproddate', // PRODUCTION DATE
				editable: false,
				emptyText: 'PRODUCTION DATE',
				format: 'Y-m-d',
			    allowBlank: false
			}]
		} , {
			xtype: 'container',
			name: 'open-input-bot',
			layout: {
			    type: 'hbox',
			    pack: 'center',
			    align: 'stretch'
			},
			defaults: {
			    // anchor: '100%',
			    width: 200,
			    padding: '0 5 5 5',
			    fieldStyle: 'font-size:14px;text-align:center;'
			},
			defaultType: 'textfield',
			items: [{
				emptyText: 'SCAN PART NUMBER',
				name: 'openpartno', // PART NUMBER
				disabled: true,
				listeners: {
					change:function(field) {
		                field.setValue(field.getValue().toUpperCase());
		            }
				}
			}, {
				xtype: 'datefield',
				name: 'openexpdate',  // EXPIRED DATE
				editable: false,
				emptyText: 'EXPIRED DATE',
				submitEmptyText: false, // If set to true, the emptyText value will be sent with the form when it is submitted.
				format: 'Y-m-d',
			    allowBlank: true
			}]
		}],
		dockedItems: [toolbar_openpart]
	});

	var groupingFeature = Ext.create('Ext.grid.feature.GroupingSummary',{
		id: 'group',
		ftype: 'groupingsummary',
		enableGroupingMenu: true
	});

	var grid_openpart = Ext.create('Ext.grid.Panel', {
		store: open_part,
		selModel: Ext.create('Ext.selection.CheckboxModel'),
	    viewConfig: {
	    	enableTextSelection  : true
	    },
	    features: [groupingFeature],
	    columns: [
	    	{ header: 'NO', xtype: 'rownumberer', width: 55, sortable: false },
	    	{ text: 'UNIQUE ID', dataIndex: 'unid', hidden: true },
	    	{ text: 'ID', dataIndex: 'openid', hidden: true },
	    	{ text: 'PART NUMBER', dataIndex: 'partno', width: 200, renderer: whitespace },
	    	{ text: 'QTY', dataIndex: 'qty', width: 80 },
	    	{ text: 'PRODUCTION DATE', dataIndex: 'proddate', flex: 1 },
	    	{ text: '(SELF LIFE)<br>EXPIRED DATE', dataIndex: 'selflife', width: 200, renderer: expstatus},
	    	{ text: 'OPEN DATE', dataIndex: 'opendate', flex: 1 },
	    	{ text: '(FLOOR LIFE)<br>EXPIRED DATE', dataIndex: 'floorlife', width: 200, renderer: expstatus },
	    	{ text: 'NIK', dataIndex: 'nik', width: 80 },
	    	{ text: 'PART STATUS', dataIndex: 'datacode', flex: 1, renderer: partstatus },
	    	{ text: 'PLACE', dataIndex: 'place', flex: 1 },
	    	{ text: 'ACC DATE', dataIndex: 'accdate', flex: 1 }
	    ],
	    bbar: {
	    	xtype: 'pagingtoolbar',
	    	displayInfo	: true,
	    	store: open_part,
	    	items: ['->',{
	    		xtype: 'textfield',
	    		name: 'stpartfldsrc',
	    		width: 600,
	    		emptyText: 'Search part number in here...',
	    		fieldStyle: 'text-align:center;',
	    		listeners: {
	    			specialkey: function(field, e) {
						if (e.getKey() == e.ENTER) {
							open_part.proxy.setExtraParam('stpartfldsrc',field.getValue());
							open_part.loadPage(1);
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

	var panel_openpart = Ext.create('Ext.panel.Panel',{
		border: true,
		layout: 'border',
	    defaults: {
	    	split: false,
	    	plain: true
	    },
	    items: [{
	        region: 'north', // FORM INPUT SIDE
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
	        items: form_openpart
	    }, {
	        region: 'center', // GRID SIDE
	        layout: 'fit',
	        items: grid_openpart
	    }]
	});
</script>