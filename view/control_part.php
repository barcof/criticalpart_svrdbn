<div id="section">
	<?php
		session_start();
		$session_value=(isset($_SESSION['username']))?$_SESSION['username']:'';
		include_once 'control_baking.php';
		include_once 'control_issue.php';
		include_once 'control_drypart.php';
		// include_once 'control_drpbaking.php';
		include_once 'control_detail.php';
	?>
</div>
<script type="text/javascript">
	Ext.Loader.setConfig({enabled:true});
	Ext.Loader.setPath('Ext.ux','../../extjs-5.1.1/build/examples/ux');

	Ext.onReady(function(){
		Ext.QuickTips.init();

		var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
		var expiredstatus = function(val) {
			// Harus di maintain karna status EXPIRED di hardcode
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1; //January is 0!
			var yyyy = today.getFullYear();

			if(dd<10) { dd = '0'+dd } 

			if(mm<10) { mm = '0'+mm } 

			today = yyyy + '-' + mm + '-' + dd;

			if (mm == val.substr(5,2)-1) {
				return ("<span style=color:#ff6f00;><b>"+val+"</b><br>( WILL BE EXPIRED SOON )</span>");
			} else { return (val); }

			if (today > val) {
				return ("<span style=color:red;>"+val+" ( EXPIRED PART )</span>");
			} else { return (val); }
		}

		Ext.define('exp_control',{
			extend: 'Ext.data.Model',
		    fields: ['unid','id','part_no','qty','balance','lotno','prod_date','exp_date','exp_after','nik']
		});

		Ext.define('exp_supp',{
			extend: 'Ext.data.Model',
			fields: ['suppcode','suppname']
		});

		Ext.define('exp_part',{
			extend: 'Ext.data.Model',
			fields: ['partno','parname','stdpack']
		});

		var exp_control = Ext.create('Ext.data.Store', {
			model: 'exp_control',
			autoLoad: true,
			pageSize: 25,
		    proxy: {
		        type: 'ajax',
		        url: 'json/displayExp.php',	
		        reader: {
		            type: 'json',
		            rootProperty: 'data',
		            totalProperty: 'totalcount'
		        }
		    },
		    listeners: {
		    	load: function(store) {
		    		store.proxy.setExtraParam('fldsrc','');
		    	}
		    }
		});

		var exp_supp = Ext.create('Ext.data.Store', {
			model: 'exp_supp',
			autoLoad: true,
		    proxy: {
		        type: 'ajax',
		        url: 'json/displaySupp.php',	
		        reader: {
		            type: 'json',
		            rootProperty: 'data'
		        }
		    }
		    // listeners: {
		    // 	load: function(store) {
		    // 		store.proxy.setExtraParam('supplier','');
		    // 	}
		    // }
		});

		var exp_part = Ext.create('Ext.data.Store', {
			model: 'exp_part',
		    proxy: {
		        type: 'ajax',
		        url: 'json/displayPart.php',	
		        reader: {
		            type: 'json',
		            rootProperty: 'data'
		        }
		    }
		    // listeners: {
		    // 	load: function(store, records) {
		    // 		// store.proxy.setExtraParam('partno','');
		    // 		if (records.length == 0) {
	    	// 		// do nothing
	    	// 		} else {
			   //  		var stdpack = exp_part.getAt(0).get('stdpack');
	     //            	Ext.ComponentQuery.query('textfield[name=issue_stdpack]')[0].setValue(stdpack);
	     //            }
		    // 	}
		    // }
		});

		var toolbar_exp = Ext.create('Ext.toolbar.Toolbar',{
			dock:'bottom',
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
					Ext.ComponentQuery.query('textfield[name=nik]')[0].setEditable(true);
					exp_supp.proxy.setExtraParam('supplier','');
					exp_part.proxy.setExtraParam('partno','');
					exp_part.proxy.setExtraParam('supplier','');
					exp_supp.load();
					exp_part.load();
				}
			}]
		});

		var form_exp = Ext.create('Ext.form.Panel',{
			name: 'form_exp',
			layout: 'anchor',
			width: 400,
			bodyStyle: {
	        	background: 'rgba(255, 255, 255, 0)'
	        },
		    defaults: {
		        anchor: '100%',
		        labelWidth: 150
		    },
		    defaultType: 'textfield',
		    items: [{
		    	xtype: 'hiddenfield',
		    	name: 'unid'
		    },{
		    	xtype: 'hiddenfield',
		    	name: 'stdpack',
		    	listeners: {
		    		change: function(field) {
		    			if (field.getValue() == 999999) {
		    				Ext.Msg.alert('WARNING','Please update Std. Packing !');
		    				Ext.ComponentQuery.query('combobox[name=partno]')[0].reset();
		    				field.reset();
		    			}
		    		}
		    	}
		    },{
		    	fieldLabel: 'NIK',
		    	name: 'nik',
		    	allowBlank: false,
		    	afterLabelTextTpl: required,
		    	minLength: 5,
		    	emptyText: 'SCAN NIK HERE...',
		    	listeners: {
	                afterrender: function(field) { field.focus(true,500); },
	                specialkey: function(field, e) {
						if (e.getKey() == e.ENTER) {
							Ext.ComponentQuery.query('textfield[name=supplier]')[0].focus(true,1);
						}
	                },
	                change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
	            }
		    },{
		    	xtype: 'combobox',
		    	name: 'supplier',
		    	fieldLabel: 'SUPPLIER',
		    	store: exp_supp,
		    	queryMode: 'proxy',
		    	queryParam: 'supplier',
                displayField: 'suppname',
                valueField: 'suppcode',
                allowBlank: false,
                afterLabelTextTpl: required,
                listConfig: {
                    loadingText: 'Searching...',
                    emptyText: 'No data found.',
                    getInnerTpl: function() {
                        return '<div> {suppname} &#8212; <b>( {suppcode} )</b></div>';
                    }
                },
                listeners: {
                	select: function(field) {
                		var val = field.getValue();
                		exp_part.proxy.setExtraParam('supplier', val); // set parameter for exp_part store
                		exp_part.load();
                	}
                }
		    },{
		    	xtype: 'combobox',
		        name: 'partno',
		        fieldLabel: 'PART NUMBER',
		        store: exp_part,
		        queryMode: 'proxy',
		        // queryParam: 'partno',
		        displayField: 'partno',
		        valueField: 'partno',
		        allowBlank: false,
		        afterLabelTextTpl: required,
		        listConfig: {
                    loadingText: 'Searching...',
                    emptyText: 'No data found.'
                },
		        listeners: {
                	change: function(field) {
	            		if(field.getValue() == null){
	            			exp_part.proxy.setExtraParam('partno', '');
							exp_part.loadPage(1);
						}
                	// 	var val = field.getValue();
                	// 	exp_part.proxy.setExtraParam('partno', val); // set parameter for exp_part store
                	// 	// exp_part.load();
                	},
                	select: function(combo, records, eOpts) {
                		// console.log(combo.getSelectedRecord().get('stdpack')); // this is how to get another record on combobox store

                		var val = combo.getValue();
                		exp_part.proxy.setExtraParam('partno', val); // set parameter for exp_part store
	                	exp_part.load();
                		var stdpack = combo.getSelectedRecord().get('stdpack');
	                	Ext.ComponentQuery.query('hiddenfield[name=stdpack]')[0].setValue(stdpack);

                	}
                }
		    },{
		        fieldLabel: 'QTY',
		        maskRe: /[0-9.,]/,
		        name: 'qty',
		        allowBlank: false,
		        afterLabelTextTpl: required
		    },{
		        fieldLabel: 'LOT NO. SUPPLIER',
		        name: 'lotno',
		        listeners: {
		        	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
		        }
		    },{
		    	xtype: 'datefield',
		        fieldLabel: 'PRODUCTION DATE',
		        name: 'prod_date',
		        format: 'Y-m-d',
		        allowBlank: false,
		        editable: false,
		        afterLabelTextTpl: required
		    }],
		    dockedItems: [toolbar_exp]
		});

		// var form_login = Ext.create('Ext.form.Panel',{
		// 	name: 'form_login',
		// 	layout: 'anchor',
		// 	width: 400,
		// 	defaults: {
		// 		anchor: '100%',
		// 		labelWidth: 150
		// 	},
		// 	defaultType: 'textfield',
		// 	// items: [{
		// 	// 	fieldLabel: 
		// 	// }]
		// });

		var grid_exp = Ext.create('Ext.grid.Panel', {
		    store: exp_control,
		    selModel: Ext.create('Ext.selection.CheckboxModel'),
		    viewConfig: {
		    	enableTextSelection  : true
		    },
		    columns: [
		    	{ header: 'NO', xtype: 'rownumberer', width: 55, sortable: false },
		    	{ text: 'UNIQUE ID', dataIndex: 'unid', hidden: true },
		    	{ text: 'ID', dataIndex: 'id', hidden: true },
		    	{ text: 'SUPPLIER CODE', dataIndex: 'suppcode', hidden: true },
		    	{ text: 'SUPPLIER NAME', dataIndex: 'suppname', flex: 1 },
		        { text: 'PART NUMBER', dataIndex: 'part_no', flex: 1 },
		        { text: 'QTY', dataIndex: 'qty', width: 70 },
		        { text: 'BALANCE', dataIndex: 'balance', width: 70 },
		        { text: 'LOT NUMBER', dataIndex: 'lotno', width: 120 },
		        { text: 'PRODUCTION DATE', dataIndex: 'prod_date',flex: 1 },
		        { text: 'EXPIRED DATE',
		          dataIndex: 'exp_date',
		          flex: 1,
		          renderer: expiredstatus
		    	},
		    	{ text: 'EXP. AFTER OPEN', dataIndex: 'exp_after', hidden: true},
		        { text: 'INPUT BY', dataIndex: 'nik',flex: 1 }
		    ],
		    bbar: {
		    	xtype: 'pagingtoolbar',
		    	displayInfo	: true,
		    	store: exp_control,
		    	items: ['->',{
		    		xtype: 'textfield',
		    		name: 'fldsrc',
		    		width: 600,
		    		emptyText: 'Search part number in here...',
		    		listeners: {
		    			specialkey: function(field, e) {
							if (e.getKey() == e.ENTER) {
								exp_control.proxy.setExtraParam('fldsrc',field.getValue());
								exp_control.loadPage(1);
								// console.log(field.value);
							}
		                }
		    		}
		    	}]
		    },
		    listeners: {
		    	select: function(grid, rowIndex, colIndex) {
                    var rec = this.getSelectionModel().getSelection();
                    // if (!rec.data) {

                    // } else {
	                    var unid = rec[0].data.unid;
	                    var suppcode = rec[0].data.suppcode;
	                    var partno = rec[0].data.part_no;
	                    var qty = rec[0].data.qty;
	                    var lotno = rec[0].data.lotno;
	                    var prod_date = rec[0].data.prod_date;
	                    var nik = rec[0].data.nik;


	                    var txt_unid = Ext.ComponentQuery.query('hiddenfield[name=unid]')[0];
	                    var txt_supp = Ext.ComponentQuery.query('combobox[name=supplier]')[0];
	                    var txt_partno = Ext.ComponentQuery.query('textfield[name=partno]')[0];
	                    var txt_qty = Ext.ComponentQuery.query('textfield[name=qty]')[0];
	                    var txt_lotno = Ext.ComponentQuery.query('textfield[name=lotno]')[0];
	                    var txt_prod_date = Ext.ComponentQuery.query('datefield[name=prod_date]')[0];
	                    var txt_nik = Ext.ComponentQuery.query('textfield[name=nik]')[0];

	                    txt_unid.setValue(unid);
	                    txt_supp.setValue(suppcode);
	                    txt_partno.setValue(partno);
	                    txt_qty.setValue(qty);
	                    txt_lotno.setValue(lotno);
	                    txt_prod_date.setValue(prod_date);
	                    txt_nik.setValue(nik);

	                    exp_part.proxy.setExtraParam('supplier', suppcode); // set parameter for exp_part store
                		exp_part.load();


	                    Ext.ComponentQuery.query('textfield[name=nik]')[0].setEditable(false);
                    // }
                }
		    }
		});

		var grid_details = Ext.create('Ext.grid.Panel', {
			// selModel: Ext.create('Ext.selection.CheckboxModel'),
		    viewConfig: {
		    	enableTextSelection  : true
		    },
		    width: 400,
		    columns: [
		    	{ header: 'NO', xtype: 'rownumberer', width: 55, sortable: false },
		    	{ text: 'UNIQUE ID', dataIndex: '', hidden: true },
		    	{ text: 'ID', dataIndex: '', hidden: true },
		    	{ text: 'PART NUMBER', dataIndex: 'part_no', flex: 1 },
		    	{ text: 'SCAN IN', dataIndex: '', flex: 1 },
		    	{ text: 'SCAN OUT', dataIndex: '', flex: 1 }
		    ]
		});

		var panel_exp = Ext.create('Ext.panel.Panel',{
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
		        	background: 'url("resources/bg-image.jpg") no-repeat center left',
		        	backgroundSize: 'cover'
		        },
		        items: form_exp
		    }, {
		        region: 'center',
		        layout: 'fit',
		        items: grid_exp
		    }]
		});

		var win_login = Ext.create('Ext.form.Panel',{
			title: 'FORM LOGIN',
			// hidden: true,
			layout: {
				type: 'vbox',
			    pack: 'center',
			    align: 'center'
			},
			items: [{
				xtype: 'textfield',
	            name: 'username',
	            fieldLabel: 'User Name',
	            allowBlank: false
			},{
				xtype: 'textfield',
	            name: 'password',
	            fieldLabel: 'Password',
	            inputType: 'password',
	            allowBlank: false
			},{
				xtype: 'button',
                formBind: true,
                disabled: true,
                scale: 'medium',
                text: 'LOGIN',
                width: 280,
                handler: function() {
                    var form = this.up('form').getForm();
					if (form.isValid()) {
						form.submit({
							url: 'login.php',
							waitMsg: 'Check for authentication, Please wait..',
							success: function(form, action) {
								Ext.Msg.show({
		                        	title   : 'SUCCESS',
		                        	msg     : action.result.msg,
		                        	buttons : Ext.Msg.OK
		                        });
		                        form.reset();
		                        location.reload();
		                        // Ext.getCmp('form-login').hide();
		                        // Ext.getCmp('form-logout').show();
		                        console.log(action.result);

		                    },
		                    failure : function(form, action) {
		                        Ext.Msg.show({
			                        title   : 'WARNING',
			                        icons   : Ext.Msg.ERROR,
			                        msg     : action.result.msg,
			                        buttons : Ext.Msg.OK
		                        });
		                        console.log(action.result);
		                    }
						});
					}
				
                }
			}]

		});

		win_logout = Ext.create('Ext.form.Panel',{
			title: 'FORM LOGOUT',
			layout: {
				type: 'vbox',
			    pack: 'center',
			    align: 'center'
			},
			items: [{
				html: '<h1>SILAKAN KLIK TOMBOL DI BAWAH UNTUK LOGOUT</h1>'
			},{
				xtype: 'button',
                // formBind: true,
                // disabled: true,
                scale: 'medium',
                text: 'LOGOUT',
                // width: 280,
                handler: function() {
                    var form = this.up('form').getForm();
					if (form.isValid()) {
						form.submit({
							url: 'logout.php',
							waitMsg: 'Check for authentication, Please wait..',
							success: function(form, action) {
								Ext.Msg.show({
		                        	title   : 'SUCCESS',
		                        	msg     : action.result.msg,
		                        	buttons : Ext.Msg.OK
		                        });
		                        // form.reset();
		                        location.reload();
		                        // Ext.getCmp('form-login').hide();
		                        // Ext.getCmp('form-logout').show();
		                        // console.log(action.result);

		                    },
		                    failure : function(form, action) {
		                        Ext.Msg.show({
			                        title   : 'WARNING',
			                        icons   : Ext.Msg.ERROR,
			                        msg     : action.result.msg,
			                        buttons : Ext.Msg.OK
		                        });
		                        // console.log(action.result);
		                    }
						});
					}
				
                }
			}]
		});

		var tab_exp = Ext.create('Ext.tab.Panel',{
			activeTab: 1, // remove after finish develop
			tabRotation: 0,
			tabPosition: 'top',
			plain: true,
			tabBar: {
		        flex: 1,
		        layout: {
		        	pack: 'center',
		            align: 'stretch',
		            overflowHandler: 'none'
		        }
		    },
			defaults: {
				bodyPadding: 5,
				bodyStyle: {
	            	background: '#ADD2ED'
	            },
			},
			items: [
		        {
		            title: 'CONTROL EXPIRED DATE',
		            layout: 'fit',
		            items: panel_exp
		        },{
		        	title: 'DRY PART CONTROL',
		        	layout: 'fit',
		        	items: tab_drypart
		        },{
		            title: 'BAKING CONTROL',
		            layout: 'fit',
		            items: panel_baking
		        },{
		        	title: 'ISSUE PART',
		        	layout: 'fit',
		        	items: panel_issue
		        },{
		        	title: 'ADMINISTRATOR',
		        	layout: 'fit',
		        	items: [win_login,win_logout],
		        	id: 'form-login',
		        	listeners: {
						activate: function() {
							console.log('Activated');
						}
					}
				}
		    ]
		});

		Ext.create('Ext.container.Viewport', {
		    layout: 'border',
		    renderTo: 'section',
		    defaults: {
		    	split: true
		    },
		    items: [{
		        region: 'north',
		        html: '<h1 class="x-panel-header" style="text-align:center;">CRITICAL PART CONTROL</h1>',
		        bodyStyle: {
		        	background: '#157FCC',
		        	color: '#ffffff'
		        },
		        height: 50,
		        maxHeight: 50,
		        minHeight: 50,
		    }, {
		        region: 'center',
		        layout: 'fit',
		        bodyStyle: {
		        	background: '#4B9CD7',
		        	color: '#ffffff'
		        },
		        items: tab_exp
		    }],
		    listeners: {
		    	render: function() {
		    		// console.log('berak');
		    		var username = '<?php echo $session_value;?>';

		    		if ( username == null || username == '') {
						win_logout.hide();
						Ext.getCmp('delete-master').setHidden(true);
						console.log('logout');
					} else {
						win_login.hide();
						Ext.getCmp('delete-master').setHidden(false);
						console.log('login');
					}
		    	}
		    }
		});
	})
</script>