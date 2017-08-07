<?php

namespace app\admin\controller;
use app\admin\model\Article;
use think\Loader;
use think\Request;
ini_set('max_execution_time', '0');
class Articles extends Base
{
    /**
     * 文章信息列表
     * @return mixed
     */

    public function index()
    {
        //得到总记录数计算出总页数
        $total = Article::GetTotal();
        $pageCount = ceil($total/10);
        $page = isset($_GET['page'])?$_GET['page']:'1';
        $result = Article::ShowArticlesByPage($page);

        $this->assign('page',$page);
        $this->assign('pageCount',$pageCount);
        $this->assign('result',$result);
        $this->assign('total',$total);
        return $this->fetch('/article-list');
    }

    /**
     * 新增文章
     * @return bool
     */
    public function addArticle()
    {
        //过滤html代码提取纯文本
        function filter($str){
            $str = strip_tags($str);
            $str = str_replace(' ','',$str);
            return $str;
        }

        //下载原图
        function DownLoadOriginal($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            $path = getcwd();

            if(!is_dir($path.'\originals'))
            {
                mkdir($path.'\originals');
            }

            //获取当前时间戳防止重名
            date_default_timezone_set('PRC');
            $name = date('YmdHis');
            $images = fopen("$path\\originals\\$name.jpg","a");
            fwrite($images, $content);
            fclose($images);
            curl_close($ch);
            return "$name.jpg";
        }
        $url = $_POST['url'];
        $type = $_POST['type'];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //装载html
        $content = curl_exec($ch);

        //删除js
        $search ="'<script[^>]*?>.*?</script>'si";
        $replace = "";
        $content = preg_replace($search,$replace,$content);

        //删除head
        $search ="'<head[^>]*?>.*?</head>'si";
        $content = preg_replace($search,$replace,$content);

        //删除防盗链
        $content = str_replace('data-src','src',$content);

        //删除&nbsp;
        $content = str_replace('&nbsp;','',$content);

        //删除留言板块
        $a = (strpos($content,'div class="ct_mpda_wrp"'));
        $b = (strpos($content,'<div/>'));
        $remove = substr($content,$a-1,$b-5);
        $content = str_replace($remove,"</html>",$content);

        //引入simple_html_dom操作类库
        Loader::import('simplehtmldom.simple_html_dom');
        //实例化simple_html_dom类库
        $dom = str_get_html($content);

        $title = filter($dom->find('h2',0));
        $date = filter($dom->find('[id=post-date]',0));
        $sourceName = filter($dom->find('[id=post-user]',0));
        $sourceNum = filter($dom->find('[class=profile_meta_value]',0));

        //保存3张图片作为预览页面的缩略图
        $pathArr=array();
        for ($i=0;$i<=2;$i++){
            sleep(1);
            //获取当前时间戳防止文件重名
            date_default_timezone_set('PRC');
            $pathArr[$i]=date('YmdHis').".jpg";
        }
        $thumbnails = implode(';',$pathArr);//数组拼接成字符串
        //内容留空待编辑
        $data = '';
        $bool = Article::addArticle($title,$date,$sourceName,$sourceNum,$url,$thumbnails,$type,$data);
        $dom->clear();
        curl_close($ch);
        if($bool){
            return $this->success('添加成功','/admin/articles');
        }else{
            return $this->error('添加失败','/admin/articles');
        }
    }

    /**
     * 删除文章
     * @return bool
     */
    public function delArticle(){
        $id  = isset($_GET['id']) ? $_GET['id'] : null;
        $info = Article::getArticleById($id)['0'];
        $delImgArr = explode(";",$info['thumbnails']);
        $jsonToArr = (json_decode($info['content']));
        $imgArr =array();
        foreach ($jsonToArr as$key=> $item){
            if ($key =="img"){
                foreach ($item as $value) {
                    if (!($value=="empty.jpg")){
                        array_push($imgArr,$value);
                    }
                }
            }
        }
        foreach ($delImgArr as $value){
            unlink("thumbnails/".$value);
        }
        foreach ($imgArr as $value){
            unlink("originals/".$value);
        }
        $bool = Article::delArticle($id);
        if($bool = true){
            return $this->success('删除成功','/admin/articles');
        }else{
            return $this->error('删除失败','/admin/articles');
        }
    }

    /**
     * 跳转到编辑文章页面，传递文章id
     */
    public function editArticle(){
        $content = input('content');
        $id = $_GET['id'];
        $result = Article::GetThumbnailsPath($id);
        $this->assign('result',$result);
        $this->assign('id',$id);
        return $this->fetch('/article-edit');
    }

    /**
     * 上传缩略图调用方法
     */
    public function uploadThumbnails(){
        //获取需要修改的缩略图的名称
        $id = $_GET['id'];
        $result = Article::GetThumbnailsPath($id);
        //转化查询结果
        $thumbStr = $result[0]['thumbnails'];
        $thumbArr = explode(';', $thumbStr);
        //获得上传的缩略图
        $file1 = request()->file('thumbnails1');
        $file2 = request()->file('thumbnails2');
        $file3 = request()->file('thumbnails3');
        $info = null;
        if($file1)$info = $file1->move(ROOT_PATH.'public'.DS.'thumbnails',$thumbArr[0]);
        if($file2)$info = $file2->move(ROOT_PATH.'public'.DS.'thumbnails',$thumbArr[1]);
        if($file3)$info = $file3->move(ROOT_PATH.'public'.DS.'thumbnails',$thumbArr[2]);
        if($info!=null){
            return $this->success('缩略图上传成功','/admin/articles');
        }else{
            return $this->error('上传失败，您未选中任何文件','/admin/articles');
        }
    }

