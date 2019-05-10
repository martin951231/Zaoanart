<?php
/* @var $this yii\web\View */
?>
<html>
<head>
    <meta name="keywords" content="艺术，早安，早安艺术，article"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <title>早安艺术</title>
    <link href="../../web/css/index.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="../../web/css/animate.min.css">
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<div id="zong">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <img src="../../web/images/logo.png" alt="">
            </div>
            <div class="col-md-4">
                <div style="position:relative;width:240px;height:36px;margin-top:14px;border:1px solid #ccc;border-radius:6px;float:left;">
                    <div style="position:absolute;top:5px;left:10px;font-size:14px;color:#6f6c6c;">最新图片</div>
                    <div style="position:absolute;right:10px;top:5px;padding-left:10px;border-left:1px solid #ccc;">搜索</div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="nav1">
                    <img src="../../web/images/1.jpg" style="width:40%;">
                    产品
                    <ul class="xiala1">
                        <li>我的</li>
                        <li>你的额</li>
                        <li>他的</li>
                        <li>他的额</li>
                        <li>我的</li>
                        <li>你的额</li>
                        <li>他的</li>
                        <li>他的额</li>
                    </ul>
                </div>
                <div class="nav2">
                    <img src="../../web/images/2.jpg" style="width:40%;">
                    类别
                    <ul class="xiala2">
                        <li>我的2</li>
                        <li>你的额</li>
                        <li>他的</li>
                        <li>他的额</li>
                    </ul>
                </div>
                <div class="nav3">
                    <img src="../../web/images/3.jpg" style="width:30%;">
                    主题
                    <ul class="xiala3">
                        <li>我的3</li>
                        <li>你的额</li>
                        <li>他的</li>
                        <li>他的额</li>
                    </ul>
                </div>
                <div class="nav4">
                    <img src="../../web/images/4.jpg" style="width:40%;">
                    颜色
                    <ul class="xiala4">
                        <li>我的4</li>
                        <li>你的额</li>
                        <li>他的</li>
                        <li>他的额</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <!-- 轮播图开始 -->
    <div class="carousel" @mouseenter="enter" @mouseleave="leave">
        <transition-group
                tag="ul"
                name="image"
                enter-active-class="animated lightSpeedIn"
                leave-active-class="animated lightSpeedOut"
        >
            <li v-for='(image,index) in img' :key='index' v-show="index === mark">
                <a href="javascript:;">
                    <img :src="image" style="width:100%;">
                </a>
            </li>
        </transition-group>
        <div class="bullet">
                <span v-for="(item,index) in img.length" :class="{'active':index === mark}"
                      @click="change(index)" :key="index"></span>
        </div>
        <div class="switch">
            <span class="prev" @click="prev">&lt;</span>
            <span class="next" @click="next">&gt;</span>
        </div>
    </div>
</div>
<!-- 轮播图结束 -->
<!-- 推荐开始 -->
<div style="margin-top:-70px;height:311px;background:#f8f8f8;">
    <div class="title">
        <div class="xian1"></div>
        <span class="tuijian">推荐  Recommend</span>
        <div class="xian2"></div>
    </div>
    <!-- <div style="width:1200px;height:311px;margin:0 auto;">
        <div class="row">
            <div class="col-sm-6 col-md-4"  v-for='lists in list'>
              <div class="thumbnail">
                <img v-bind:src="lists.src" alt="" style="width:100%;">
              </div>
            </div>
          </div>
    </div> -->
    <div style="width:1200px;height:311px;margin:0 auto;">
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <img src="../../web/images/149283441171072998.jpg" alt="" style="width:100%;">
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <img src="../../web/images/149283441171072998.jpg" alt="" style="width:100%;">
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <img src="../../web/images/149283441171072998.jpg" alt="" style="width:100%;">
                </div>
            </div>

        </div>
    </div>

</div>
<!-- 推荐结束 -->
<!-- 开始 -->
<div style="width:1200px;height:200px;margin:0 auto;">
    <div style="padding-top:30px;">
        <div class="daoduan1"><span>[高端复制画]</span>
            <a href="#">素描水彩</a><a href="#">当代油画</a><a href="#">素描水彩</a><a href="#">当代油画</a>
            <a href="#">素描水彩</a><a href="#">当代油画</a><a href="#">素描水彩</a><a href="#">当代油画</a>
            <a href="#">素描水彩</a><a href="#">当代油画</a><a href="#">素描水彩</a><a href="#">当代油画</a>
        </div>
        <div class="daoduan2"><span>[装饰摆件]</span>
            <a href="#">雕塑</a><a href="#">陶瓷</a><a href="#">雕塑</a><a href="#">陶瓷</a>
            <a href="#">雕塑</a><a href="#">陶瓷</a><a href="#">雕塑</a><a href="#">陶瓷</a>
            <a href="#">雕塑</a><a href="#">陶瓷</a><a href="#">雕塑</a><a href="#">陶瓷</a>
            <a href="#">雕塑</a><a href="#">陶瓷</a><a href="#">雕塑</a><a href="#">陶瓷</a>
        </div>
        <div class="daoduan3"><span>[按主题分]</span>
            <a href="#">抽象</a>
            <a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a>
            <a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a>
            <a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a>
            <a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a><a href="#">抽象</a>
        </div>
    </div>
    <div>

    </div>
