/**
 * Created by Alan on 2016/11/10.
 */
$(function() {
    //表单验证数据
    $('#shopForm').validate({
        rules : {
            shop_name : {
                required : true,
                //remote : {
                //url : ThinkPHP['MODULE'] + '/AddData/checkShopName',
                //type : 'POST',
                // }
            },
            location : {
                required : true,
            },
            desc : {
                maxlength :255,
            },
            Alltheme : {
                required : true,
            },
            detail : {
                required : true,
                url : true,
            }


        },
        messages : {
            shop_name : {
                required : '请填写店铺名称',
                //remote : '店铺名称已存在！',
            },
            location : {
                required : ' 地址不得为空',
            },
            desc : {
                maxlength :'简介不得大于255位',
            },
            Alltheme : {
                required : '主题不得为空',
            },
            detail: {
                required : '店铺网址不得为空',
                url : '店铺网址格式不正确！',
            }
        }
    });

    $('#username').blur(function(){
//                alert('lost focus');
        //发送异步请求坚持用户名是否存在
        if($(this).val() != ''){
            $.ajax({
                url: url+"/isUserExists",    //请求的url地址
                dataType: "json",   //返回格式为json
                async: true, //请求是否异步，默认为异步，这也是ajax重要特性
                data: { "username": $(this).val() },    //参数值
                type: "GET",   //请求方式
                success: function(data) {
                    //请求成功时处理
//                        alert(data.msg);
                    if(data.msg == true){
                        $('#userForm').attr("onSubmit","return false");
                        $('#userErrMsg').text('账户已存在！');
                    } else {
                        $('#userForm').attr("onSubmit","return true");
                        $('#userErrMsg').text('');
                    }
                },
                error: function(data) {
                    //请求出错处理
                    alert('请求发生错误！'+data.status);
                    /*alert(XMLHttpRequest.status);
                     alert(XMLHttpRequest.readyState);
                     alert(textStatus);*/
                }
            });
        }
    });

    $('#theme_title').blur(function(){
        if($(this).val() != ''){
            $.ajax({
                url: url+"/isThemeExists",    //请求的url地址
                dataType: "json",   //返回格式为json
                async: true, //请求是否异步，默认为异步，这也是ajax重要特性
                data: { "themeTitle": $(this).val() },    //参数值
                type: "GET",   //请求方式
                success: function(data) {
                    //请求成功时处理
//                        alert(data.msg);
                    if(data.msg == true){
                        $('#themeForm').attr("onSubmit","return false");
                        $('#themeErrMsg').text('主题已存在！');
                    } else {
                        $('#themeForm').attr("onSubmit","return true");
                        $('#themeErrMsg').text('');
                    }
                },
                error: function(data) {
                    //请求出错处理
                    alert('请求发生错误！'+data.status);
                }
            });
        }
    });

    $.validator.addMethod('url',function (value, element){
        var text = /^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?$/i;
        return this.optional(element) || (text.test(value));
    }, '存在@符号')


});