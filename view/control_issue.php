<script type="text/javascript">
	
	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

	Ext.define('issue_control',{
		extend: 'Ext.data.Model',
	    fields: ['unid','issueid','expid','part_no','part_name','model','qty','lotsize','lotno','nik','remark','prod_date','exp_date']
	});

	Ext.define('get_partno',{
		extend: 'Ext.data.Model',
		fields: ['id','partno']
	});

	Ext.define('get_partname',{
		extend: 'Ext.data.Model',
		fields: ['partname']
	});

	//note how we set the 'root' in the reader to match the data structure above
	var issue_store = Ext.create('Ext.data.Store', {
	    model: 'issue_control',
	    autoLoad: true,
	    pageSize: 25,
	    proxy: {
	        type: 'ajax',
	        url: 'json/displayIssue.php',
	        reader: {
	            type: 'json',
	            rootProperty: 'data',
	            totalProperty: 'totalcount'
	        }
	    },
	    listeners: {
	    	load: function(store) {
	    		// store.proxy.setExtraParam('issue_fldsrc','');
				var src_partno = Ext.ComponentQuery.query('textfield[name=src_partno]')[0].getRawValue();
				var src_model = Ext.ComponentQuery.query('textfield[name=src_model]')[0].getValue();
				var src_lotno = Ext.ComponentQuery.query('textfield[name=src_lotno]')[0].getValue();
				var src_proddate = Ext.ComponentQuery.query('datefield[name=src_proddate]')[0].getRawValue();
	    		store.proxy.setExtraParam('issue_fldsrc',src_partno);
	    		store.proxy.setExtraParam('src_model',src_model);
	    		store.proxy.setExtraParam('src_lotno',src_lotno);
	    		store.proxy.setExtraParam('src_proddate',src_proddate);
	    	}
	    }
	});

	var get_partno = Ext.create('Ext.data.Store', {
	    model: 'get_partno',
	    proxy: {
	        type: 'ajax',
	        url: 'json/getPartno.php',
	        reader: {
	            type: 'json',
	            rootProperty: 'data'
	        }
	    }
	    // listeners: {
	    // 	load: function(store, records) {
	    // 		// console.log(records);
	    // 		if (records.length == 0) {
	    // 			// do nothing
	    // 		} else {
	    // 			var expid = store.getAt(0).get('id');
	    // 			var partno = store.getAt(0).get('partno');
	    // 			Ext.ComponentQuery.query('hiddenfield[name=issue_expid]')[0].setValue(expid);
	    // 			Ext.ComponentQuery.query('textfield[name=issue_partno]')[0].setValue(partno);

					// get_partname.proxy.setExtraParam('partno',partno);
					// get_partname.load();
	    // 		}
	    // 	}
	    // }
	});

	var get_partname = Ext.create('Ext.data.Store', {
	    model: 'get_partname',
	    proxy: {
	        type: 'ajax',
	        url: 'json/displayPart.php',
	        reader: {
	            type: 'json',
	            rootProperty: 'data'
	        }
	    },
	    listeners: {
	    	load: function(store, records) {
	    		// console.log(records);
	    		if (records.length == 0) {
	    			// do nothing
	    		} else {
	    			var partname = store.getAt(0).get('partname');
	    			Ext.ComponentQuery.query('textfield[name=issue_partname]')[0].setValue(partname);
	    		}
	    	}
	    }
	});

	var toolbar_issue = Ext.create('Ext.toolbar.Toolbar',{
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
						url: 'response/updateIssue.php',
						waitMsg : 'Now transfering data, please wait..',
						success : function(form, action) {
	                        Ext.Msg.show({
	                        	title   : 'SUCCESS',
	                        	msg     : action.result.msg,
	                        	buttons : Ext.Msg.OK
	                        });
	                        issue_store.loadPage(1);
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
				var rec = grid_issue.getSelectionModel().getSelection();
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
						if(btn == 'yes'){
							for (var i=0;i<len;i++) {
								Ext.Ajax.request({
									url: 'response/deleteIssue.php',
									method: 'POST',
									params: 'issue_unid='+rec[i].data.unid,
									success: function(obj){
										var resp = obj.responseText;
										if(resp !=0) {
											issue_store.loadPage(1);
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
			//disabled: true,
			//hidden: true,
			icon: 'resources/print.png',
			handler	: function(widget, event) {
				var rec = grid_issue.getSelectionModel().getSelection();
				var len = rec.length;
				
				if(len == ""){
					Ext.Msg.show({
						title		:'Message',
						icon		: Ext.Msg.ERROR,
						msg			: "No data selected.",
						buttons		: Ext.Msg.OK
					});
				}
				else{
					//	get selected data
					var i = 0; // initial variable for looping
					var a = ''; // empty string 
					var b = ''; // empty string
					var total = 0;
					// console.log();
					// console.log(rec[0].data.id);
					for (var i=0; i < len; i++) {
						cb 	= a + '' + rec[i].data.issueid;
						a 	= a + '' + rec[i].data.issueid + '/';
						
						lbl = b + '' + rec[i].data.issueid;
						b 	= b + '' + rec[i].data.issueid + ', ';
						
						total++;
					}
					window.open ("response/printIssue_sato.php?total="+total+"&cb="+cb+"");
				}
			}
		},
		'->',{
			name: 'create',
			icon: 'resources/create.png',
			formBind: true,
			handler: function() {
				var getForm = this.up('form').getForm();
				if (getForm.isValid()) {
					getForm.submit({
						url: 'response/inputIssue.php',
						waitMsg : 'Now transfering data, please wait..',
						success : function(form, action) {
	                        Ext.Msg.show({
	                        	title   : 'SUCCESS',
	                        	msg     : action.result.msg,
	                        	buttons : Ext.Msg.OK
	                        });
	                        issue_store.loadPage(1);
	      					// Ext.toast({
							// 	html: '<h1>'+action.result.msg+'</h1>',
							// 	title: 'SUCCESS',
							// 	// width: 200,
							// 	align: 'b'
							// });
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
				Ext.ComponentQuery.query('textfield[name=issue_nik]')[0].setEditable(true);
				Ext.ComponentQuery.query('textfield[name=src_partno]')[0].reset();
				Ext.ComponentQuery.query('textfield[name=src_model]')[0].reset();
				Ext.ComponentQuery.query('textfield[name=src_lotno]')[0].reset();
				Ext.ComponentQuery.query('datefield[name=src_proddate]')[0].reset();
				issue_store.proxy.setExtraParam('issue_fldsrc','');
	    		issue_store.proxy.setExtraParam('src_model','');
	    		issue_store.proxy.setExtraParam('src_lotno','');
	    		issue_store.proxy.setExtraParam('src_proddate','');
				issue_store.loadPage(1);
			}
		},{
			name: 'download',
			icon: 'resources/unduh.png',
			handler: function() {
				var src_partno = Ext.ComponentQuery.query('textfield[name=src_partno]')[0].getValue();
				var src_model = Ext.ComponentQuery.query('textfield[name=src_model]')[0].getValue();
				var src_lotno = Ext.ComponentQuery.query('textfield[name=src_lotno]')[0].getValue();
				var src_proddate = Ext.ComponentQuery.query('datefield[name=src_proddate]')[0].getRawValue();
				// console.log({"partno":src_partno},{"model":src_model},{"lotno":src_lotno},{"proddate":src_proddate});
				window.open('response/downloadIssue.php?src_partno='+src_partno+'&src_model='+src_model+'&src_lotno='+src_lotno+'&src_proddate='+src_proddate, "_self");
			}
		}]
	});

	var form_issue = Ext.create('Ext.form.Panel',{
		name: 'form_exp',
		layout: {
			type: 'hbox',
			pack: 'center',
			align: 'stretch'
		},
		bodyStyle: {
        	background: 'rgba(255, 255, 255, 0)'
        },
	    items: [{
	    	xtype: 'container',
	    	name: 'form_issue_left',
	    	layout: 'vbox',
	    	defaultType: 'textfield',
	    	defaults: { labelWidth: 115 },
	    	items: [{
				xtype: 'hiddenfield',
				name: 'issue_unid'
			},{
				xtype: 'hiddenfield',
				name: 'issue_expid'
			},{
			    fieldLabel: 'PART NUMBER',
			    name: 'issue_partno',
			    allowBlank: false,
			    afterLabelTextTpl: required,
			    minLength: 15,
			    listeners: {
			    	afterrender: function(field) { field.focus(true,500); },
			    	specialkey: function(field, x) {
			    		if (x.getKey() == x.ENTER) {
			    			// var expid = field.getValue().substr(16,13);
							// get_partname.proxy.setExtraParam('issue_partno',expid);
							var partno = field.getValue().substr(0,15);
							get_partname.proxy.setExtraParam('partno', partno)
							get_partname.loadPage();
							// console.log(field.getValue());
						}
			    	},
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
			    		var partno = field.getValue().substr(0,15);
			    		var expid = field.getValue().substr(16,13);
			    		field.setValue(field.getValue().toUpperCase());
		                field.setValue(partno);
		                Ext.ComponentQuery.query('hiddenfield[name=issue_expid]')[0].setValue(expid);


		            }
			    }
			},{
				fieldLabel: 'PART NAME',
				name: 'issue_partname',
				allowBlank: false,
				afterLabelTextTpl: required,
				listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			},{
			    fieldLabel: 'QTY ISSUE',
			    name: 'issue_qty',
			    maskRe: /[0-9.,]/,
			    allowBlank: false,
			    afterLabelTextTpl: required
			},{
			    fieldLabel: 'LOT SIZE',
			    name: 'issue_lotsize',
			    allowBlank: false,
			    afterLabelTextTpl: required,
			    listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			},{
			    fieldLabel: 'LOT NUMBER',
			    name: 'issue_lotno',
			    allowBlank: false,
			    afterLabelTextTpl: required,
			    listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			}]
	    },{xtype:'tbspacer',width:10},
	    {
	    	xtype: 'container',
	    	name: 'form_issue_right',
	    	layout: 'vbox',
	    	defaultType: 'textfield',
	    	defaults: { labelWidth: 100 },
	    	items: [{
				fieldLabel: 'MODEL',
				name: 'issue_model',
				allowBlank: false,
				afterLabelTextTpl: required,
				listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			},{
		    	xtype: 'datefield',
		        fieldLabel: 'OPEN DATE',
		        name: 'issue_opendate',
		        format: 'Y-m-d',
		        allowBlank: false,
		        editable: false,
		        afterLabelTextTpl: required
		    },{
				fieldLabel: 'NIK',
				name: 'issue_nik',
				allowBlank: false,
		    	afterLabelTextTpl: required,
		    	emptyText: 'SCAN NIK HERE...',
				listeners: {
	                specialkey: function(field, e) {
						if (e.getKey() == e.ENTER) {
							Ext.ComponentQuery.query('textareafield[name=issue_remark]')[0].focus(true,1);
						}
	                },
	                change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
	            }
			},{
				xtype: 'textareafield',
				name: 'issue_remark',
				fieldLabel: 'REMARK',
				grow: true,
				listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			}]
	    }],
	    dockedItems: [toolbar_issue]
	});

	var grid_issue = Ext.create('Ext.grid.Panel', {
	    store: issue_store,
	    selModel: Ext.create('Ext.selection.CheckboxModel'),
	    viewConfig: {
	    	enableTextSelection  : true
	    },
	    columns: [
	    	{ header: 'NO', xtype: 'rownumberer', width: 55, sortable: false },
	    	{ text: 'UNIQUE ID', dataIndex: 'unid', hidden: true },
	    	{ text: 'ID', dataIndex: 'issueid', hidden: true },
	    	{ text: 'EXP. ID', dataIndex: 'expid', hidden: true },
	        { text: 'PART NUMBER', dataIndex: 'part_no', flex: 1, layout: {type:'hbox',align:'stretch',pack:'center'},
				items: [{
					xtype: 'textfield',
					name: 'src_partno',
					emptyText: 'Part Number...',
					listeners: {
	    				specialkey: function(field, e) {
							if (e.getKey() == e.ENTER) {
								issue_store.proxy.setExtraParam('issue_fldsrc',field.getValue());
								issue_store.proxy.setExtraParam('src_model',Ext.ComponentQuery.query('textfield[name=src_model]')[0].getValue());
								issue_store.proxy.setExtraParam('src_lotno',Ext.ComponentQuery.query('textfield[name=src_lotno]')[0].getValue());
								issue_store.proxy.setExtraParam('src_proddate',Ext.ComponentQuery.query('datefield[name=src_proddate]')[0].getRawValue());
								issue_store.loadPage(1);
						// console.log(field.value);
							}
                		}
	    			}
				}]
			},
	        { text: 'PART NAME', dataIndex: 'part_name', flex: 1 },
	        { text: 'MODEL', dataIndex: 'model', flex: 1,layout: {type:'hbox',align:'stretch',pack:'center'},
				items: [{
					xtype: 'textfield',
					name: 'src_model',
					emptyText: 'Model...',
					listeners: {
	    				specialkey: function(field, e) {
							if (e.getKey() == e.ENTER) {
								issue_store.proxy.setExtraParam('src_model',field.getValue());
								issue_store.proxy.setExtraParam('issue_fldsrc',Ext.ComponentQuery.query('textfield[name=src_partno]')[0].getValue());
								issue_store.proxy.setExtraParam('src_lotno',Ext.ComponentQuery.query('textfield[name=src_lotno]')[0].getValue());
								issue_store.proxy.setExtraParam('src_proddate',Ext.ComponentQuery.query('datefield[name=src_proddate]')[0].getRawValue());
								issue_store.loadPage(1);
							}
                		}
	    			}
				}] 
			},
	        { text: 'QTY ISSUE', dataIndex: 'qty', flex: 1 },
	        { text: 'LOT SIZE', dataIndex: 'lotsize',flex: 1 },
	        { text: 'LOT NUMBER', dataIndex: 'lotno',flex: 1, layout: {type:'hbox',align:'stretch',pack:'center'},
				items: [{
					xtype: 'textfield',
					name: 'src_lotno',
					emptyText: 'Lot Number...',
					listeners: {
	    				specialkey: function(field, e) {
							if (e.getKey() == e.ENTER) {
								issue_store.proxy.setExtraParam('src_lotno',field.getValue());
								issue_store.proxy.setExtraParam('issue_fldsrc',Ext.ComponentQuery.query('textfield[name=src_partno]')[0].getValue());
								issue_store.proxy.setExtraParam('src_model',Ext.ComponentQuery.query('textfield[name=src_model]')[0].getValue());
								issue_store.proxy.setExtraParam('src_proddate',Ext.ComponentQuery.query('datefield[name=src_proddate]')[0].getRawValue());
								issue_store.loadPage(1);
							}
                		}
	    			}
				}] },
	        { text: 'OPEN DATE', dataIndex: 'opendate',flex: 1 },
	        { text: 'PROD. DATE', dataIndex: 'prod_date',flex: 1, layout: {type:'hbox',align:'stretch',pack:'center'},
				items: [{
					xtype: 'datefield',
					name: 'src_proddate',
					emptyText: 'Prod. Date...',
					format: 'Y-m-d',
					editable: false,
					listeners: {
	    				select: function(field) {
							issue_store.proxy.setExtraParam('issue_fldsrc',Ext.ComponentQuery.query('textfield[name=src_partno]')[0].getValue());
							issue_store.proxy.setExtraParam('src_model',Ext.ComponentQuery.query('textfield[name=src_model]')[0].getValue());
							issue_store.proxy.setExtraParam('src_lotno',Ext.ComponentQuery.query('textfield[name=src_lotno]')[0].getValue());
							issue_store.proxy.setExtraParam('src_proddate',field.getRawValue());
							issue_store.loadPage(1);
                		}
	    			}
				}] },
	        { text: 'EXP. DATE', dataIndex: 'exp_date',flex: 1,
	        	renderer: function(val) {
		          	var today = new Date();
					var dd = today.getDate();
					var mm = today.getMonth()+1; //January is 0!
					var yyyy = today.getFullYear();

					if(dd<10) {
						dd = '0'+dd
					} 

					if(mm<10) {
					    mm = '0'+mm
					} 

					today = yyyy + '-' + mm + '-' + dd;

					if (today > val) {
						return ("<span style=color:red;>"+val+" ( EXPIRED PART )</span>");
					} else {
						return (val);
					}
		          	// return val;
		          	// console.log(today);
		        }
	        },
	        { text: 'NIK', dataIndex: 'nik',flex: 1 },
	        { text: 'REMARK', dataIndex: 'remark',flex: 1 }
	    ],
	    bbar: {
	    	xtype: 'pagingtoolbar',
	    	displayInfo	: true,
	    	store: issue_store,
	    	items: ['->',{
	    		xtype: 'textfield',
	    		name: 'issue_fldsrc',
	    		width: 600,
	    		emptyText: 'Search part number in here...',
	    		// listeners: {
	    		// 	specialkey: function(field, e) {
				// 		if (e.getKey() == e.ENTER) {
				// 			issue_store.proxy.setExtraParam('issue_fldsrc',field.getValue());
				// 			issue_store.loadPage(1);
				// 		}
				// 	}
	    		// }
	    	}]
	    },
	    listeners: {
	    	select: function(grid, rowIndex, colIndex) {
                var rec = this.getSelectionModel().getSelection();
                var unid = rec[0].data.unid;
                var partno = rec[0].data.part_no;
                var partname = rec[0].data.part_name;
                var model = rec[0].data.model;
                var qty = rec[0].data.qty;
                var lotsize = rec[0].data.lotsize;
                var lotno = rec[0].data.lotno;
                var opendate = rec[0].data.opendate;
                var nik = rec[0].data.nik;
                var remark = rec[0].data.remark;

                var txt_unid = Ext.ComponentQuery.query('hiddenfield[name=issue_unid]')[0];

                var txt_partno = Ext.ComponentQuery.query('textfield[name=issue_partno]')[0];
                var txt_partname = Ext.ComponentQuery.query('textfield[name=issue_partname]')[0];
                var txt_model = Ext.ComponentQuery.query('textfield[name=issue_model]')[0];
                var txt_qty = Ext.ComponentQuery.query('textfield[name=issue_qty]')[0];
                var txt_lotsize = Ext.ComponentQuery.query('textfield[name=issue_lotsize]')[0];
                var txt_lotno = Ext.ComponentQuery.query('textfield[name=issue_lotno]')[0];
                var txt_opendate = Ext.ComponentQuery.query('datefield[name=issue_opendate]')[0];
                var txt_nik = Ext.ComponentQuery.query('textfield[name=issue_nik]')[0];
                var txt_remark = Ext.ComponentQuery.query('textareafield[name=issue_remark]')[0];

                txt_unid.setValue(unid);
                txt_partno.setValue(partno);
                txt_partname.setValue(partname);
                txt_model.setValue(model);
                txt_qty.setValue(qty);
                txt_lotsize.setValue(lotsize);
                txt_lotno.setValue(lotno);
                txt_opendate.setValue(opendate);
                txt_nik.setValue(nik);
                txt_remark.setValue(remark);

                Ext.ComponentQuery.query('textfield[name=issue_nik]')[0].setEditable(false);
            }
	    }
	});
	
	var panel_issue = Ext.create('Ext.panel.Panel',{
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
	        	background: 'url("resources/bg-image.jpg") no-repeat center bottom',
	        	backgroundSize: 'cover'
	        },
	        items: form_issue
	    }, {
	        region: 'center',
	        layout: 'fit',
	        items: grid_issue
	    }]
	});
</script>