    /**
     * 编辑文章上传
     */
    public function uploadArticle(){

        //下载图片，并以数组形式返回文件名，宽，高
        function DownLoadImage($url){
            //为保证返回的图片文件名不重复和不被微信限制操作，延迟1秒
            sleep(1);
            //构建真实文件url，删除防爬取参数
            $reStr = strrchr($url,'?');
            $reStrLength = strlen($reStr);
            $newUrl = substr($url,0,strlen($url)-$reStrLength);
            $newUrlLength = strlen($newUrl);

            //判断构造后的url是不是https的，如果是则转换为http，否则不能正确抓取图片
            if(substr($newUrl,0,5)=='https'){
                $newUrl = substr_replace($newUrl,'',4,-$newUrlLength+5);
            }
            if($newUrl=='http://m.qpic.cn/psb'||$newUrl=='http://m.qpic.cn/psu'){
                $newUrl = $url;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $newUrl);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            $path = getcwd();

            if(!is_dir($path.'\originals'))
            {
                mkdir($path.'\originals');
            }

            //获取当前时间戳防止重名
            date_default_timezone_set('PRC');
            $name = date('YmdHis');

            list($width,$height,$type,$attr) = getimagesize($newUrl);

            //getimagesize支持的图片文件类型
            $extension = array(
                1 => 'GIF',
                2 => 'JPG',
                3 => 'PNG',
                4 => 'SWF',
                5 => 'PSD',
                6 => 'BMP',
                7 => 'TIFF',
                8 => 'TIFF',
                9 => 'JPC',
                10 => 'JP2',
                11 => 'JPX',
                12 => 'JB2',
                13 => 'SWC',
                14 => 'IFF',
                15 => 'WBMP',
                16 => 'XBM'
            );
            $imageName = $name.'.'.$extension[$type];
            $image = fopen("$path\\originals\\$imageName","a");
            fwrite($image, $content);
            fclose($image);
            curl_close($ch);
            $imageInfo = array(
                0=>$imageName,
                1=>$width,
                2=>$height,
            );
            //返回图片详细信息，包括文件名，宽高
            return $imageInfo;
        }
        //去除html标签，提取纯文本
        function filter($str){
            $str = strip_tags($str);
            $str = str_replace(' ','',$str);
            return $str;
        }
        $id = $_GET['id'];
        $content = $_POST['content'];
        //删除&nbsp;
        $content = str_replace('&nbsp;','',$content);

        Loader::import('simplehtmldom.simple_html_dom');
        $dom = str_get_html($content);
        $introduce = $dom->find('p');
        //定义2个数组
        $textArr = array(
            'p'=>array(),
            'img'=>array(),
            'width'=>array(),
            'height'=>array()
        );

        $imgArr = array();
        $widthArr = array();
        $heightArr = array();

        //保存图片并将图片名称保存入$imgArr
        $contentImg = $dom->find('img');
        foreach($contentImg as $element){
            //提取图片url
            $src = $element->src;
            $imageInfo = DownLoadImage($src);

            //将本地图片文件名，图片宽，高分别追加入$imgArr，$widthArr，$heightArr中
            array_push($imgArr,$imageInfo[0]);
            array_push($widthArr,$imageInfo[1]);
            array_push($heightArr,$imageInfo[2]);
        }
        //遍历$introduce装入二维数组$textArr
        foreach ($introduce as $value){
            if(!preg_match("/<br/",$value) && !filter($value)==null || preg_match("/<img/",$value)){
                $val = filter($value);
                if ($val==null){
                    array_push($textArr['p'],$val);
                    array_push($textArr['img'],"img.jpg");
                    array_push($textArr['width'],"");
                    array_push($textArr['height'],"");
                }else {
                    array_push($textArr['img'],"empty.jpg");
                    array_push($textArr['p'], $val);
                    array_push($textArr['width'],"");
                    array_push($textArr['height'],"");
                }
            }
        }
        $i=0;
        foreach ($textArr['img'] as $key=>$value){
            if($value == "img.jpg"){
                if($i == count($imgArr)){
                    break;
                }
                $textArr['img'][$key] = $imgArr[$i];
                $textArr['width'][$key] = $widthArr[$i];
                $textArr['height'][$key] = $heightArr[$i];
                $i++;
            }
        }
        $content = json_encode($textArr);
        Article::updateContentById($id,$content);
        $dom->clear();
        return $this->success('更新成功','/admin/articles');
    }

    //layui编辑器图片上传接口
    public function lay_img_upload(){
        $file = Request::instance()->file('file');
        if(empty($file)){
            $result["code"] = "1";
            $result["msg"] = "请选择图片";
            $result['data']["src"] = '';
        }else{
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/layui' );
            if($info){
                $name_path =str_replace('\\',"/",$info->getSaveName());
                //成功上传后 获取上传信息
                $result["code"] = '0';
                $result["msg"] = "上传成功";
                $result['data']["src"] = "/uploads/layui/".$name_path;
            }else{
                // 上传失败获取错误信息
                $result["code"] = "2";
                $result["msg"] = "上传出错";
                $result['data']["src"] ='';
            }
        }
        return json_encode($result);
    }

}