</div>
<!-- 结束 -->
<!-- 开始 -->
<div style="height:600px;background:#f8f8f8;">
    <div style="width:1200px;margin:0 auto;margin-top:50px;">
        <div class="title">
            <div class="xian1"></div>
            <span class="tuijian">最新  New</span>
            <div class="xian2">
            </div>
        </div>
        <div class="content">
            <div class="row">
                <div class="col-xs-6 col-md-3 jx">
                    <a href="#" class="thumbnail">
                        <img src="../../web/images/z.jpg" alt="...">
                    </a>
                    <div class="caption kg">
                        <h5>油画 19世纪荷兰风景 3</h5>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 jx">
                    <a href="#" class="thumbnail">
                        <img src="../../web/images/z.jpg" alt="...">
                    </a>
                    <div class="caption kg">
                        <h5>油画 19世纪荷兰风景 3</h5>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 jx">
                    <a href="#" class="thumbnail">
                        <img src="../../web/images/z.jpg" alt="...">
                    </a>
                    <div class="caption kg">
                        <h5>油画 19世纪荷兰风景 3</h5>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 jx">
                    <a href="#" class="thumbnail">
                        <img src="../../web/images/z.jpg" alt="...">
                    </a>
                    <div class="caption kg">
                        <h5>油画 19世纪荷兰风景 3</h5>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 jx">
                    <a href="#" class="thumbnail">
                        <img src="../../web/images/z.jpg" alt="...">
                    </a>
                    <div class="caption kg">
                        <h5>油画 19世纪荷兰风景 3</h5>
                    </div>
                </div>

                <div class="col-xs-6 col-md-3 jx">
                    <a href="#" class="thumbnail">
                        <img src="../../web/images/z.jpg" alt="...">
                    </a>
                    <div class="caption kg">
                        <h5>油画 19世纪荷兰风景 3</h5>
                    </div>
                </div>

                <div class="col-xs-6 col-md-3 jx">
                    <a href="#" class="thumbnail">
                        <img src="../../web/images/z.jpg" alt="...">
                    </a>
                    <div class="caption kg">
                        <h5>油画 19世纪荷兰风景 3</h5>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 jx">
                    <a href="#" class="thumbnail">
                        <img src="../../web/images/z.jpg" alt="...">
                    </a>
                    <div class="caption kg">
                        <h5>油画 19世纪荷兰风景 3</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- 第三部分开始 -->

<!-- 第三部分结束 -->
<div>
    <div style="width:1200px;margin:0 auto;margin-top:20px;">
        <div class="title">
            <div class="xian1"></div>
            <span class="tuijian">按类别查看  See</span>
            <div class="xian2">
            </div>
        </div>
    </div>
</div>
<!-- 结束 -->
</div>

<script>
    /*
        轮播逻辑
        效果 ==> 图片显示第几章的时候，下面的bullet的第几个就要改变成active
                 点击相应的bullet可以切换到对应的图片
    */
    //初始化一个vue实例然后挂载到父元素上，并设定数据为图片的数组，以及后面计数用的mark，mark初始值为0
    var vm = new Vue({
        el:'.carousel',
        data:{
            mark:0,
            img:[
                'images/5922b94b71204.jpg',
                'images/5922b97ef1ddc.jpg',
                'images/5922b973b8329.jpg',
                'images/5922bd5389296.jpg'
            ],
            list:[
                {src:'../../web/images/149283441171072998.jpg'},
                {src:'../../web/images/149283441171072998.jpg'},
                {src:'../../web/images/149283441171072998.jpg'}
            ],
            time:null,

        },
        methods:{   //添加方法
            change(i){
                this.mark = i;
            },
            prev(){
                this.mark--;
                if(this.mark === -1){
                    this.mark = 3;
                    return
                }
            },
            next(){
                this.mark++;
                if(this.mark === 4){
                    this.mark = 0;
                    return
                }
            },
            autoPlay(){
                this.mark++;
                if(this.mark === 4){
                    this.mark = 0;
                    return
                }
            },
            play(){
                this.time = setInterval(this.autoPlay,3000);
            },
            enter(){
                console.log('enter')
                clearInterval(this.time);
            },
            leave(){
                console.log('leave')
                this.play();
            }
        },
        created(){
            this.play()
        }
    })
</script>

<script type="text/javascript" src="../../web/js/index.js"></script>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>
