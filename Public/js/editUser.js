/**
 * Created by Alan on 2017/1/15.
 */
$(function(){
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
            $("#user_photo").on("fileuploaded", function (event, data, previewId, index) {
                //alert(JSON.stringify(data.response));
                var myResponse = data.response;
                //alert(myResponse.content);
                if(myResponse.status == 1){
                    $('#userForm').append('<input type="hidden" name="user_photo_url" value="'+myResponse.photo_url_id+'"/>');
                    $('#user_photo_url').attr('src',myResponse.content);
                    $("#myModal").modal("hide");
                }else{
                    alert(myResponse.content);
                }
            });
        }
        return oFile;
    };

    //初始化fileinput
    var oFileInput = new FileInput();
    oFileInput.Init("user_photo", url+"/uploadUserPic");
});