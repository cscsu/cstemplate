$(function() {

		$( "#itemdate" ).datepicker();
	
});
$(function() {

		$( "#assignmentdate" ).datepicker();
	
});
	$(document).ready(function() {

            $('#edit_addmaterial').click(function() {
                var num     = new Number($("div[id^='edit_material']").length - 1);
                var newNum  = new Number(num + 100000);
		var xNum  = new Number(num + 1);
                var newElem = $('#edit_material' + num).clone().attr('id', 'edit_material' + xNum);
                newElem.find("input[name^='materials_title']").attr('name', 'materials_titleadd[' + newNum + ']').attr('value','');
 		newElem.find("input[name^='materials_resource']").attr('name', 'materials_resourceadd[' + newNum + ']').attr('value','');
                $('#edit_material' + num).after(newElem);
 
            });
 
            $('#edit_addreading').click(function() {
                var num     = new Number($("div[id^='edit_reading']").length - 1);
                var newNum  = new Number(num + 100000);
		var xNum  = new Number(num + 1);
                var newElem = $('#edit_reading' + num).clone().attr('id', 'edit_reading' + xNum);
                newElem.find("input[name^='readings_title']").attr('name', 'readings_titleadd[' + newNum + ']').attr('value','');
 		newElem.find("input[name^='readings_resource']").attr('name', 'readings_resourceadd[' + newNum + ']').attr('value','');
                $('#edit_reading' + num).after(newElem);
 
            });


            $("a[id^='add_widget_']").click(function() {
                var widget = this.id.substr(11);
		$('#pagecontent').focus();
		var sel = $('#pagecontent').getSelection();
		$('#pagecontent').insertText("<!--[widget name="+widget+"]-->", sel.start, true);
		
            });



            $("a[id^='add_add']").click(function() {
		if (this.id.substr(0, this.id.lastIndexOf('_')) == 'add')
		{
			addSet(this.id);
		}
		else
		{
			var oid = this.id.substr(new Number(this.id.lastIndexOf('_') + 1));
 			addSubset(this.id, oid);
		}

            });

        });

	function hideElements()
	{
		$("div[id^='add_']").children().not("a[id^='show_']").css("display","none");
		$("div[id^='add_']").find(".field").find("input[name*='_name']").parent().css("display","block");
		$("div[id^='add_']").find(".field").find("input[name*='_number']").parent().css("display","block");
		$("div[id^='add_']").find(".field").find("select[name*='_name']").parent().css("display","block");
		$("div[id^='add_']").find(".field").find("select[name*='_number']").parent().css("display","block");
	}



	function show(tag,id,subid)
	{
		var sub = '';
		if (subid != null)
		{
			sub = '_' + subid;
		}
		$("div[id='"+ tag + id + sub + "']").children("a[id^='show_']").attr('id', function() {
			return 'hide' + this.id.substr(this.id.indexOf('_')); 
		}
		).attr('onclick', function(index, attr) {
			return 'hide' + attr.substr(attr.indexOf('(')); 
		}
		).find("img").attr('src', function (index,attr)
		{
			return attr.substr(0,attr.lastIndexOf('/')+1) + 'hide.png'; 
		}).attr('alt','Hide');
		$("div[id='"+ tag + id + sub + "']").children().css("display","");
		$("div[id='"+ tag + id + sub + "']").find("input[name*='_name']").parent().css("display","block");
		$("div[id='"+ tag + id + sub + "']").find("input[name*='_number']").parent().css("display","block");
		$("div[id='"+ tag + id + sub + "']").find("input[name*='_day']").parent().css("display","block");
		$("div[id='"+ tag + id + sub + "']").find("select[name*='_name']").parent().css("display","block");
		$("div[id='"+ tag + id + sub + "']").find("select[name*='_number']").parent().css("display","block");
		$("div[id='"+ tag + id + sub + "']").find("select[name*='_day']").parent().css("display","block");

			

			
	}

	function hide(tag,id,subid)
	{
		var sub = '';
		if (subid != null)
		{
			sub = '_' + subid;
		}
		$("div[id='"+ tag + id + sub + "']").children("a[id^='hide_']").attr('id', function() {
			return 'show' + this.id.substr(this.id.indexOf('_')); 
		}
		).attr('onclick', function(index, attr) {
			return 'show' + attr.substr(attr.indexOf('(')); 
		}
		).find("img").attr('src', function (index,attr)
		{
			return attr.substr(0,attr.lastIndexOf('/')+1) + 'show.png'; 
		}).attr('alt','Show');
		$("div[id='"+ tag + id + sub + "']").children().not("a[id^='show_']").css("display","none");
		$("div[id='"+ tag + id + sub + "']").find(".field").find("input[name*='_name']").parent().css("display","block");
		$("div[id='"+ tag + id + sub + "']").find(".field").find("input[name*='_number']").parent().css("display","block");
		$("div[id='"+ tag + id + sub + "']").find(".field").find("select[name*='_name']").parent().css("display","block");
		$("div[id='"+ tag + id + sub + "']").find(".field").find("select[name*='_number']").parent().css("display","block");
		$("div[id='"+ tag + id + sub + "']").find(".field").find("select[name*='_day']").parent().css("display","block");

			

			
	}


	function remove(tag,id,subid)
	{
		var sub = '';
		var xsub = '';
		if (subid != null)
		{
			sub = '_' + subid;
			xsub = id;
		}

		var num = new Number($("div[id^='" + tag + xsub + "']").length - 1);
		if (num == 0)
		{
			alert("The last element cannot be removed");
			return;
		}
		var answer = confirm("Are you sure you want to delete this element, this cannot be undone");
		if (answer)
		{

			$("div[id='"+ tag + id + sub + "']").remove();
		}
			

			
	}


	function addSubset(setid, oid)
	{
		
		var settitle = setid.substr(setid.indexOf('add_add')+7);
		settitle = settitle.substr(0, settitle.indexOf('_'));
		var num     = new Number($("div[id^='add_" + settitle + '_' + oid + "']").length - 1);
                var newNum  = new Number(num + 1);
                var newElem = $('#add_' + settitle + '_' + oid + '_' + num).clone().attr('id', 'add_' + settitle + '_' + oid + '_' + newNum);
		newElem.find("input[name*='_" + settitle + "']").attr('name', function (index, attr) { return updateOutterSet(index,attr,newNum); }).attr('value','');

                newElem.children("a[id^='show']").attr('id', function(index, attr) {
			var t = attr.substr(5);
			var fld = t.substr(0, t.indexOf('_'));
			return 'hide_' + fld + '_' + oid + '_' + newNum ; 
		}
		).attr('onclick', function(index, attr) {
			var fld = attr.substr(0,attr.indexOf(','));
			return fld + ',' + oid + ',' + newNum + ')'; 
		}
		);
		newElem.children("a[id^='hide']").attr('id', function(index, attr) {
			var t = attr.substr(5);
			var fld = t.substr(0, t.indexOf('_'));
			return 'hide_' + fld + '_' + oid + '_' + newNum ; 
		}
		).attr('onclick', function(index, attr) {
			var fld = attr.substr(0, attr.indexOf(','));
			return fld + ',' + oid + ',' + newNum  + ')'; 
		}
		);
		newElem.children("a[id^='remove']").attr('id', function(index, attr) {
			var t = attr.substr(7);
			var fld = t.substr(0, t.indexOf('_'));
			return 'remove_' + fld + '_' + oid + '_' + newNum ; 
		}
		).attr('onclick', function(index, attr) {
			var fld = attr.substr(0, attr.indexOf(','));
			return fld + ',' + oid + ',' + newNum  + ')'; 
		}
		);

		newElem.find("select[name*='_" + settitle + "']").attr('name', function (index, attr) { return updateOutterSet(index,attr,newNum); }).attr('value','');

		$('#add_' + settitle + '_' + oid + '_' + num).after(newElem);
	
	}

	function addSet(setid)
	{

		var settitle = setid.substr(setid.indexOf('add_add')+7);
		var num     = new Number($("div[id^='add_" + settitle + "']").length - 1);
                var newNum  = new Number(num + 1);
                var newElem = $('#add_' + settitle + num).clone().attr('id', 'add_' + settitle + newNum);


                newElem.children("a[id^='show']").attr('id', function(index, attr) {
			var t = attr.substr(5);
			var fld = t.substr(0, t.indexOf('_'));
			return 'show_' + fld + '_' + newNum ; 
		}
		).attr('onclick', function(index, attr) {
			var fld = attr.substr(0, attr.indexOf(','));
			return fld + ',' + newNum + ')'; 
		}
		);
		newElem.children("a[id^='hide']").attr('id', function(index, attr) {
			var t = attr.substr(5);
			var fld = t.substr(0, t.indexOf('_'));
			return 'hide_' + fld + '_' + newNum ; 
		}
		).attr('onclick', function(index, attr) {
			var fld = attr.substr(0, attr.indexOf(','));
			return fld + ',' + newNum  + ')'; 
		}
		);
		newElem.children("a[id^='remove']").attr('id', function(index, attr) {
			var t = attr.substr(7);
			var fld = t.substr(0, t.indexOf('_'));
			return 'remove_' + fld + '_' + newNum ; 
		}
		).attr('onclick', function(index, attr) {
			var fld = attr.substr(0, attr.indexOf(','));
			return fld + ',' + newNum  + ')'; 
		}
		);


		newElem.find("div[id^='add_']").children("a[id^='show']").attr('id', function(index, attr) {
			var t = attr.substr(5);
			var fld = t.substr(0,t.indexOf('_'));
			return 'show_' + fld + '_' + newNum + '_' + 0; 
		}
		).attr('onclick', function(index, attr) {
			var fld = attr.substr(0, attr.indexOf(','));
			return fld + ',' + newNum + ',0)'; 
		}
		);
		newElem.find("div[id^='add_']").children("a[id^='hide']").attr('id', function(index, attr) {
			var t = attr.substr(5);
			var fld = t.substr(0,t.indexOf('_'));
			return 'hide_' + fld + '_' + newNum + '_' + 0;  
		}
		).attr('onclick', function(index, attr) {
			var fld = attr.substr(0, attr.indexOf(','));
			return fld + ',' + newNum  + ',0)'; 
		}
		);
		newElem.find("div[id^='add_']").children("a[id^='remove']").attr('id', function(index, attr) {
			var t = attr.substr(7);
			var fld = t.substr(0,t.indexOf('_'));
			return 'remove_' + fld + '_' + newNum + '_' + 0;  
		}
		).attr('onclick', function(index, attr) {
			var fld = attr.substr(0, attr.indexOf(','));
			return fld + ',' + newNum  + ',0)'; 
		}
		);

		newElem.find("div[id^='add_']").not("div[id$='0']").remove();
		newElem.find("div[id^='add_']").attr('id',function (index, attr) { return updateSubset(index,attr,newNum); });
                newElem.find("input[name^='"+ settitle +"']").attr('name', function (index, attr) { return updateInnerSet(index,attr,newNum); }).attr('value','');
		newElem.find("select[name^='"+ settitle +"']").attr('name', function (index, attr) { return updateInnerSet(index,attr,newNum); }).attr('value','');

		newElem.find("a[id^='add_add']").attr('id',function (index, attr) { return updateSubsetLinks(index,attr,newNum); }).click(function() {
 		addSubset(this.id,newNum);
            	});


                $('#add_' + settitle + num).after(newElem);
	}
	function updateSubset(index,attr,newNum)
	{

		var tName = attr.substring(4);
		var xName = tName.substring(0, tName.indexOf('_'));
		return 'add_' + xName + '_' + newNum + '_0';
	}
	function updateSubsetLinks(index,attr,newNum)
	{

		var tName = attr.substring(7);
		var xName = tName.substring(0, tName.indexOf('_'));
		return 'add_add' + xName + '_' + newNum;
	}

	function updateInnerSet(index,attr,newNum)
	{
		

		var xName = attr.substring(0,attr.indexOf("["));
		if (attr.indexOf("]")+1 != attr.length)
		{
			var tField = attr.substring(attr.indexOf("]")+1);
			return xName + '[' + newNum + ']' + tField;
		}
		else
		{
			return xName + '[' + newNum + ']';
		}
	}

	function updateOutterSet(index,attr,newNum)
	{
		
		var xName = attr.substring(0,attr.lastIndexOf("_"));
		var xField = attr.substring(attr.lastIndexOf("_"));
		xField = xField.substring(0,xField.lastIndexOf('['));
		return xName + xField + '[' + newNum + ']';
	}