<?php
	$m = new MongoClient("192.168.1.100");
   	$db = $m->TestForHang;
   	$collection = $db->createCollection("user2");
	$cols = getTableCols($collection);  
	
	/**
	 * 获取表中的所有字段
	 * Enter description here ...
	 * @param $collection
	 */
	function getTableCols($collection){
		$cursor = $collection->find();
		foreach ($cursor as $document) {
			$keys = array_keys($document);
			break;
		}
		return $keys;
	}
?>
<script src="http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript" src="js/editTable.js"></script>
<script src="http://apps.bdimg.com/libs/angular.js/1.2.15/angular.min.js"></script>
<script type="text/javascript"> 
	var new_id = 1; //当用户新增数据时，new_id自动增长
	var oldText = '';
	var newModel = [];  //临时保存用户bianj
	var cols = [];
	$(document).ready(function(){
		$("#bt_update").click(function(){
			var id = $(this).attr("id");
			updateToServer();
		});
		$("#bt_test").click(function(){
			var id = $(this).attr("id");
			test();
		});
		$("#bt_save").click(function(){
			var id = $(this).attr("id");
			save();
		});
		$("#bt_modify").click(function(){
			var id = $(this).attr("id");
			modifyRowsToServer();
		});
		$("#bt_delete").click(function(){
			var id = $(this).attr("id");
			deleteRows();
		});
		$("#bt_new").click(function(){
			str = "<tr id=tr_new_"+new_id+" class='tr_xxx'>";
			str +="<td class='box'><input type='checkbox' id=new_"+new_id+" /></td>";
			for(x=1; x<cols.length; x++){
				str += "<td class='"+cols[x]+"'></td>";
			}
			str += "</tr>";
			$("#table1").append(str);
			new_id++;
		});
		
		//单击这些td时，创建文本框 
		$(document).on("click","td",function(){
			//创建文本框对象 
			var inputobj = $("<input type='text'>"); 
			//获取当前点击的单元格对象 
			var tdobj = $(this); 
			//获取单元格中的文本 
			var text = tdobj.html(); 
			oldText = text;

			//如果单元表格里面是checkbox,要返回true即可
			var checkbox = tdobj.children("input")[0];
			if($(checkbox).attr("type") == "checkbox"){
				return true;
			}
			
			//如果当前单元格中有文本框，就直接跳出方法 
			//注意：一定要在插入文本框前进行判断 
			if(tdobj.children("input").length>0){ 
				return false; 
			} 
			
			//清空单元格的文本 
			tdobj.html(""); 
			inputobj.css("border","0") 
				.css("font-size",tdobj.css("font-size"))
				.css("font-family",tdobj.css("font-family")) 
				.css("background-color",tdobj.css("background-color")) 
				.css("color","#C75F3E") 
				.width(tdobj.width()) 
				.val(text) 
				.appendTo(tdobj); 
			inputobj.get(0).select(); 
			//阻止文本框的点击事件 
			inputobj.click(function(){ 
				return false;
			}); 

			inputobj.blur(function(){ 
				var inputtext = $(this).val(); 
				var tdclass  = $(tdobj).attr("class");
				var parent_id = $(tdobj).parent().attr("id");
				tdobj.html(inputtext); 
				if(parent_id.indexOf("tr_new") == 0){
					return;
				}
				updateModifyTd(parent_id, tdclass, inputtext);
			});
				 
			//处理文本框上回车和esc按键的操作 
			//jQuery中某个事件方法的function可以定义一个event参数，jQuery会屏蔽浏览器的差异，传递给我们一个可用的event对象 
			inputobj.keyup(function(event){ 
				//获取当前按键的键值 
				//jQuery的event对象上有一个which的属性可以获得键盘按键的键值 
				var keycode = event.which; 
				//处理回车的情况 
				if(keycode==13){ 
					//获取当前文本框的内容 
					var inputtext = $(this).val(); 
					//将td的内容修改成文本框中的内容 
					tdobj.html(inputtext); 
				} 
				//处理esc的情况 
					if(keycode == 27){
					//将td中的内容还原成text 
					tdobj.html(text); 
				} 
			});
		});
	}); 

	
	function test(){
		alert("test");
		var trs = $(".tr_xxx");
		//alert("trs : "+trs.length);
		for(var x=0; x<trs.length; x++){
			var tds = $(trs[0]).children();
			for(var y=1; y<tds.length; y++) //从一开始，忽略checkbox单元格
			{
				$(tds[y]).prop("class",cols[y-1]);
			}
			break;  //由于是angualer 进行绑定了我的Class，只需要将更新一行的class，其他的全部自动更新，这背后使用的是什么原理？ 
		}

		for(x=0; x<trs.length; x++){
			var tds = $(trs[0]).children();
			for(y=1; y<tds.length; y++) //从一开始，忽略checkbox单元格
			{
				alert("tds : "+$(tds[y]).attr("class"));
			}
		}
	}
	
	/**
	*	从网上下载的将单词的首字母转大写的功能
	*   	测试可以使用
	*/
	function ReplaceFirstUper(str)
	{   
		str = str.toLowerCase();   
		return str.replace(/\b(\w)|\s(\w)/g, function(m){
			return m.toUpperCase();
		});  
	}

	//将首字母转为小写
	function toLocaleLowerCase(str){
		str.trim();
		c = str.substring(0,1);
		c = c.toLocaleLowerCase();
		str = str.substring(1,str.length);
		str = c+str;
		return str;
	}

	/**
	* 将用户编辑过的数据全部更新到服务端
	*	其实是将update和modify两个功能合并 
	*/
	function save(){
		
		updateToServer();
		modifyRowsToServer();
	}

	/**
	*	获取用户的修改过的所有字符串
	*/
	function getModifyRowsJsonStr(){ 
		var json = '[';
		for(var x=0; x<newModel.length; x++){
			json += "{";
			for(var y=0; y<newModel[x].length; y++){
				//alert("info : "+newModel[x][y]);
				val = newModel[x][y];
			    json +="\""+val+"\":\""+newModel[x][val]+"\"";
			    if(y != newModel[x].length-1){
					json += ",";
				}
			}
			json += "}";
			if(x != newModel.length-1)
				json += ',';
		}
		json += ']';
		//alert("json : "+json);
		return json;
	}
	
	/**
	*
	* 用户在编辑一个单元格后，将文本保存下来到一个临时的区域
	* 以便将来更新到服务器做出准备
	*/
	function updateModifyTd(parent_id, tdClass, inputtext){
		tdClass = tdClass.substring(0,tdClass.indexOf(" ng-binding"));  //去掉多余的后缀（ ng-binding)

		for(x=0; x<model.length; x++){
			if(model[x]['id'] == parent_id){
				oldText = model[x][tdClass];
				break;
			}
		}
		if(oldText == inputtext){
			return ;
		}else{
			var num = newModel.length;
			var flag = false;
			for(x=0; x<num; x++){
				if(newModel[x]['id'] == parent_id){
					//alert("yes");
					flag = true;
					newModel[x][tdClass] = inputtext;
					newModel[x][newModel[x].length] = tdClass;
					break;
				}
			}

			if(!flag){  //纪录的数据第一次修改
				newModel[newModel.length] = new Array();
				newModel[newModel.length-1][cols[0]] = parent_id;
				newModel[newModel.length-1][0] = cols[0];
				newModel[newModel.length-1][tdClass] = inputtext;
				var len = newModel[newModel.length-1].length;
				newModel[newModel.length-1][len] = tdClass;
				//alert("tdClass : "+tdClass);
				//alert("text : "+newModel[newModel.length-1][tdClass]);
			}
		}
	}

	//打印对象 
	function dump_obj(myObject){ 
		  var s = ""; 
		  for (var property in myObject) { 
		   s = s + "\n "+property +": " + myObject[property] ; 
		  } 
		  alert(s); 
	} 
			
	/**
	* 	测试使用jquery将json字符串转换成json对象数组 
	*/
	function ConvertToJsonForJq() {  
	    //var testJson = '{ "name": "小强", "age": 16 }';  
	    //'{ name: "小强", age: 16 }' (name 没有使用双引号包裹)
	    //"{ 'name': "小强", 'age': 16 }"(name使用单引号)
	    
	    var testJson = '[{"name": "小强", "age": 16 },{"name": "张三", "age": 21}]';   
	    testJson = $.parseJSON(testJson);  
	    for(var x=0; x<testJson.length; x++){
			alert("name : "+testJson[x]["name"]);
		}
	};

	function modifyRowsToServer(){
		/*
		*	1、获取用户选择要更新的纪录，并将其封装成json字符串
		*	2、使用jquery ajax 同步的方式将json字符串提交到Server
		* 	3、处理后事
		*/
		var json = getModifyRowsJsonStr();
		alert("your json : "+json);
		$.ajax({
			url:"/UserSystem2/user/json/json2.php",//要请求的servlet
			data:{json:json,method:"update"},//给服务器的参数
			type:"POST",
			async:false,//是否异步请求，如果是异步，那么不会等服务器返回，我们这个函数就向下运行了。
			cache:false,
			success:function(result){
				alert("result : "+result);
				newModel = [];
				return true;
			}
		});
	}

	/**
	* 将用户新插入的数据保存到服务器上
	*/
	function updateToServer(){
		/*
		*	1、获取用户插入的新行，并获取数据；
		*	2、判断用户的数据输入的数据合法性；（只有一个字段不为空都是合法，全部为空则不提交）
		*	3、将用户的数据拼装成Json字符串，通过Jquery ajax的方式提交到服务端
		*	4、更新本地的UI属性
		*/
		var records = getNewRecords();
		var json = getNewRecordsJsonStr(records);
		alert("update json : "+json);
	    $.ajax({
			url:"/UserSystem2/user/json/json2.php",//要请求的servlet
			data:{json:json,method:"add"},//给服务器的参数
			type:"POST",
			async:false,//是否异步请求，如果是异步，那么不会等服务器返回，我们这个函数就向下运行了。
			cache:false,
			success:function(result){
				var ids = $.parseJSON(result);
				for(var x=0; x<records.length; x++){
					i = ids[records[x]['id']];
					if(id == null){
					} else if(typeof(id) == 'undefined'){
					}else{
						var myid = "#new_"+records[x]['id'];
						$(myid).attr("id","checkbox_"+i);   //要更改的id地方有两个
						$("#tr_new_"+records[x]['id']).attr("id",i);
					}
				}
				return true;
			}
		});
	}

	/**
	*	根据提供的新用户数据数组生成Json字符串 
	*/
	function getNewRecordsJsonStr(records){
		var json = '[';
		for(var x=0; x<records.length; x++){
			//alert("size : "+records.length);
			json += "{";
			
			json +="\""+cols[0]+"\":\""+records[x][cols[0]]+"\",";
			for(y=1; y<cols.length; y++){
				json +="\""+cols[y]+"\":\""+records[x][cols[y]]+"\"";
				if(y != cols.length-1){
					json += ",";
				}
			}
			
			json += "}";
			if(x != records.length-1)
				json += ',';
		}
		
		json += ']';
		return json;
	}

	/**
	*	获取用户新插入的数据
	*/
	function getNewRecords(){
		var records = new Array();
		var inputs = $("td input");
		for(var x=0; x<inputs.size(); x++){
			if($(inputs[x]).attr("type") == "checkbox"){
					id = $(inputs[x]).attr("id");
					if(id.indexOf('new_') == 0){
						id = id.substr(4);
						tds = $("#tr_new_"+id).children();
						record = new Array();
						for(y=0; y<tds.size(); y++){
							cla = $(tds[y]).attr("class");
							for(i=1; i<cols.length; i++){
								if(cla == cols[i]){
									val = $(tds[y]).text();
									record[cla] = val;
									break;
								}
							}
							/*
							if(cla=='name' || cla=='city' || cla=='country'){
								val = $(tds[y]).text();
								record[cla] = val;
							}
							*/
							//alert("info : "+record[cla]);
						}
						record[cols[0]] = id;
						records[records.length] = record;
					}
			}
		}
		return records;
	}
	
	/**
	* 删除用户选中的行
	*	1、获取用户选中checkboe对应的行数据ID
	*	2、将ID组装成json字符成用jquery的ajax发送到后台的php
	*/
	function deleteRows(){
		var ids = getDeleteId();
		if(!confirm("您确定要删除 "+ids.length+" 记录？")){
		   return false;
		}
		deleteLocalRecord();
		ids = getDeleteId(); //先删除本地的记录，在请求服务端删除服务器中的记录 
		var json = getDeleteJsonStr(ids);
		alert("delete json : "+json);
		
		$.ajax({
			url:"/UserSystem2/user/json/json2.php",//要请求的servlet
			data:{json:json,method:"delete"},//给服务器的参数
			type:"POST",
			async:false,//是否异步请求，如果是异步，那么不会等服务器返回，我们这个函数就向下运行了。
			cache:false,
			success:function(result){ //返回的结果是服务端成功删除的数据Id
				alert("init");
				alert("result : "+result);
				var ids = $.parseJSON(result);
				for(var x=0; x<ids.length; x++){
					alert("id : "+ids[x]);
					 $("#"+ids[x]).remove(); 
				}
				alert("end");
				
				return true;
			}
		});
	};

	/**
	* 删除本地用户选中的记录
	*/
	function deleteLocalRecord(){
		var inputs = $("td input");
		for(var x=0; x<inputs.size(); x++){
			if($(inputs[x]).attr("type") == "checkbox"){
				flag = $(inputs[x]).prop("checked"); //prop是jq1.6以后的函数，之前使用的是attr
				if(flag == true){
					id = $(inputs[x]).attr("id");
					var flag = id.indexOf("new_");
					id = id.substr(4);
					if(flag == 0){
						 $("#tr_new_"+id).remove(); 
					}
				}
			}
		}
	}
	
	/**
	*获取用户要删除数据的Id
	*/
	function getDeleteId(){
		var inputs = $("td input");
		var ids = new Array();
		alert("checksize : "+inputs.size());
		for(var x=0; x<inputs.size(); x++){
			if($(inputs[x]).attr("type") == "checkbox"){
				flag = $(inputs[x]).prop("checked"); //prop是jq1.6以后的函数，之前使用的是attr
				if(flag == true){
					id = $(inputs[x]).attr("id");
					alert("id : "+id);
					id = id.substr(9);  //去掉	checkbox_ 前缀
					ids[ids.length] =id;					
				}
			}
		}
		return ids;
	};

	/**
	*	生成删除的数据的Json字符串
	*		需要提供删除数据的Id数组
	*/
	function getDeleteJsonStr(ids){
		var str = '[';
		for(var x=0; x<ids.length; x++){
			str += "{";
			str += "\""+cols[0]+"\":\""+ids[x]+"\"";
			str += "}";
			if(x != ids.length-1){
				str += ",";
			}
		}
		str += ']';
		return str;
	}

	function del(){
		var users = new Array();
		var trs = $("tr");
		for(var x=0; x<trs.size(); x++){
			var obj = trs[x];
			var id = $(trs[x]).attr("id");
			if(typeof(id) != 'undefined'){
				if(id != null){
					name = $(obj).find(".name").text();
					city = $(obj).find(".city").text();
					country = $(obj).find(".country").text();
					user = new Array(name, city, country);
					users[users.length] = user;
				}
			}else{
				
			}
		}
		
		for(x=0; x<users.length; x++){
			user = users[x];
			alert("name : "+user[0]);
			alert("city : "+user[1]);
			("country : "+user[2]);
		}
	}
	
