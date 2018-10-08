<script type="text/javascript">

	var form_bakingdry = Ext.create('Ext.panel.Panel',{
		// title: 'FORM BAKING PART',
		// header: { titleAlign: 'center' },
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
	    	fieldLabel: 'Part Number'
	    }]
	});

	var grid_bakingdry = Ext.create('Ext.grid.Panel', {
		selModel: Ext.create('Ext.selection.CheckboxModel'),
	    viewConfig: {
	    	enableTextSelection  : true
	    },
	    width: 400,
	    columns: [
	    	{ header: 'NO', xtype: 'rownumberer', width: 55, sortable: false },
	    	{ text: 'EXP ID', dataIndex: '' },
	    	{ text: 'ID', dataIndex: '' },
	    	{ text: 'PART NUMBER', dataIndex: 'part_no', flex: 1 },
	    	{ text: 'SCAN IN', dataIndex: '', flex: 1 },
	    	{ text: 'SCAN OUT', dataIndex: '', flex: 1 }
	    ]
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
				background: 'url("resources/bg-image-2.jpg") no-repeat center bottom',
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