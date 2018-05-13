/**
 * Created by Alan on 2017/1/14.
 */
$(function(){

    //上传商店图片开始
    //初始化fileinput
    var FileInput = function () {
        var oFile = new Object();
        //初始化fileinput控件（第一次初始化）
        oFile.Init = function(ctrlName, uploadUrl) {
            var control = $('#' + ctrlName);
            //初始化上传控件的样式
            control.fileinput({
                language: 'zh', //设置语言
                uploadUrl: uploadUrl, //上传的地址
                allowedFileExtensions: ['jpg','jpeg', 'gif', 'png'],//接收的文件后缀
                showUpload: true, //是否显示上传按钮
                showCaption: false,//是否显示标题
                browseClass: "btn btn-primary", //按钮样式
                //dropZoneEnabled: false,//是否显示拖拽区域
                //minImageWidth: 50, //图片的最小宽度
                //minImageHeight: 50,//图片的最小高度
                //maxImageWidth: 1000,//图片的最大宽度
                //maxImageHeight: 1000,//图片的最大高度
                //maxFileSize: 0,//单位为kb，如果为0表示不限制文件大小
                //minFileCount: 0,
                maxFileCount: 1, //表示允许同时上传的最大文件个数
                enctype: 'multipart/form-data',
                validateInitialCount:true,
                previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
                msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
            });
            //导入文件上传完成之后的事件
            $("#shop_photo").on("fileuploaded", function (event, data, previewId, index) {
                //alert(JSON.stringify(data.response));
                var myResponse = data.response;
                //alert(myResponse.content);
                if(myResponse.status == 1){
                    //alert('inhere');
                    $('#shopForm').append('<input type="hidden" name="shop_photo_url" value="'+myResponse.content+'"/>');
                }else{
                    alert(myResponse.content);
                }
            });
        }
        return oFile;
    };

    //初始化fileinput
    var oFileInput = new FileInput();
    oFileInput.Init("shop_photo", url+"/uploadShopPic");
    //上传商店图片结束

    $('#shop_name').blur(function(){
        if($(this).val() != ''){
            //alert('in here');
            $.ajax({
                url: url+"/isShopExists",    //请求的url地址
                dataType: "json",   //返回格式为json
                async: true, //请求是否异步，默认为异步，这也是ajax重要特性
                data: { "shopName": $(this).val() },    //参数值
                type: "GET",   //请求方式
                success: function(data) {
                    //请求成功时处理
//                        alert(data.msg);
                    if(data.msg == true){
                        $('#shopForm').attr("onSubmit","return false");
                        $('#shopErrMsg').text('商店已存在！');
                    } else {
                        $('#shopForm').attr("onSubmit","return true");
                        $('#shopErrMsg').text('');
                    }
                },
                error: function(data) {
                    //请求出错处理
                    alert('请求发生错误！'+data.status);
                }
            });
        }
    });

    //上传轮播图片开始
    var FileInputCarousel = function () {
        var oFile = new Object();
        //初始化fileinput控件（第一次初始化）
        oFile.Init = function(ctrlName, uploadUrl) {
            var control = $('#' + ctrlName);
            //初始化上传控件的样式
            control.fileinput({
                language: 'zh', //设置语言
                uploadUrl: uploadUrl, //上传的地址
                allowedFileExtensions: ['jpg','jpeg', 'gif', 'png'],//接收的文件后缀
                showUpload: true, //是否显示上传按钮
                showCaption: false,//是否显示标题
                browseClass: "btn btn-primary", //按钮样式
                //dropZoneEnabled: false,//是否显示拖拽区域
                //minImageWidth: 50, //图片的最小宽度
                //minImageHeight: 50,//图片的最小高度
                //maxImageWidth: 1000,//图片的最大宽度
                //maxImageHeight: 1000,//图片的最大高度
                //maxFileSize: 0,//单位为kb，如果为0表示不限制文件大小
                //minFileCount: 0,
                maxFileCount: 3, //表示允许同时上传的最大文件个数
                enctype: 'multipart/form-data',
                validateInitialCount:true,
                previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
                msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
            });
            //导入文件上传完成之后的事件
            $("#carousel_photo").on("fileuploaded", function (event, data, previewId, index) {
                //alert(JSON.stringify(data.response));
                var myResponse = data.response;
                //alert(myResponse.content);
                if(myResponse.status == 1){
                    //alert('inhere');
                    $('#shopForm').append(
                        '<input type="hidden" name="carousel_photo_url_ids[]" value="'+myResponse.content+'"/>');
                }else{
                    alert(myResponse.content);
                }
            });
        }
        return oFile;
    };

    //初始化fileinput
    var carouselFileInput = new FileInputCarousel();
    carouselFileInput.Init("carousel_photo", url+"/uploadMany");
    //上传轮播图片结束

    //上传描述图片开始
    var FileInputDesc = function () {
        var oFile = new Object();
        //初始化fileinput控件（第一次初始化）
        oFile.Init = function(ctrlName, uploadUrl) {
            var control = $('#' + ctrlName);
            //初始化上传控件的样式
            control.fileinput({
                language: 'zh', //设置语言
                uploadUrl: uploadUrl, //上传的地址
                allowedFileExtensions: ['jpg','jpeg', 'gif', 'png'],//接收的文件后缀
                showUpload: true, //是否显示上传按钮
                showCaption: false,//是否显示标题
                browseClass: "btn btn-primary", //按钮样式
                //dropZoneEnabled: false,//是否显示拖拽区域
                //minImageWidth: 50, //图片的最小宽度
                //minImageHeight: 50,//图片的最小高度
                //maxImageWidth: 1000,//图片的最大宽度
                //maxImageHeight: 1000,//图片的最大高度
                //maxFileSize: 0,//单位为kb，如果为0表示不限制文件大小
                //minFileCount: 0,
                maxFileCount: 10, //表示允许同时上传的最大文件个数
                enctype: 'multipart/form-data',
                validateInitialCount:true,
                previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
                msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
            });
            //导入文件上传完成之后的事件
            $("#desc_photo").on("fileuploaded", function (event, data, previewId, index) {
                //alert(JSON.stringify(data.response));
                var myResponse = data.response;
                //alert(myResponse.content);
                if(myResponse.status == 1){
                    //alert('inhere');
                    $('#shopForm').append(
                        '<input type="hidden" name="desc_photo_url_ids[]" value="'+myResponse.content+'"/>');
                }else{
                    alert(myResponse.content);
                }
            });
        }
        return oFile;
    };

    //初始化fileinput
    var desclFileInput = new FileInputDesc();
    desclFileInput.Init("desc_photo", url+"/uploadMany");
    //上传描述图片结束
});