</script>

<script>
	var model; //服务端数据的Json数组对象， 用来缓存服务端中的数据
	function customersController($scope,$http) {
		$scope.firstName = "huang";
		$scope.lastName =  "hang";
	  	$http.get("http://anxing.wicp.net/UserSystem2/user/json/json2.php?method=getInfo")
	  		.success(function(response) {
		  		alert("response : "+response);
		  		model = dealResponse(response);
				cols = getCols(response);  		
				/*
				temp = cols[0];
				cols[0] = cols[cols.length-1];
				cols[cols.length-1] = temp;
				*/
		  		//model = response;
		 		$scope.users = response;
		 		$scope.cols = cols;
		});
	}

	/**
	*	
	*/
	function getCols(response){
		var model = [];
		for(var x=0; x<response.length; x++){
			num = 0;
			for(prop in response[x]){
				model[num++] = prop;				
			}
			break;
		}

		for(var x=0; x<model.length; x++){
			alert(x+" : "+model[x]);
		}

		return model;
	}


	/**
	*	奇怪的js Array，居然在二维数组不支持获取数组的大小
	*      呵呵
	*/
	function dealResponse(response){
		var model = [];
		size = 0; //不知点解js Array是如此的复杂，没有length和size方式获取长度，啊  
		for(var x=0; x<response.length; x++){
			row = new Array();
			model[x] = new Array();
			num = 0;
			for(prop in response[x]){
					row[num++] = prop;
					row[prop] = response[x][prop]; 
					//alert("row length : "+typeof(model));  居然打印是Object？
			 }
			 size ++;
			 model[x] = row;
		}
		return model;
	}
