<script type="text/javascript">

	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

	Ext.define('baking_control',{
			extend: 'Ext.data.Model',
		    fields: ['unid','id','expid','part_no','model','process','qty','lotno','temperature','duration','nikin','nikout','datein','dateout','remark']
		});

	//note how we set the 'root' in the reader to match the data structure above
	var baking_control = Ext.create('Ext.data.Store', {
	    model: 'baking_control',
	    autoLoad: true,
	    pageSize: 25,
	    proxy: {
	        type: 'ajax',
	        url: 'json/displayBaking.php',
	        reader: {
	            type: 'json',
	            rootProperty: 'data',
	            totalProperty: 'totalcount'
	        }
	    },
	    listeners: {
	    	load: function(store) {
	    		store.proxy.setExtraParam('baking_fldsrc',' ');
	    	}
	    }
	});

	var toolbar_baking = Ext.create('Ext.toolbar.Toolbar',{
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
						url: 'response/updateBaking.php',
						waitMsg : 'Now transfering data, please wait..',
						success : function(form, action) {
	                        Ext.Msg.show({
	                        	title   : 'SUCCESS',
	                        	msg     : action.result.msg,
	                        	buttons : Ext.Msg.OK
	                        });
	                        baking_control.loadPage(1);
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
				var rec = grid_baking.getSelectionModel().getSelection();
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
									url: 'response/deleteBaking.php',
									method: 'POST',
									params: 'baking_unid='+rec[i].data.unid,
									success: function(obj){
										var resp = obj.responseText;
										if(resp !=0) {
											baking_control.loadPage(1);
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
			name: 'print', // temporary hidden and disabled
			// disabled: true,
			// hidden: true,
			icon: 'resources/print.png',
			handler	: function(widget, event) {
				var rec = grid_baking.getSelectionModel().getSelection();
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
					
					for (var i=0; i < len; i++) {
						cb 	= a + '' + rec[i].data.id;
						a 	= a + '' + rec[i].data.id + '/';
						
						lbl = b + '' + rec[i].data.id;
						b 	= b + '' + rec[i].data.id + ', ';
						
						total++;
					}
					window.open("response/printBaking_sato.php?total="+total+"&cb="+cb+"");
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
						url: 'response/inputBaking.php',
						waitMsg : 'Now transfering data, please wait..',
						success : function(form, action) {
	                        Ext.Msg.show({
	                        	title   : 'SUCCESS',
	                        	msg     : action.result.msg,
	                        	buttons : Ext.Msg.OK
	                        });
	                        baking_control.loadPage(1);
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
			}
		}]
	});

	var form_baking = Ext.create('Ext.form.Panel',{
		name: 'form_exp',
		layout: 'hbox',
		bodyStyle: {
        	background: 'rgba(255, 255, 255, 0)'
        },
	    items: [{
	    	xtype: 'container',
	    	name: 'form_baking_left',
	    	layout: 'vbox',
	    	defaultType: 'textfield',
	    	defaults: { labelWidth: 130 },
	    	items: [{
				xtype: 'hiddenfield',
				name: 'baking_unid'
			},{
				xtype: 'hiddenfield',
				name: 'baking_expid'
			},{
			    fieldLabel: 'PART NUMBER',
			    name: 'baking_partno',
			    allowBlank: false,
		        afterLabelTextTpl: required,
			    listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
			    		var partno = field.getValue().substr(0,15);
			    		var expid = field.getValue().substr(16,13);
		                field.setValue(partno);
		                Ext.ComponentQuery.query('hiddenfield[name=baking_expid]')[0].setValue(expid);
		            }
			    }
			},{
				fieldLabel: 'MODEL',
				name: 'baking_model',
				allowBlank: false,
				afterLabelTextTpl: required,
				listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			},{
				fieldLabel: 'PROCESS',
				name: 'baking_process',
				allowBlank: false,
				afterLabelTextTpl: required,
				listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			},{
			    fieldLabel: 'QTY',
			    name: 'baking_qty',
			    maskRe: /[0-9.,]/,
			    allowBlank: false,
			    afterLabelTextTpl: required
			},{
			    fieldLabel: 'LOT NUMBER',
			    name: 'baking_lotno',
			    allowBlank: false,
			    afterLabelTextTpl: required,
			    listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			},{
				fieldLabel: 'TEMPERATURE',
				name: 'baking_temp',
				maskRe: /[0-9.,]/,
				allowBlank: false,
				afterLabelTextTpl: required
			}]
	    },{xtype:'tbspacer',width:10},
	    {
	    	xtype: 'container',
	    	width: 500,
	    	name: 'form_baking_right',
	    	layout: 'vbox',
	    	defaultType: 'textfield',
	    	defaults: { labelWidth: 130 },
	    	items: [{
				fieldLabel: 'DURATION',
				name: 'baking_duration',
				maskRe: /[0-9.,]/,
				allowBlank: false,
				afterLabelTextTpl: required
			},{
				fieldLabel: 'NIK IN',
				name: 'baking_nik_in',
				allowBlank: false,
				afterLabelTextTpl: required,
				listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			},{
				xtype: 'fieldcontainer',
				width: '100%',
				fieldLabel: 'DATE / TIME IN',
				layout: 'hbox',
				afterLabelTextTpl: required,
				items: [{
					xtype: 'datefield',
					name: 'baking_date_in',
					format: 'Y-m-d',
					allowBlank: false,
					editable: false
				},{xtype:'tbspacer',width:10},
				{
					xtype: 'timefield',
					name: 'baking_time_in',
					format: 'H:i',
					allowBlank: false
				}]
			},{
				fieldLabel: 'NIK OUT',
				name: 'baking_nik_out',
				listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			},{
				xtype: 'fieldcontainer',
				width: '100%',
				fieldLabel: 'DATE / TIME OUT',
				layout: {
					type: 'hbox',
					pack: 'start',
					align: 'stretch'
				},
				items: [{
					xtype: 'datefield',
					name: 'baking_date_out',
					format: 'Y-m-d',
					editable: false,
				},{xtype:'tbspacer',width:10},
				{
					xtype: 'timefield',
					name: 'baking_time_out',
					format: 'H:i'
				}]
			},{
				xtype: 'textareafield',
				name: 'baking_remark',
				fieldLabel: 'REMARK',
				grow: true,
				listeners: {
			    	change:function(field){
		                field.setValue(field.getValue().toUpperCase());
		            }
			    }
			}]
	    }],
	    dockedItems: [toolbar_baking]
	});

	var grid_baking = Ext.create('Ext.grid.Panel', {
	    store: baking_control,
	    selModel: Ext.create('Ext.selection.CheckboxModel'),
	    viewConfig: {
	    	enableTextSelection  : true
	    },
	    columns: [
	    	{ header: 'NO', xtype: 'rownumberer', width: 55, sortable: false },
	    	{ text: 'UNIQUE ID', dataIndex: 'unid', hidden: true },
	    	{ text: 'ID', dataIndex: 'id', hidden: true },
	    	{ text: 'EXP. ID', dataIndex: 'expid', hidden: true },
	        { text: 'PART NUMBER', dataIndex: 'part_no', flex: 1 },
	        { text: 'MODEL', dataIndex: 'model', flex: 1 },
	        { text: 'PROCESS', dataIndex: 'process', flex: 1 },
	        { text: 'QTY', dataIndex: 'qty', flex: 1 },
	        { text: 'LOT NUMBER', dataIndex: 'lotno',flex: 1 },
	        { header: 'PRODUCT REQUIREMENT', columns: [
	        	{ text: 'TEMPERATURE', dataIndex: 'temperature',flex: 1 },
	        	{ text: 'DURATION', dataIndex: 'duration',flex: 1 },
			] },
			{ header: 'IN', columns: [
				{ text: 'DATE', dataIndex: 'datein',flex: 1 },
	        	{ text: 'PIC', dataIndex: 'nikin',flex: 1 },
			] },
			{ header: 'OUT', columns: [
				{ text: 'DATE', dataIndex: 'dateout',flex: 1 },
	        	{ text: 'PIC', dataIndex: 'nikout',flex: 1 },
			] },
	        { text: 'REMARK', dataIndex: 'remark',flex: 1 }
	    ],
	    bbar: {
	    	xtype: 'pagingtoolbar',
	    	displayInfo	: true,
	    	store: baking_control,
	    	items: ['->',{
	    		xtype: 'textfield',
	    		name: 'baking_fldsrc',
	    		width: 800,
	    		emptyText: 'Search part number in here...',
	    		listeners: {
	    			specialkey: function(field, e) {
					if (e.getKey() == e.ENTER) {
						baking_control.proxy.setExtraParam('baking_fldsrc',field.rawValue);
						baking_control.loadPage(1);
						// console.log(field.value);
					}
                }
	    		}
	    	}]
	    },
	    listeners: {
	    	select: function(grid, rowIndex, colIndex) {
                var rec = this.getSelectionModel().getSelection();
                var unid = rec[0].data.unid;
                var expid = rec[0].data.expid;
                var partno = rec[0].data.part_no;
                var model = rec[0].data.model;
                var process = rec[0].data.process;
                var qty = rec[0].data.qty;
                var lotno = rec[0].data.lotno;
                var temp = rec[0].data.temperature;
                var duration = rec[0].data.duration;
                var raw_datein = rec[0].data.datein;
                var nikin = rec[0].data.nikin;
                var raw_dateout = rec[0].data.dateout;
                var nikout = rec[0].data.nikout;
                var remark = rec[0].data.remark;

                var txt_unid = Ext.ComponentQuery.query('hiddenfield[name=baking_unid]')[0];
                var txt_expid = Ext.ComponentQuery.query('hiddenfield[name=baking_expid]')[0];

                var txt_partno = Ext.ComponentQuery.query('textfield[name=baking_partno]')[0];
                var txt_model = Ext.ComponentQuery.query('textfield[name=baking_model]')[0];
                var txt_process = Ext.ComponentQuery.query('textfield[name=baking_process]')[0];
                var txt_qty = Ext.ComponentQuery.query('textfield[name=baking_qty]')[0];
                var txt_lotno = Ext.ComponentQuery.query('textfield[name=baking_lotno]')[0];
                var txt_temp = Ext.ComponentQuery.query('textfield[name=baking_temp]')[0];
                var txt_duration = Ext.ComponentQuery.query('textfield[name=baking_duration]')[0];

                var txt_datein = Ext.ComponentQuery.query('datefield[name=baking_date_in]')[0];
                var txt_timein = Ext.ComponentQuery.query('timefield[name=baking_time_in]')[0];
                var txt_nikin = Ext.ComponentQuery.query('textfield[name=baking_nik_in]')[0];

                var txt_dateout = Ext.ComponentQuery.query('datefield[name=baking_date_out]')[0];
                var txt_timeout = Ext.ComponentQuery.query('timefield[name=baking_time_out]')[0];
                var txt_nikout = Ext.ComponentQuery.query('textfield[name=baking_nik_out]')[0];

                var txt_remark = Ext.ComponentQuery.query('textfield[name=baking_remark]')[0];

                // console.log(datein.substr(0,10));
                // console.log(datein.substr(11,5));

                if (raw_datein == null) {
                	var datein = raw_datein;
                	var timein = raw_datein;
                } else {
                	var datein = raw_datein.substr(0,10);
                	var timein = raw_datein.substr(11,5);
                }

                if (raw_dateout == null) {
                	var dateout = raw_dateout;
                	var timeout = raw_dateout;
                } else {
                	var dateout = raw_dateout.substr(0,10);
                	var timeout = raw_dateout.substr(11,5);
                }

                txt_unid.setValue(unid);
                txt_expid.setValue(expid);
                txt_partno.setValue(partno);
                txt_model.setValue(model);
                txt_process.setValue(process);
                txt_qty.setValue(qty);
                txt_lotno.setValue(lotno);
                txt_temp.setValue(temp);
                txt_duration.setValue(duration);
                txt_datein.setValue(datein);
                txt_timein.setValue(timein);
                txt_nikin.setValue(nikin);
                txt_dateout.setValue(dateout);
                txt_timeout.setValue(timeout);
                txt_nikout.setValue(nikout);
                txt_remark.setValue(remark);
            }
	    }
	});

	var panel_baking = Ext.create('Ext.panel.Panel',{
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
	        	background: 'url("resources/bg-image.jpg") no-repeat center top',
	        	backgroundSize: 'cover'
	        },
	        items: form_baking
	    }, {
	        region: 'center',
	        layout: 'fit',
	        items: grid_baking
	    }]
	});
</script>