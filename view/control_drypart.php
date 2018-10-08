<?php 
	include_once 'control_drpbaking.php';
	include_once 'control_detail.php';
?>
<script type="text/javascript">
	
	var toolbar_drypart = Ext.create('Ext.toolbar.Toolbar',{
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
				// Ext.ComponentQuery.query('textfield[name=nik]')[0].setEditable(true);
				
			}
		}]
	});
	
	var form_drypart = Ext.create('Ext.form.Panel',{
		// title: 'FORM CONTROL DRY PART',
		// header: { titleAlign: 'center' },
		name: 'form_drypart',
		layout: 'anchor',
		bodyStyle: {
        	background: 'rgba(255, 255, 255, 0)'
        },
		width: 400,
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
			name: 'drynik',
			value: '37297',
			selectOnFocus: true,
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
						Ext.ComponentQuery.query('textfield[name=drypartno]')[0].focus(true,1);
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
			emptyText: 'SCAN PART NUMBER',
			name: 'drypartno',
			disabled: true
		}, {
			emptyText: 'CHECK LIFETIME PART',
			name: 'drycheck'
		}],
		dockedItems: [toolbar_drypart]
	});

	var grid_drypart = Ext.create('Ext.grid.Panel', {
		selModel: Ext.create('Ext.selection.CheckboxModel'),
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

	var panel_drypart = Ext.create('Ext.panel.Panel',{
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
	        	background: 'url("resources/bg-image-2.jpg") no-repeat center bottom',
	        	backgroundSize: 'cover'
	        },
	        items: form_drypart
	    }, {
	        region: 'center', // GRID SIDE
	        layout: 'fit',
	        items: grid_drypart
	    }]
	});

	var tab_drypart = Ext.create('Ext.tab.Panel',{
		// activeTab: 1,
		plain: true,
		tabePosition: 'top',
		tabBar: {
			flex: 1,
			layout: {
				pack: 'center',
				align: 'stretch',
				// overflowHandler: 'none'
			}
		},
		defaults: {
			bodyStyle: 'background: #ADD2ED',
		},
		items: [{
			title: 'SETTING PARTS',
			layout: 'fit',
			items: panel_detail
		}, {
			title: 'CONTROL EXPIRED PART',
			layout: 'fit',
			items: panel_drypart
		}, {
        	title: 'BAKING DRY PART',
        	layout: 'fit',
        	items: panel_bakingdry
        }]
	});

</script>
<style type="text/css">
	.settings {
		height: 64px;
	}
</style>