</script>

<style>
body {
	font-size: 18px;
}

table {
	color: #4F6B72;
	border: 1px solid #C1DAD7;
	border-collapse: collapse;
	width: 400px;
}

th {
	width: 50%;
	border: 1px solid #C1DAD7;
}

td {
	width: 50%;
	border: 1px solid #C1DAD7;
}
</style>

<div ng-app="" ng-controller="customersController">
	{{firstName}} | {{lastName}}
<div align="center" style="margin-top: 50px">
		<table id="table1">
			<thead>
				<tr>
					<th colspan="4">鼠标点击表格就可以编辑</th>
				</tr>
			</thead>
			<tbody>
				<tr id="table_head">				
					<th></th>
					<?php 
						for($x=0; $x<count($cols); $x++){
							if ($cols[$x] !== "_id"){
								echo "<th>$cols[$x]</th>"."\n";
							}else{
							}
						}
					?>
					<!-- 
					<th ng-repeat="col in cols">{{col}}</th>
						<th>Name</th>
						<th>City</th>
						<th>Country</th>
					-->
				</tr>
				<tr ng-repeat="user in users" id="{{user.id}}" class="tr_xxx">
					<td class="box" ><input type="checkbox" id="checkbox_{{user.id}}" /></td>
					<?php 
						for($x=0; $x<count($cols); $x++){
							if ($cols[$x] !== "_id"){
								echo "<td class='$cols[$x]'>{{user.$cols[$x]}}</td>"."\n";
							}else{
							}
						}
					?>
				</tr>
				
			</tbody>
		</table>
			<button style="font-size: 20px; color: green;" id="bt_new">新增</button>
			<button style="font-size: 20px; color: green;" id="bt_save">save</button>
			<button style="font-size: 20px; color: green;" id="bt_delete">delete</button>
			<button style="font-size: 20px; color: green;" id="bt_test">test</button>
			<!-- 
				<button style="font-size: 20px; color: green;" id="bt_update">update</button>
				<button style="font-size: 20px; color: green;" id="bt_modify">modify</button>
			 -->
	</div>
</div>
